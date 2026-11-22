<p class="crumbs"><a href="?page=cabinet">Личный кабинет</a> / Обращение № <?= $id ?></p>
<p class="role"><?= e($roles[$user['role']]) ?> / <?= e($user['name']) ?></p>
<h1>Обращение № <?= $id ?></h1>
<span class="badge <?= isOverdue($a) ? 'badge-overdue' : '' ?>"><?= e($states[$a['status']] ?? $a['status']) ?></span>
<?php if (isOverdue($a)): ?><span class="badge badge-overdue">Просрочено</span><?php endif; ?>

<article>
    <h2><?= e($a['subject']) ?></h2>
    <p class="body"><?= e($a['body']) ?></p>
    <p>Категория: <?= e($a['category']) ?> · Адрес: <?= e($a['address']) ?></p>
    <p>Исполнитель: <?= e($a['executor'] ?? 'Ожидает назначения') ?> · Срок: <?= e($a['due']) ?></p>

    <div style="margin-top: 15px;">
        <?php foreach (query('SELECT id,name,mime FROM attachment WHERE appeal_id=?', [$id]) as $f): ?>
            <a href="?file=<?= $f['id'] ?>" class="file-link">
                <?php if ($f['mime'] === 'application/pdf'): ?>📄<?php else: ?>🖼️<?php endif; ?>
                <?= e($f['name']) ?>
            </a>
        <?php endforeach ?>
    </div>

    <button type="button" onclick="window.print();" style="background: #6c757d; margin-top: 15px;">🖨
        Распечатать</button>
</article>

<?php if ($a['draft'] && ($a['status'] === 'closed' || $user['role'] !== 'citizen')): ?>
    <article>
        <h2><?= $a['status'] === 'closed' ? 'Ответ заявителю' : 'Проект ответа' ?></h2>
        <p class="body"><?= e($a['draft']) ?></p>
    </article>
<?php endif ?>

<!-- Оценка работы -->
<?php if ($user['role'] === 'citizen' && $a['status'] === 'closed'): ?>
    <article>
        <h3>Оценка качества работы</h3>
        <?php if ($a['rating']): ?>
            <p>Ваша оценка: <b><?= str_repeat('★', (int) $a['rating']) ?><?= str_repeat('☆', 5 - (int) $a['rating']) ?></b></p>
            <p>Отзыв: <?= e($a['feedback']) ?></p>
        <?php else: ?>
            <form method="post" style="margin-top:10px;">
                <?= token() ?>
                <input type="hidden" name="op" value="rate">
                <input type="hidden" name="id" value="<?= $id ?>">
                <label>Оцените от 1 до 5: <input type="number" name="rating" min="1" max="5" required
                        style="width:80px; display:inline-block;"></label>
                <label>Комментарий (необязательно): <textarea name="feedback" maxlength="1000"></textarea></label>
                <button>Отправить оценку</button>
            </form>
        <?php endif; ?>
    </article>
<?php endif; ?>

<?php if ($user['role'] === 'officer' && $a['status'] === 'assigned'): ?>    <?= postForm('take') ?><button>Принять в
        работу</button></form>
<?php elseif ($user['role'] === 'officer' && $a['status'] === 'work'): ?>    <?= postForm('reply') ?><label>Проект ответа<textarea
            name="reply" required maxlength="5000"><?= e($a['draft']) ?></textarea></label><button>Передать на
        согласование</button></form><?php endif ?>

<?php if ($user['role'] === 'manager' && $a['status'] === 'review'): ?>
    <div class="grid"><?= postForm('approve') ?><button>Согласовать и направить</button></form>
        <?= postForm('revise') ?><button>Вернуть на доработку</button></form>
    </div>
<?php elseif ($user['role'] === 'manager' && $a['status'] !== 'closed'): ?>
    <?= postForm('assign') ?><label>Назначение по участку<select
            name="precinct"><?= options() ?></select></label><button>Назначить исполнителя</button></form>
<?php endif ?>

<h2>История обращения</h2>
<div class="timeline">
    <?php foreach (query('SELECT h.*,u.name FROM history h JOIN profile u ON u.id=h.actor WHERE appeal_id=? ORDER BY h.id', [$id]) as $h): ?>
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <time><?= e($h['created']) ?></time>
                <p><?= e($h['event']) ?></p>
                <small><?= e($h['name']) ?></small>
            </div>
        </div>
    <?php endforeach ?>
</div>