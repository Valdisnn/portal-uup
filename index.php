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
    if ($user['role'] !== 'manager') {
        $where[] = ($user['role'] === 'citizen' ? 'citizen' : 'officer') . '=?';
        $args[] = $user['id'];
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

if ($page === 'stats' && $user['role'] === 'manager') {
    $period = queryString('period') ?: 'all';
    $dateFrom = queryString('date_from');
    $dateTo = queryString('date_to');

    $whereDate = '1=1';
    $dateArgs = [];

    if ($period === 'month') {
        $whereDate = 'DATE(created) >= DATE_FORMAT(CURRENT_DATE, "%Y-%m-01")';
    } elseif ($period === 'quarter') {
        $whereDate = 'YEAR(created)=YEAR(CURRENT_DATE) AND QUARTER(created)=QUARTER(CURRENT_DATE)';
    } elseif ($period === 'year') {
        $whereDate = 'YEAR(created)=YEAR(CURRENT_DATE)';
    } elseif ($period === 'custom' && $dateFrom && $dateTo) {
        $whereDate = 'DATE(created) BETWEEN ? AND ?';
        $dateArgs = [$dateFrom, $dateTo];
    }

    $statsTotal = query("SELECT COUNT(*) total, COUNT(CASE WHEN status='closed' THEN 1 END) closed, COUNT(CASE WHEN status!='closed' AND due<CURRENT_DATE THEN 1 END) late FROM appeal WHERE $whereDate", $dateArgs)->fetch();

    $catStats = query("SELECT category, COUNT(*) n FROM appeal WHERE $whereDate GROUP BY category", $dateArgs)->fetchAll();
    $chartLabels = array_column($catStats, 'category');
    $chartData = array_column($catStats, 'n');

    $statusStats = query("SELECT status, COUNT(*) n FROM appeal WHERE $whereDate GROUP BY status", $dateArgs)->fetchAll();
    $statusLabels = [];
    $statusData = [];
    foreach ($statusStats as $s) {
        $statusLabels[] = $states[$s['status']];
        $statusData[] = $s['n'];
    }

    // Динамика по месяцам — последние 12 месяцев (не зависит от фильтра)
    $trendStats = query("
        SELECT DATE_FORMAT(created, '%Y-%m') AS month, COUNT(*) AS n
        FROM appeal
        WHERE created >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created, '%Y-%m')
        ORDER BY month
    ")->fetchAll();
    $trendLabels = [];
    $trendData = [];
    foreach ($trendStats as $t) {
        $trendLabels[] = date('M Y', strtotime($t['month'] . '-01'));
        $trendData[] = (int) $t['n'];
    }
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
elseif ($page === 'stats' && $user['role'] === 'manager')
    require __DIR__ . '/views/stats.php';
elseif ($page === '404')
    require __DIR__ . '/views/404.php';
else
    require __DIR__ . '/views/cabinet.php';

require __DIR__ . '/views/footer.php';
?>
</body>
</html>