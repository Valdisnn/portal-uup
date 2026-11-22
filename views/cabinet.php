<p class="role"><?= e($roles[$user['role']]) ?> / <?= e($user['name']) ?></p>
<h1><?= match ($user['role']) { 'citizen' => 'Мои обращения', 'officer' => 'Назначенные обращения', default => 'Все обращения'} ?>
</h1>

<?php if ($user['role'] === 'citizen'): ?>
    <a class="btn" href="?page=new" style="margin-bottom: 20px;">Подать обращение</a>
<?php elseif ($user['role'] === 'manager'): ?>
    <form method="post" style="margin-bottom: 20px;">
        <?= token() ?>
        <input type="hidden" name="op" value="export">
        <button class="btn" style="background: #128070;">Выгрузить в Excel</button>
    </form>
<?php endif ?>

<article>
    <form method="GET" class="search-form">
        <input type="hidden" name="page" value="cabinet">
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" name="q" placeholder="Поиск по теме или адресу..." value="<?= e($searchQuery) ?>"
                style="flex:1;">
            <select name="status" style="width:auto;">
                <option value="">Все статусы</option>
                <?php foreach ($states as $s => $name)
                    echo '<option value="' . e($s) . '" ' . ($statusFilter === $s ? 'selected' : '') . '>' . e($name) . '</option>'; ?>
            </select>
            <button type="submit">Найти</button>
        </div>
    </form>
</article>

<?php if (!$list): ?>
    <p>Обращений по выбранным условиям нет.</p>
<?php else: ?>
    <?php foreach ($list as $r): ?>
        <article class="<?= isOverdue($r) ? 'overdue' : '' ?>">
            <span class="role">№ <?= $r['id'] ?> · <?= e($states[$r['status']] ?? $r['status']) ?></span>
            <h3><a href="?id=<?= $r['id'] ?>"><?= e($r['subject']) ?></a></h3>
            <small><?= e($r['address']) ?> · Срок: <?= e($r['due']) ?></small>

            <?php if (isOverdue($r)): ?>
                <span class="badge badge-overdue">Просрочено</span>
            <?php elseif (isDueSoon($r)): ?>
                <span class="badge badge-soon">Срок истекает</span>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>

    <!-- Пагинация -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=cabinet&p=<?= $i ?>&q=<?= urlencode($searchQuery) ?>&status=<?= $statusFilter ?>"
                    class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>