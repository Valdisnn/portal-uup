<p class="crumbs"><a href="?page=cabinet">Личный кабинет</a> / Обращение № <?=$id?></p>
<p class="role"><?=e($roles[$user['role']])?> / <?=e($user['name'])?></p>
<h1>Обращение № <?=$id?></h1>

<span class="badge <?=isOverdue($a)?'badge-overdue':''?>"><?=e($states[$a['status']] ?? $a['status'])?></span>
<?php if(isOverdue($a)): ?>
    <span class="badge badge-overdue">Просрочено</span>
<?php elseif(isDueSoon($a)): ?>
    <span class="badge badge-soon">Срок истекает</span>
<?php endif; ?>

<article>
    <h2><?=e($a['subject'])?></h2>
    <p class="body"><?=e($a['body'])?></p>
    <p>Категория: <?=e($a['category'])?> · Адрес: <?=e($a['address'])?></p>
    <p>Исполнитель: <?=e($a['executor']??'Ожидает назначения')?> · Срок: <?=e($a['due'])?></p>

    <div style="margin-top: 15px;">
        <?php foreach(query('SELECT id,name,mime FROM attachment WHERE appeal_id=?',[$id]) as $f):?>
            <a href="?file=<?=$f['id']?>" class="file-link">
                <?php if($f['mime'] === 'application/pdf'): ?>📄<?php else: ?>🖼️<?php endif; ?>
                <?=e($f['name'])?>
            </a>
        <?php endforeach?>
    </div>

    <button type="button" onclick="window.print();" style="background: #6c757d; margin-top: 15px;">🖨 Распечатать</button>
</article>

<?php if($a['draft'] && ($a['status']==='closed'||$user['role']!=='citizen')):?>
    <article>
        <h2><?=$a['status']==='closed'?'Ответ заявителю':'Проект ответа'?></h2>
        <p class="body"><?=e($a['draft'])?></p>
    </article>
<?php endif?>

<!-- Оценка работы -->
<?php if($user['role']==='citizen' && $a['status']==='closed'): ?>
    <article>
        <h3>Оценка качества работы</h3>
        <?php if($a['rating']): ?>
            <p>Ваша оценка: <b><?=str_repeat('★', (int)$a['rating'])?><?=str_repeat('☆', 5-(int)$a['rating'])?></b></p>
            <p>Отзыв: <?=e($a['feedback'])?></p>
        <?php else: ?>
            <form method="post" style="margin-top:10px;">
                <?=token()?>
                <input type="hidden" name="op" value="rate">
                <input type="hidden" name="id" value="<?=$id?>">
                <label>Оцените от 1 до 5: <input type="number" name="rating" min="1" max="5" required style="width:80px; display:inline-block;"></label>
                <label>Комментарий (необязательно): <textarea name="feedback" maxlength="1000"></textarea></label>
                <button>Отправить оценку</button>
            </form>
        <?php endif; ?>
    </article>
<?php endif; ?>

<!-- Действия участкового -->
<?php if($user['role']==='officer' && $a['status']==='assigned'):?>
    <?=postForm('take')?><button>Принять в работу</button></form>
<?php elseif($user['role']==='officer' && $a['status']==='work'):?>
    <?=postForm('reply')?><label>Проект ответа<textarea name="reply" required maxlength="5000"><?=e($a['draft'])?></textarea></label><button>Передать на согласование</button></form>
<?php endif?>

<!-- Согласование начальником отдела -->
<?php if($user['role']==='department_head' && $a['status']==='department_review'):?>
    <div class="grid" style="grid-template-columns:1fr 1fr;gap:15px;background:#fff;padding:20px;border-radius:12px;border:1px solid var(--mvd-rule);margin-bottom:20px;">
        <?=postForm('approve')?><button type="submit" style="width:100%;padding:14px;font-weight:600;">Согласовать и передать руководителю</button></form>
        <?=postForm('revise')?><label>Причина возврата<textarea name="reason" required maxlength="1000"></textarea></label><button type="submit" class="btn-danger" style="width:100%;padding:14px;">Вернуть исполнителю</button></form>
    </div>
<?php endif; ?>

<!-- Финальное согласование руководителем -->
<?php if($user['role']==='manager' && $a['status']==='manager_review'):?>
    <div class="grid" style="grid-template-columns:1fr 1fr;gap:15px;background:#fff;padding:20px;border-radius:12px;border:1px solid var(--mvd-rule);margin-bottom:20px;">
        <?=postForm('approve')?><button type="submit" style="width:100%;padding:14px;font-weight:600;">Согласовать и направить заявителю</button></form>
        <?=postForm('revise')?><label>Причина возврата<textarea name="reason" required maxlength="1000"></textarea></label><button type="submit" class="btn-danger" style="width:100%;padding:14px;">Вернуть исполнителю</button></form>
    </div>
<?php elseif($user['role']==='manager' && $a['status']==='manual'):?>
    <?=postForm('assign')?>
        <label>Назначение по участку<select name="precinct" required><?=options()?></select></label>
        <button>Назначить исполнителя</button>
    </form>
<?php endif; ?>

<h2>История обращения</h2>
<div class="timeline">
    <?php foreach(query('SELECT h.*,u.name,u.role AS actor_role FROM history h JOIN profile u ON u.id=h.actor WHERE appeal_id=? ORDER BY h.id',[$id]) as $h):?>
        <?php
    // Определяем цвет и иконку в зависимости от события
    $ev = mb_strtolower($h['event']);
    $dotClass = 'dot-blue';
    $icon = '⚙️';

    if (str_contains($ev, 'зарегистрировано')) { $dotClass = 'dot-gray'; $icon = '📝'; }
    if (str_contains($ev, 'назначил') || str_contains($ev, 'назначен')) { $dotClass = 'dot-orange'; $icon = '👤'; }
    if (str_contains($ev, 'принято в работу')) { $dotClass = 'dot-blue'; $icon = '👮'; }
    if (str_contains($ev, 'ответ передан')) { $dotClass = 'dot-purple'; $icon = '📄'; }
    if (str_contains($ev, 'согласован')) { $dotClass = 'dot-green'; $icon = '✅'; }
    if (str_contains($ev, 'возвращен') || str_contains($ev, 'доработку')) { $dotClass = 'dot-red'; $icon = '↺'; }
    if (str_contains($ev, 'оценку')) { $dotClass = 'dot-gold'; $icon = '⭐'; }
    ?>
        <div class="timeline-item">
            <div class="timeline-dot <?= $dotClass ?>"><span class="timeline-icon"><?= $icon ?></span></div>
            <div class="timeline-content">
                <time><?=e($h['created'])?></time>
                <p><?=e($h['event'])?></p>
                <small><?= e(match ($h['actor_role']) { 'citizen' => 'Гражданин', 'officer' => 'Сотрудник', 'department_head' => 'Начальник отдела', 'manager' => 'Руководитель', default => 'Участник маршрута' }) ?>: <b><?=e($h['name'])?></b></small>
            </div>
        </div>
    <?php endforeach?>
</div>