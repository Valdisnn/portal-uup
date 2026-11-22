<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Moscow');

if (!defined('PORTAL_REQUEST')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/database.php';

ini_set('session.use_strict_mode', '1');
session_name('UUP' . substr(hash('sha256', __DIR__), 0, 16));
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Strict',
    'path' => rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\') . '/',
]);
session_start();

header('Cache-Control: no-store');
header("Content-Security-Policy: default-src 'self'; frame-ancestors 'none'; form-action 'self'; base-uri 'none'");
header('X-Content-Type-Options: nosniff');
header('Content-Type: text/html; charset=utf-8');

if (empty($_SESSION['token']) || !is_string($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(24));
}

try {
    $db = portalDatabase();
} catch (Throwable $failure) {
    error_log('Portal UUP: ' . $failure->getMessage());
    http_response_code(503);
    exit('<!doctype html><html lang="ru"><meta charset="utf-8"><title>Ошибка базы данных</title><link rel="stylesheet" href="style.css"><main><h1>База портала недоступна</h1><p><small>' . htmlspecialchars($failure->getMessage()) . '</small></p></main></html>');
}

// --- ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ---

function query(string $sql, array $params = []): PDOStatement
{
    global $db;
    $s = $db->prepare($sql);
    $s->execute($params);
    return $s;
}

function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function postString(string $key): ?string
{
    $v = $_POST[$key] ?? null;
    return is_string($v) ? $v : null;
}
function queryString(string $key): string
{
    $v = $_GET[$key] ?? '';
    return is_string($v) ? $v : '';
}

function intValue(mixed $v): int
{
    if (is_int($v))
        return $v;
    if (is_string($v) && preg_match('/^\d+$/', $v))
        return (int) $v;
    return 0;
}
function intPost(string $key): int
{
    return intValue($_POST[$key] ?? 0);
}
function intGet(string $key): int
{
    return intValue($_GET[$key] ?? 0);
}

function field(string $key, int $limit, bool $required = true): string
{
    $v = postString($key);
    if ($v === null)
        throw new DomainException('Некорректное значение поля.');
    $v = trim($v);
    $len = function_exists('mb_strlen') ? mb_strlen($v) : strlen($v);
    if (($required && $v === '') || $len > $limit)
        throw new DomainException('Проверьте длину и заполнение поля.');
    return $v;
}

function token(): string
{
    return '<input type="hidden" name="token" value="' . e($_SESSION['token']) . '">';
}
function go(string $url): never
{
    header("Location: $url", true, 303);
    exit;
}
function deny(): never
{
    http_response_code(403);
    exit('Доступ закрыт');
}

function setFlash(string $type, string $text): void
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}
function getFlash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function isOverdue(array $appeal): bool
{
    return $appeal['status'] !== 'closed' && strtotime($appeal['due']) < time();
}

function isDueSoon(array $appeal): bool
{
    $timeLeft = strtotime($appeal['due']) - time();
    return $appeal['status'] !== 'closed' && $timeLeft > 0 && $timeLeft < (86400 * 3);
}

function allowed(array $a): bool
{
    global $user;
    if (!$user)
        return false;
    $uid = (int) $user['id'];
    return match ($user['role']) {
        'manager' => true,
        'officer' => isset($a['officer']) && (int) $a['officer'] === $uid,
        'citizen' => isset($a['citizen']) && (int) $a['citizen'] === $uid,
        default => false,
    };
}

function event(int $id, string $message): void
{
    global $user;
    query('INSERT INTO history(appeal_id, actor, event) VALUES(?,?,?)', [$id, $user['id'], $message]);
}

// --- АВТОРИЗАЦИЯ ---
$user = null;
$sessionUid = intValue($_SESSION['uid'] ?? 0);
if ($sessionUid > 0) {
    $user = query('SELECT * FROM profile WHERE id=?', [$sessionUid])->fetch() ?: null;
}

$roles = ['citizen' => 'Гражданин', 'officer' => 'Участковый', 'manager' => 'Руководитель'];
$states = ['manual' => 'Ручное назначение', 'assigned' => 'Назначено', 'work' => 'В работе', 'review' => 'На согласовании', 'closed' => 'Ответ направлен'];
$categories = ['Шум', 'Благоустройство', 'Иное обращение'];
$error = '';

// --- АВТОМАТИЧЕСКАЯ ЭСКАЛАЦИЯ ПРОСРОЧЕННЫХ ---
if ($user && $user['role'] === 'manager') {
    $overdue = query("SELECT id FROM appeal WHERE due < CURRENT_DATE AND status IN ('assigned', 'work')")->fetchAll();
    if ($overdue) {
        foreach ($overdue as $o) {
            query("UPDATE appeal SET status='manual', officer=NULL, precinct_id=NULL WHERE id=?", [$o['id']]);
            query('INSERT INTO history(appeal_id, actor, event) VALUES(?,?,?)', [$o['id'], $user['id'], 'Срок истек. Заявка автоматически передана руководителю.']);
        }
        setFlash('error', 'Внимание: ' . count($overdue) . ' просроченных заявок передано вам на контроль.');
    }
}

// --- ОБРАБОТКА POST ЗАПРОСОВ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['token'] ?? '';
    if (!is_string($postedToken) || !hash_equals($_SESSION['token'], $postedToken))
        deny();

    $op = postString('op') ?? '';

    try {
        if ($op === 'login') {
            $login = field('login', 50);
            $password = postString('password') ?? '';

            $u = query('SELECT * FROM profile WHERE login=?', [$login])->fetch();
            if ($u && md5($password) === $u['password']) {
                session_regenerate_id(true);
                $_SESSION['uid'] = $u['id'];
                $_SESSION['token'] = bin2hex(random_bytes(24));
                go('./');
            } else {
                throw new DomainException('Неверный логин или пароль.');
            }
        }

        if ($op === 'logout') {
            session_destroy();
            go('./');
        }

        if ($op === 'acknowledge' && !$user) {
            if (postString('ack') !== '1' || postString('consent') !== '1')
                throw new DomainException('Подтвердите согласие.');
            $_SESSION['acknowledged'] = true;
            go('?page=notice');
        }

        if ($op === 'enter' && !$user && !empty($_SESSION['acknowledged'])) {
            go('login.php');
        }

        if (!$user)
            deny();

        $db->beginTransaction();
        $dest = '?page=cabinet';

        if ($op === 'create' && $user['role'] === 'citizen') {
            if (!empty($_POST['website']))
                throw new DomainException('Спам обнаружен.');

            $todayCount = query('SELECT COUNT(*) FROM appeal WHERE citizen=? AND DATE(created) = CURRENT_DATE', [$user['id']])->fetchColumn();
            if ($todayCount >= 3)
                throw new DomainException('Превышен лимит: не более 3 обращений в сутки.');

            $category = field('category', 60);
            $address = field('address', 150, false) ?: (string) ($user['residence'] ?? '');

            $p = query('SELECT p.* FROM building b JOIN precinct p ON p.id=b.precinct_id WHERE b.address=?', [$address])->fetch();
            $precinctId = $p ? (int) $p['id'] : null;
            $officerId = $p ? (int) $p['officer'] : null;
            $status = $p ? 'assigned' : 'manual';

            query(
                'INSERT INTO appeal(citizen, category, subject, body, address, precinct_id, officer, status, due) VALUES(?,?,?,?,?,?,?,?,DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY))',
                [$user['id'], $category, field('subject', 180), field('body', 5000), $address, $precinctId, $officerId, $status]
            );

            $id = (int) $db->lastInsertId();
            event($id, 'Обращение зарегистрировано');
            event($id, $p ? 'Назначен исполнитель. ' . $p['name'] : 'Передано для ручного назначения');

            $file = $_FILES['file'] ?? null;
            if (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024)
                    throw new DomainException('Файл до 5 МБ.');
                $tmp = $file['tmp_name'];
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
                if (in_array($mime, ['image/png', 'image/jpeg', 'application/pdf'], true)) {
                    $bytes = file_get_contents($tmp);
                    query('INSERT INTO attachment(appeal_id, name, mime) VALUES(?,?,?)', [$id, mb_substr(basename($file['name']), 0, 180), $mime]);
                    saveParts($db, (int) $db->lastInsertId(), $bytes);
                }
            }

            setFlash('success', 'Обращение успешно зарегистрировано!');
            $dest = '?id=' . $id;

        } elseif ($op === 'rate' && $user['role'] === 'citizen') {
            $id = intPost('id');
            $a = query('SELECT * FROM appeal WHERE id=? AND citizen=?', [$id, $user['id']])->fetch();
            if ($a && $a['status'] === 'closed' && $a['rating'] === null) {
                query('UPDATE appeal SET rating=?, feedback=? WHERE id=?', [intPost('rating'), field('feedback', 1000, false), $id]);
                setFlash('success', 'Спасибо за оценку!');
            }
            $dest = '?id=' . $id;

        } elseif ($user['role'] === 'manager') {
            if ($op === 'export') {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=appeals_export.csv');
                $output = fopen('php://output', 'w');
                fputs($output, "\xEF\xBB\xBF");
                fputcsv($output, ['ID', 'Дата', 'Тема', 'Статус', 'Адрес', 'Оценка'], ';');
                foreach (query('SELECT * FROM appeal ORDER BY id DESC') as $row) {
                    fputcsv($output, [$row['id'], $row['created'], $row['subject'], $states[$row['status']], $row['address'], $row['rating'] ?? ''], ';');
                }
                fclose($output);
                exit;
            }

            if ($op === 'publish') {
                query('INSERT INTO bulletin(title, body, author) VALUES(?,?,?)', [field('title', 180), field('body', 2500), $user['id']]);
                setFlash('success', 'Новость опубликована.');
            } elseif ($op === 'edit_news') {
                query('UPDATE bulletin SET title=?, body=? WHERE id=?', [field('title', 180), field('body', 2500), intPost('id')]);
                setFlash('success', 'Новость обновлена.');
            } elseif ($op === 'delete_news') {
                query('DELETE FROM bulletin WHERE id=?', [intPost('id')]);
                setFlash('success', 'Новость удалена.');
            } elseif (in_array($op, ['hours', 'address'], true)) {
                $p = intPost('precinct');
                if ($op === 'hours')
                    query('UPDATE precinct SET hours=? WHERE id=?', [field('hours', 100), $p]);
                else
                    query('INSERT INTO building(address, precinct_id) VALUES(?,?) ON DUPLICATE KEY UPDATE precinct_id=VALUES(precinct_id)', [field('address', 150), $p]);
                setFlash('success', 'Справочник обновлен.');
            }
            $dest = '?page=directory';

        } else {
            $id = intPost('id');
            $a = query('SELECT * FROM appeal WHERE id=? FOR UPDATE', [$id])->fetch();
            if (!$a || !allowed($a))
                deny();

            $manager = $user['role'] === 'manager';
            $officer = $user['role'] === 'officer';
            $s = $a['status'];

            if ($op === 'assign' && $manager) {
                $p = query('SELECT * FROM precinct WHERE id=?', [intPost('precinct')])->fetch();
                query("UPDATE appeal SET precinct_id=?, officer=?, status='assigned' WHERE id=?", [$p['id'], $p['officer'], $id]);
                event($id, 'Руководитель назначил исполнителя. ' . $p['name']);
                setFlash('success', 'Исполнитель назначен.');
            } elseif ($op === 'take' && $officer && $s === 'assigned') {
                query("UPDATE appeal SET status='work' WHERE id=?", [$id]);
                event($id, 'Принято в работу');
                setFlash('success', 'Обращение в работе.');
            } elseif ($op === 'reply' && $officer && $s === 'work') {
                query("UPDATE appeal SET status='review', draft=? WHERE id=?", [field('reply', 5000), $id]);
                event($id, 'Ответ передан на согласование');
                setFlash('success', 'Ответ отправлен.');
            } elseif ($op === 'approve' && $manager && $s === 'review') {
                query("UPDATE appeal SET status='closed' WHERE id=?", [$id]);
                event($id, 'Ответ согласован');
                setFlash('success', 'Согласовано.');
            } elseif ($op === 'revise' && $manager && $s === 'review') {
                query("UPDATE appeal SET status='work' WHERE id=?", [$id]);
                event($id, 'На доработку');
                setFlash('success', 'Возвращено.');
            } else {
                throw new DomainException('Действие недоступно.');
            }
            $dest = '?id=' . $id;
        }

        $db->commit();
        go($dest);
    } catch (Throwable $ex) {
        if ($db->inTransaction())
            $db->rollBack();
        $error = $ex instanceof DomainException ? $ex->getMessage() : 'Ошибка. Попробуйте снова.';
        http_response_code(422);
    }
}

// --- СКАЧИВАНИЕ ФАЙЛОВ ---
if (isset($_GET['file'])) {
    $fileId = intGet('file');
    $f = query('SELECT f.*, a.citizen, a.officer FROM attachment f JOIN appeal a ON a.id=f.appeal_id WHERE f.id=?', [$fileId])->fetch();
    if (!$f || !allowed($f))
        deny();

    header('Content-Type: ' . $f['mime']);
    header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($f['name']));
    $parts = query('SELECT content FROM attachment_part WHERE attachment_id=? ORDER BY part_no', [$fileId]);
    while ($part = $parts->fetch(PDO::FETCH_ASSOC))
        echo $part['content'];
    exit;
}

// --- ПОДГОТОВКА ДАННЫХ ---
$id = intGet('id');
$a = null;
if ($id > 0) {
    $a = query('SELECT a.*, p.name AS executor FROM appeal a LEFT JOIN profile p ON p.id=a.officer WHERE a.id=?', [$id])->fetch();
    if (!$a || !allowed($a))
        deny();
}

function postForm(string $op): string
{
    global $id;
    return '<form method="post">' . token() . '<input type="hidden" name="op" value="' . e($op) . '"><input type="hidden" name="id" value="' . (int) $id . '">';
}

function options(): string
{
    $r = '';
    foreach (query('SELECT * FROM precinct ORDER BY id') as $p) {
        $r .= '<option value="' . (int) $p['id'] . '">' . e($p['name']) . '</option>';
    }
    return $r;
}