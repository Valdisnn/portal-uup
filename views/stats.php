<h1>Контроль исполнения</h1>
<div class="grid">
    <div class="box">Всего обращений<h1><?= $statsTotal['total'] ?></h1>
    </div>
    <div class="box">Ответ направлен<h1><?= $statsTotal['closed'] ?></h1>
    </div>
    <div class="box" style="border-color:#C41E3A;">Просрочено<h1 style="color:#C41E3A;"><?= $statsTotal['late'] ?></h1>
    </div>
</div>

<div class="grid" style="grid-template-columns: 1fr 1fr; margin-top: 20px;">
    <article style="padding:20px;">
        <h2>Распределение по категориям</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="myChart"></canvas>
        </div>
    </article>

    <article style="padding:20px;">
        <h2>Статусы обращений</h2>
        <div style="height: 300px; position: relative;">
            <canvas id="statusChart"></canvas>
        </div>
    </article>
</div>