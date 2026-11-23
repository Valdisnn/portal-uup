<p class="crumbs"><a href="?page=cabinet">Личный кабинет</a> / Контроль исполнения</p>
<p class="role"><?= e($roles[$user['role']]) ?> / <?= e($user['name']) ?></p>
<h1>Контроль исполнения обращений</h1>

<!-- Фильтр по периоду -->
<form method="GET" class="box" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
    <input type="hidden" name="page" value="stats">
    <div style="flex:1; min-width:160px;">
        <label style="font-size:13px; margin-bottom:4px; display:block;">Период</label>
        <select name="period" id="periodSelect" style="width:100%;">
            <option value="all" <?= $period==='all'?'selected':'' ?>>Всё время</option>
            <option value="month" <?= $period==='month'?'selected':'' ?>>Текущий месяц</option>
            <option value="quarter" <?= $period==='quarter'?'selected':'' ?>>Текущий квартал</option>
            <option value="year" <?= $period==='year'?'selected':'' ?>>Текущий год</option>
            <option value="custom" <?= $period==='custom'?'selected':'' ?>>Свой диапазон</option>
        </select>
    </div>
    <div id="dateRangeBlock" style="display:none; flex:2; gap:12px; flex-wrap:wrap;">
        <div style="flex:1; min-width:160px;">
            <label style="font-size:13px; margin-bottom:4px; display:block;">С даты</label>
            <input type="date" name="date_from" value="<?= e($dateFrom) ?>" style="width:100%;">
        </div>
        <div style="flex:1; min-width:160px;">
            <label style="font-size:13px; margin-bottom:4px; display:block;">По дату</label>
            <input type="date" name="date_to" value="<?= e($dateTo) ?>" style="width:100%;">
        </div>
    </div>
    <button type="submit" style="align-self:flex-end;">Применить</button>
    <a href="?page=stats" style="align-self:flex-end; padding:10px 17px;">Сбросить</a>
</form>

<script>
(function() {
    var sel = document.getElementById('periodSelect');
    var block = document.getElementById('dateRangeBlock');
    function toggle() {
        block.style.display = sel.value === 'custom' ? 'flex' : 'none';
    }
    sel.addEventListener('change', toggle);
    toggle();
})();
</script>

<!-- Карточки статистики -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $statsTotal['total'] ?></div>
        <div class="stat-label">Всего обращений</div>
    </div>
    <div class="stat-card stat-success">
        <div class="stat-value"><?= $statsTotal['closed'] ?></div>
        <div class="stat-label">Ответ направлен</div>
    </div>
    <div class="stat-card stat-danger">
        <div class="stat-value"><?= $statsTotal['late'] ?></div>
        <div class="stat-label">Просрочено</div>
    </div>
    <div class="stat-card stat-info">
        <div class="stat-value"><?= $statsTotal['total'] > 0 ? round(($statsTotal['closed'] / $statsTotal['total']) * 100) : 0 ?>%</div>
        <div class="stat-label">Эффективность</div>
    </div>
</div>

<!-- Графики -->
<div class="charts-grid">
    <div class="chart-box">
        <h3>Распределение по категориям</h3>
        <div class="chart-container">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
    <div class="chart-box">
        <h3>Статусы обращений</h3>
        <div class="chart-container">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<div class="chart-box" style="margin-top:24px;">
    <h3>Динамика обращений (последние 12 месяцев)</h3>
    <div class="chart-container" style="height:350px;">
        <canvas id="trendChart"></canvas>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 24px 0;
}
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-left: 4px solid var(--mvd-blue);
}
.stat-card.stat-success { border-left-color: #128070; }
.stat-card.stat-danger { border-left-color: #c41e3a; }
.stat-card.stat-info { border-left-color: #ffc107; }
.stat-value {
    font-size: 36px;
    font-weight: 800;
    color: var(--mvd-dark);
    margin-bottom: 8px;
}
.stat-label {
    font-size: 13px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
}
.chart-box {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid var(--mvd-rule);
}
.chart-box h3 {
    margin: 0 0 20px 0;
    color: var(--mvd-dark);
    font-size: 18px;
}
.chart-container {
    position: relative;
    height: 300px;
}
</style>