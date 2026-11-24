<?php
declare(strict_types=1);
$host = parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST);
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) || !in_array(strtolower((string) $host), ['localhost', '127.0.0.1', '[::1]'], true)) {
    http_response_code(403);
    exit('Доступ через localhost.');
}
define('PORTAL_REQUEST', true);
require __DIR__ . '/app.php';

$page = queryString('page') ?: 'cabinet';
$statusFilter = queryString('status');
$searchQuery = trim(queryString('q'));
$currentPage = max(1, intGet('p'));
$limit = 5;
$offset = ($currentPage - 1) * $limit;
$list = [];
$totalPages = 1;

if (!$user) {
    require __DIR__ . '/views/entry.php';
    exit;
}

// Обработка несуществующих страниц
$knownPages = ['cabinet', 'directory', 'profile', 'stats', 'new', 'notice', '404'];
if (!in_array($page, $knownPages, true) && !$a) {
    http_response_code(404);
    $page = '404';
}

if ($page === 'cabinet') {
    $where = ['1=1'];
    $args = [];
    if ($user['role'] === 'citizen') {
        $where[] = 'citizen=?'; $args[] = $user['id'];
    } elseif ($user['role'] === 'officer') {
        $where[] = 'officer=?'; $args[] = $user['id'];
    } elseif ($user['role'] === 'department_head') {
        $where[] = "precinct_id IN (SELECT up.id FROM precinct up JOIN precinct d ON d.district=up.district AND d.is_department=1 WHERE d.id=? AND up.is_department=0)";
        $args[] = (int)$user['department_id'];
    }
    if ($statusFilter !== '' && isset($states[$statusFilter])) {
        $where[] = 'status=?';
        $args[] = $statusFilter;
    }
    if ($searchQuery !== '') {
        $where[] = '(subject LIKE ? OR address LIKE ?)';
        $args[] = "%{$searchQuery}%";
        $args[] = "%{$searchQuery}%";
    }
    $whereSql = implode(' AND ', $where);
    $totalAppeals = query("SELECT COUNT(*) FROM appeal WHERE $whereSql", $args)->fetchColumn();
    $totalPages = ceil($totalAppeals / $limit);
    if ($totalPages < 1) $totalPages = 1;
    $list = query("SELECT * FROM appeal WHERE $whereSql ORDER BY due, id DESC LIMIT $limit OFFSET $offset", $args)->fetchAll();
}

if ($page === 'stats' && in_array($user['role'], ['manager', 'department_head'], true)) {
    $period = queryString('period') ?: 'all';
    $dateFrom = queryString('date_from');
    $dateTo = queryString('date_to');

    $whereDate = '1=1';
    $dateArgs = [];
    // Руководитель видит всю статистику, начальник отдела — только свой отдел.
    $departmentScope = '';
    $scopeArgs = [];
    if ($user['role'] === 'department_head') {
        $departmentId = (int)($user['department_id'] ?? 0);
        if ($departmentId <= 0) {
            throw new RuntimeException('Для учётной записи начальника отдела не указан отдел.');
        }
        $departmentScope = " AND EXISTS (
            SELECT 1 FROM precinct up
            JOIN precinct dep ON dep.district = up.district AND dep.is_department = 1
            WHERE up.id = a.precinct_id AND up.is_department = 0 AND dep.id = ?
        )";
        $scopeArgs[] = $departmentId;
    }
    if ($period === 'month') {
        $whereDate = 'created >= DATE_FORMAT(CURRENT_DATE, "%Y-%m-01")';
    } elseif ($period === 'quarter') {
        $whereDate = 'YEAR(created)=YEAR(CURRENT_DATE) AND QUARTER(created)=QUARTER(CURRENT_DATE)';
    } elseif ($period === 'year') {
        $whereDate = 'YEAR(created)=YEAR(CURRENT_DATE)';
    } elseif ($period === 'custom') {
        if ($dateFrom !== '' && $dateTo !== '' && $dateFrom <= $dateTo) {
            $whereDate = 'DATE(created) BETWEEN ? AND ?';
            $dateArgs = [$dateFrom, $dateTo];
        } else {
            $period = 'all';
            $dateFrom = $dateTo = '';
        }
    }

    $statsTotal = query("SELECT
        COUNT(*) total,
        SUM(status='closed') closed,
        SUM(status<>'closed') active,
        SUM(status<>'closed' AND due<CURRENT_DATE) late,
        SUM(status='manual') manual_count,
        SUM(status='assigned') assigned_count,
        SUM(status='work') work_count,
        SUM(status IN ('review','department_review','manager_review')) review_count,
        SUM(status='closed' AND due >= DATE(COALESCE(
            (SELECT MIN(h.created) FROM history h
             WHERE h.appeal_id=a.id AND h.event LIKE '%согласован и направлен заявителю%'),
            a.created))) closed_in_due
        FROM appeal a WHERE $whereDate $departmentScope", array_merge($dateArgs, $scopeArgs))->fetch();

    $catStats = query("SELECT a.category, COUNT(*) n FROM appeal a WHERE $whereDate $departmentScope GROUP BY a.category ORDER BY n DESC", array_merge($dateArgs, $scopeArgs))->fetchAll();
    $chartLabels = array_column($catStats, 'category');
    $chartData = array_map('intval', array_column($catStats, 'n'));

    $statusStats = query("SELECT a.status, COUNT(*) n FROM appeal a WHERE $whereDate $departmentScope GROUP BY a.status ORDER BY n DESC", array_merge($dateArgs, $scopeArgs))->fetchAll();
    $statusLabels = [];
    $statusData = [];
    foreach ($statusStats as $st) {
        $statusLabels[] = $states[$st['status']] ?? $st['status'];
        $statusData[] = (int)$st['n'];
    }

    $trendStats = query("SELECT DATE_FORMAT(created, '%Y-%m') month, COUNT(*) n
        FROM appeal a WHERE a.created >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH) $departmentScope
        GROUP BY DATE_FORMAT(a.created, '%Y-%m') ORDER BY month", $scopeArgs)->fetchAll();
    $trendLabels = [];
    $trendData = [];
    foreach ($trendStats as $t) {
        $trendLabels[] = date('m.Y', strtotime($t['month'] . '-01'));
        $trendData[] = (int)$t['n'];
    }

    $averageDays = query("SELECT AVG(GREATEST(DATEDIFF(COALESCE(
        (SELECT MAX(h.created) FROM history h WHERE h.appeal_id=a.id AND h.event LIKE '%согласован%'),
        IF(a.status='closed', a.created, NULL)), a.created),0))
        FROM appeal a WHERE $whereDate $departmentScope", array_merge($dateArgs, $scopeArgs))->fetchColumn();

    $categoryTable = $catStats;
    $departmentTable = query("SELECT p.district, COUNT(a.id) total,
        SUM(a.status='closed') closed, SUM(a.status<>'closed') active,
        SUM(a.status<>'closed' AND a.due<CURRENT_DATE) late
        FROM appeal a LEFT JOIN precinct p ON p.id=a.precinct_id
        WHERE $whereDate $departmentScope GROUP BY p.district ORDER BY total DESC", array_merge($dateArgs, $scopeArgs))->fetchAll();
}

if ($page === 'directory') {
    $mapPrecincts = query('SELECT id, name, address, phone, hours, lat, lng FROM precinct WHERE lat IS NOT NULL')->fetchAll();
}
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Портал участковых | Нижний Новгород</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php require __DIR__ . '/views/header.php'; ?>
<main>
<?php
if ($page === 'directory')
    require __DIR__ . '/views/directory.php';
elseif ($page === 'profile')
    require __DIR__ . '/views/profile.php';
elseif ($a)
    require __DIR__ . '/views/appeal.php';
elseif ($page === 'new' && $user['role'] === 'citizen')
    require __DIR__ . '/views/create.php';
elseif ($page === 'stats' && in_array($user['role'], ['manager', 'department_head'], true))
    require __DIR__ . '/views/stats.php';
elseif ($page === '404')
    require __DIR__ . '/views/404.php';
else
    require __DIR__ . '/views/cabinet.php';

require __DIR__ . '/views/footer.php';
?>
</body>
</html>