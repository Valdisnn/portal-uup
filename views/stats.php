<p class="crumbs"><a href="?page=cabinet">Личный кабинет</a> / Статистика</p>
<p class="role"><?= e($roles[$user['role']]) ?> / <?= e($user['name']) ?></p>
<h1>Статистика обращений</h1>

<section class="stats-filter box" aria-label="Фильтр статистики">
    <form method="GET" class="stats-filter__form">
        <input type="hidden" name="page" value="stats">
        <div class="stats-filter__period">
            <label for="periodSelect">Период статистики</label>
            <select name="period" id="periodSelect">
                <option value="all" <?= $period==='all'?'selected':'' ?>>Всё время</option>
                <option value="month" <?= $period==='month'?'selected':'' ?>>Текущий месяц</option>
                <option value="quarter" <?= $period==='quarter'?'selected':'' ?>>Текущий квартал</option>
                <option value="year" <?= $period==='year'?'selected':'' ?>>Текущий год</option>
                <option value="custom" <?= $period==='custom'?'selected':'' ?>>Произвольный диапазон</option>
            </select>
        </div>
        <div id="dateRangeBlock" class="stats-filter__dates">
            <div><label for="dateFrom">С даты</label><input id="dateFrom" type="date" name="date_from" value="<?= e($dateFrom) ?>"></div>
            <div><label for="dateTo">По дату</label><input id="dateTo" type="date" name="date_to" value="<?= e($dateTo) ?>"></div>
        </div>
        <div class="stats-filter__actions">
            <button type="submit">Применить</button>
            <a class="stats-reset" href="?page=stats">Сбросить</a>
        </div>
    </form>
</section>

<div class="stats-toolbar">
    <div class="stats-toolbar__caption">Показатели за выбранный период</div>
    <button type="button" class="btn-print" id="printStatsButton">Распечатать статистику</button>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-value"><?= (int)$statsTotal['total'] ?></div><div class="stat-label">Всего поступило</div></div>
    <div class="stat-card stat-success"><div class="stat-value"><?= (int)$statsTotal['closed'] ?></div><div class="stat-label">Завершено</div></div>
    <div class="stat-card stat-info"><div class="stat-value"><?= (int)$statsTotal['active'] ?></div><div class="stat-label">В работе</div></div>
    <div class="stat-card stat-danger"><div class="stat-value"><?= (int)$statsTotal['late'] ?></div><div class="stat-label">Просрочено сейчас</div></div>
</div>

<section class="stats-section">
    <h2>Сводные показатели</h2>
    <div class="stats-table-wrap">
        <table class="stats-table">
            <thead><tr><th>Показатель</th><th class="numeric">Значение</th><th>Пояснение</th></tr></thead>
            <tbody>
                <tr><td>Всего зарегистрировано</td><td class="numeric"><?= (int)$statsTotal['total'] ?></td><td>Все обращения, созданные в выбранном периоде</td></tr>
                <tr><td>Завершено</td><td class="numeric"><?= (int)$statsTotal['closed'] ?></td><td>Статус «Ответ направлен»</td></tr>
                <tr><td>На текущем исполнении</td><td class="numeric"><?= (int)$statsTotal['active'] ?></td><td>Все незакрытые обращения</td></tr>
                <tr><td>Ожидают назначения</td><td class="numeric"><?= (int)$statsTotal['manual_count'] ?></td><td>Статус «Не назначено»</td></tr>
                <tr><td>Назначено исполнителю</td><td class="numeric"><?= (int)$statsTotal['assigned_count'] ?></td><td>Статус «Назначено»</td></tr>
                <tr><td>В работе у исполнителя</td><td class="numeric"><?= (int)$statsTotal['work_count'] ?></td><td>Статус «В работе»</td></tr>
                <tr><td>На рассмотрении / согласовании</td><td class="numeric"><?= (int)$statsTotal['review_count'] ?></td><td>Статус «На согласовании»</td></tr>
                <tr><td>Просрочено на текущую дату</td><td class="numeric"><?= (int)$statsTotal['late'] ?></td><td>Незакрытые обращения с истёкшим сроком</td></tr>
                <tr><td>Завершено не позднее срока</td><td class="numeric"><?= (int)$statsTotal['closed_in_due'] ?></td><td>Закрытые обращения, срок которых ещё не истёк</td></tr>
                <tr><td>Доля завершённых от поступивших</td><td class="numeric"><?= $statsTotal['total'] ? number_format($statsTotal['closed'] / $statsTotal['total'] * 100, 1, ',', ' ') . '%' : '0,0%' ?></td><td>Завершено / всего поступило</td></tr>
                <tr><td>Средняя длительность до согласования</td><td class="numeric"><?= $averageDays !== null ? number_format((float)$averageDays, 1, ',', ' ') . ' дн.' : '—' ?></td><td>Оценка по событиям истории согласования; при отсутствии события дата завершения не учитывается</td></tr>
            </tbody>
        </table>
    </div>
    <p class="stats-note">Примечание: показатель «Просрочено» рассчитывается на дату просмотра. Доля завершённых показывает отношение завершённых обращений к поступившим за период, а не долю исполнения в срок.</p>
</section>

<section class="stats-section">
    <h2>Распределение по категориям и отделам</h2>
    <div class="charts-grid">
        <div class="chart-box"><h3>По категориям</h3><div class="chart-container"><canvas id="categoryChart"></canvas></div></div>
        <div class="chart-box"><h3>По статусам</h3><div class="chart-container"><canvas id="statusChart"></canvas></div></div>
    </div>
    <div class="chart-box stats-trend"><h3>Динамика поступления за последние 12 месяцев</h3><div class="chart-container"><canvas id="trendChart"></canvas></div></div>
</section>

<section class="stats-section">
    <h2>Показатели по категориям</h2>
    <div class="stats-table-wrap"><table class="stats-table">
        <thead><tr><th>Категория</th><th class="numeric">Обращений</th><th class="numeric">Доля</th></tr></thead>
        <tbody>
        <?php if (!$categoryTable): ?><tr><td colspan="3">Нет данных за выбранный период.</td></tr><?php endif ?>
        <?php foreach ($categoryTable as $row): ?>
            <tr><td><?= e($row['category']) ?></td><td class="numeric"><?= (int)$row['n'] ?></td><td class="numeric"><?= $statsTotal['total'] ? number_format($row['n'] / $statsTotal['total'] * 100, 1, ',', ' ') . '%' : '0,0%' ?></td></tr>
        <?php endforeach ?>
        </tbody>
    </table></div>
</section>

<section class="stats-section">
    <h2>Показатели по районам / отделам</h2>
    <div class="stats-table-wrap"><table class="stats-table">
        <thead><tr><th>Район</th><th class="numeric">Поступило</th><th class="numeric">Завершено</th><th class="numeric">В работе</th><th class="numeric">Просрочено</th><th class="numeric">Завершено, %</th></tr></thead>
        <tbody>
        <?php if (!$departmentTable): ?><tr><td colspan="6">Нет данных за выбранный период.</td></tr><?php endif ?>
        <?php foreach ($departmentTable as $row): ?>
            <tr><td><?= e($row['district'] ?: 'Не определён') ?></td><td class="numeric"><?= (int)$row['total'] ?></td><td class="numeric"><?= (int)$row['closed'] ?></td><td class="numeric"><?= (int)$row['active'] ?></td><td class="numeric"><?= (int)$row['late'] ?></td><td class="numeric"><?= $row['total'] ? number_format($row['closed'] / $row['total'] * 100, 1, ',', ' ') . '%' : '0,0%' ?></td></tr>
        <?php endforeach ?>
        </tbody>
    </table></div>
</section>

<section class="stats-section export-panel">
    <h2>Выгрузка полного реестра в Excel</h2>
    <p>Выгрузка содержит карточки обращений и маршрут их исполнения. Укажите обе даты, чтобы выгрузить только обращения, зарегистрированные в выбранном диапазоне. Если даты не указаны, выгружается вся база.</p>
    <form method="post" class="export-form">
        <?= token() ?>
        <input type="hidden" name="op" value="export">
        <div><label for="exportFrom">С даты регистрации</label><input type="date" id="exportFrom" name="date_from" value="<?= e($dateFrom) ?>"></div>
        <div><label for="exportTo">По дату регистрации</label><input type="date" id="exportTo" name="date_to" value="<?= e($dateTo) ?>"></div>
        <button type="submit" class="export-button">Выгрузить Excel (CSV)</button>
    </form>
</section>

<script nonce="<?= e($cspNonce) ?>">
(function () {
    const printButton = document.getElementById("printStatsButton");
    if (printButton) printButton.addEventListener("click", () => window.print());
    const select = document.getElementById('periodSelect');
    const range = document.getElementById('dateRangeBlock');
    function updateRange() {
        range.classList.toggle('is-visible', select.value === 'custom');
    }
    select.addEventListener('change', updateRange);
    updateRange();
})();
</script>

<style>
.stats-filter { margin: 18px 0 24px; padding: 22px 24px; }
.stats-filter__form { display:flex; gap:14px; align-items:end; flex-wrap:wrap; }
.stats-filter__period { flex:0 1 245px; min-width:210px; }
.stats-filter__dates { flex:1 1 300px; }
.stats-filter label, .export-form label { display:block; margin:0 0 7px; font-size:14px; font-weight:600; color:#243247; }
.stats-filter select, .stats-filter input, .export-form input { min-height:48px; }
.stats-filter__dates { display:none; grid-template-columns:repeat(2,minmax(130px,1fr)); gap:12px; }
.stats-filter__dates.is-visible { display:grid; }
.stats-filter__actions { display:flex; gap:14px; align-items:center; padding-bottom:1px; white-space:nowrap; }
.stats-reset { padding:10px 4px; }
.stats-toolbar { display:flex; justify-content:space-between; align-items:center; gap:16px; margin:22px 0 12px; }
.stats-toolbar__caption { color:#46566b; font-weight:600; }
.btn-print { background:#fff; color:#075b9c; border:1px solid #b7cbd9; }
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:16px; margin:14px 0 26px; }
.stat-card { background:#fff; border:1px solid #dbe3eb; border-radius:10px; padding:20px; border-left:4px solid var(--mvd-blue); }
.stat-card.stat-success { border-left-color:#128070; }
.stat-card.stat-danger { border-left-color:#c41e3a; }
.stat-card.stat-info { border-left-color:#d49b18; }
.stat-value { font-size:34px; font-weight:800; color:var(--mvd-dark); line-height:1.15; margin-bottom:7px; }
.stat-label { color:#566579; font-size:13px; }
.stats-section { margin:28px 0; }
.stats-section h2 { margin:0 0 14px; font-size:22px; color:#25344a; }
.stats-table-wrap { overflow-x:auto; background:#fff; border:1px solid #dce4ec; border-radius:10px; }
.stats-table { display:table; width:100%; border-collapse:collapse; }
.stats-table th { background:#f1f5f9; color:#344256; font-weight:700; font-size:13px; }
.stats-table td, .stats-table th { padding:11px 14px; border-bottom:1px solid #e5ebf1; }
.stats-table tbody tr:nth-child(even) { background:#fafbfd; }
.stats-table tbody tr:last-child td { border-bottom:0; }
.stats-table .numeric { text-align:right; font-variant-numeric:tabular-nums; white-space:nowrap; }
.stats-note { color:#64748b; font-size:13px; margin:9px 2px; }
.charts-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:18px; }
.chart-box { background:#fff; border:1px solid #dce4ec; border-radius:10px; padding:20px; break-inside:avoid; }
.chart-box h3 { margin:0 0 15px; font-size:17px; color:#25344a; }
.chart-container { position:relative; height:290px; }
.stats-trend { margin-top:18px; }
.export-panel { background:#fff; border:1px solid #dce4ec; border-radius:10px; padding:22px; }
.export-panel p { color:#5b6878; margin:0 0 16px; }
.export-form { display:flex; align-items:end; gap:14px; flex-wrap:wrap; }
.export-form > div { min-width:190px; flex:1; }
.export-button { background:#128070; }
@media(max-width:760px) {
 .stats-filter__form { display:flex; flex-direction:column; align-items:stretch; }
 .stats-filter__dates { grid-template-columns:1fr 1fr; }
 .stats-toolbar { align-items:flex-start; flex-direction:column; }
}
@media print {
 @page { size:A4 landscape; margin:12mm; }
 body { background:#fff !important; color:#111 !important; display:block; }
 header, footer, .stats-filter, .stats-toolbar button, .export-panel, .crumbs { display:none !important; }
 main { max-width:none; margin:0; padding:0; }
 h1 { font-size:24px; margin:0 0 12px; }
 .role { margin-bottom:12px; }
 .stats-section { break-inside:avoid; margin:14px 0; }
 .stats-table-wrap, .chart-box, .stat-card { box-shadow:none !important; border-color:#bbb !important; }
 .stats-table th { background:#eee !important; print-color-adjust:exact; -webkit-print-color-adjust:exact; }
 .charts-grid { grid-template-columns:1fr 1fr; }
 .chart-container { height:220px; }
 .stats-trend { break-before:page; }
}
</style>
