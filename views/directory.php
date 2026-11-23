<h1>Участки и прием</h1>
<p>Участковые пункты полиции города Нижнего Новгорода и области.</p>

<!-- КАРТА -->
<div id="map" style="height: 400px; width: 100%; margin-bottom: 30px; border-radius: 8px; z-index: 1;"></div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .leaflet-attribution-flag, .leaflet-control-attribution { display: none !important; }
    details.district-group {
        margin-bottom: 20px;
        background: #fff;
        border: 1px solid var(--mvd-rule);
        border-radius: 8px;
        padding: 0;
    }
    details.district-group summary {
        font-size: 20px;
        font-weight: 700;
        cursor: pointer;
        padding: 15px 20px;
        color: var(--mvd-blue);
        outline: none;
        user-select: none;
    }
    details.district-group[open] summary {
        border-bottom: 2px solid var(--mvd-rule);
        margin-bottom: 20px;
    }
    .dept-card { border-left: 4px solid var(--mvd-red) !important; background: #fdfbfb; }
    .grid-card { margin: 0; display: flex; flex-direction: column; justify-content: space-between; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var map = L.map('map').setView([56.326, 44.005], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
        var precincts = <?= json_encode($mapPrecincts ?? [], JSON_UNESCAPED_UNICODE) ?>;
        precincts.forEach(function (p) {
            if (p.lat && p.lng) {
                var marker = L.marker([p.lat, p.lng]).addTo(map);
                marker.bindPopup('<b>' + p.name + '</b><br>' + (p.address || '') + '<br>Тел: ' + (p.phone || 'нет') + '<br>Прием: ' + p.hours);
            }
        });
    });
</script>

<!-- СПИСОК УЧАСТКОВ -->
<?php
// LEFT JOIN, чтобы участки без участковых тоже отображались
$precincts = query('SELECT p.*, u.name person FROM precinct p LEFT JOIN profile u ON u.id=p.officer ORDER BY p.is_department DESC, p.district, p.name')->fetchAll();
$grouped = [];
foreach ($precincts as $p) {
    $grouped[$p['district']][] = $p;
}
?>

<div class="district-nav" style="margin-bottom: 30px;">
    <?php foreach ($grouped as $district => $items): ?>
        <a href="#<?= md5($district) ?>"><?= e($district) ?> (<?= count($items) ?>)</a>
    <?php endforeach; ?>
</div>

<?php foreach ($grouped as $district => $items): ?>
    <details class="district-group" id="<?= md5($district) ?>">
        <summary><?= e($district) ?> <span class="muted" style="font-size: 14px; font-weight: normal;">(всего: <?= count($items) ?>)</span></summary>

        <div class="grid" style="padding: 0 20px 20px 20px;">
            <?php foreach ($items as $p): ?>
                <article class="grid-card <?= $p['is_department'] ? 'dept-card' : '' ?>">
                    <div>
                        <p class="role" style="margin-top:0;"><?= e($p['name']) ?></p>

                        <?php if (!empty($p['person'])): ?>
                            <!-- Если участковый есть (для УПП) -->
                            <p style="margin-bottom: 5px;"><strong><?= e($p['person']) ?></strong></p>
                        <?php else: ?>
                            <!-- Если участкового нет (или это Отдел полиции) —
                                 пустая строка для выравнивания карточек по высоте -->
                            <p style="margin-bottom: 5px;">&nbsp;</p>
                        <?php endif; ?>

                    </div>
                    <div style="font-size: 14px; color: #444;">
                        <?php if (!empty($p['hours'])): ?>
                            <div>Часы приема: <?= e($p['hours']) ?></div>
                        <?php endif; ?>
                        <div>Телефон: <?= e($p['phone'] ?: 'нет данных') ?></div>
                        <div>Адрес: <?= e($p['address'] ?: 'нет данных') ?></div>
                    </div>
                </article>
            <?php endforeach ?>
        </div>
    </details>
<?php endforeach ?>

<?php if (($user['role'] ?? '') === 'manager'): ?>
    <!-- Управление справочником -->
    <h2 style="margin-top: 40px; border-bottom: 2px solid var(--mvd-rule); padding-bottom: 10px;">Управление привязкой домов</h2>

    <article style="border-left: 4px solid var(--mvd-blue);">
        <h3>Привязать дом к участку</h3>
        <p class="muted" style="font-size: 14px;">Если дом не привязан к УПП, при создании обращения система не сможет автоматически назначить участкового (обращение уйдет в «Ручное назначение»).</p>
        <form method="post" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <?= token() ?>
            <input type="hidden" name="op" value="address">
            <div style="flex: 1; min-width: 250px;">
                <label>Выберите участок (УПП или Отдел)
                    <select name="precinct"><?= options() ?></select>
                </label>
            </div>
            <div style="flex: 1; min-width: 250px;">
                <label>Адрес дома
                    <input type="text" name="address" required placeholder="Нижний Новгород, ул. Новая, д. 1">
                </label>
            </div>
            <button type="submit" style="margin-bottom: 12px;">Привязать адрес</button>
        </form>
    </article>

    <!-- Управление новостями -->
    <h2 style="margin-top: 40px;">Сообщения и отчеты участков</h2>
    <?php
    $news = query('SELECT * FROM bulletin ORDER BY id DESC')->fetchAll();
    if (!$news) echo '<p>Публикаций пока нет.</p>';
    foreach ($news as $n): ?>
        <article style="border-left: 4px solid var(--mvd-blue);">
            <time><?= e($n['created']) ?></time>
            <h3><?= e($n['title']) ?></h3>
            <p class="body"><?= e($n['body']) ?></p>
            <div style="margin-top: 10px; display: flex; gap: 10px;">
                <button onclick="editNews(<?= $n['id'] ?>, '<?= e(addslashes($n['title'])) ?>', '<?= e(addslashes($n['body'])) ?>')" class="btn" style="background: #ffc107; color: #000; padding: 5px 10px; font-size: 13px;">✏️ Редактировать</button>
                <form method="post" style="display:inline;">
                    <?= token() ?><input type="hidden" name="op" value="delete_news"><input type="hidden" name="id" value="<?= $n['id'] ?>">
                    <button class="btn" style="background: #dc3545; padding: 5px 10px; font-size: 13px;" onclick="return confirm('Удалить новость?');">🗑️ Удалить</button>
                </form>
            </div>
        </article>
    <?php endforeach ?>

    <!-- Форма добавления/редактирования -->
    <article id="news-form-container">
        <?= postForm('publish') ?>
        <input type="hidden" name="op" id="news-op" value="publish">
        <input type="hidden" name="id" id="news-id" value="0">
        <h2 id="news-form-title">Опубликовать новость</h2>
        <label>Заголовок<input type="text" name="title" id="news-title" required maxlength="180"></label>
        <label>Текст<textarea name="body" id="news-body" required maxlength="2500"></textarea></label>
        <button type="submit" class="btn">Сохранить</button>
        <button type="button" onclick="resetNewsForm()" class="btn" style="background: #6c757d; display:none;" id="news-cancel">Отмена</button>
        </form>
    </article>

    <script>
        function editNews(id, title, body) {
            document.getElementById('news-op').value = 'edit_news';
            document.getElementById('news-id').value = id;
            document.getElementById('news-title').value = title;
            document.getElementById('news-body').value = body;
            document.getElementById('news-form-title').innerText = 'Редактировать новость';
            document.getElementById('news-cancel').style.display = 'inline-block';
            document.getElementById('news-form-container').scrollIntoView({ behavior: 'smooth' });
        }
        function resetNewsForm() {
            document.getElementById('news-op').value = 'publish';
            document.getElementById('news-id').value = '0';
            document.getElementById('news-title').value = '';
            document.getElementById('news-body').value = '';
            document.getElementById('news-form-title').innerText = 'Опубликовать новость';
            document.getElementById('news-cancel').style.display = 'none';
        }
    </script>
<?php else: ?>
    <!-- Просмотр новостей для остальных -->
    <h2 style="margin-top: 40px;">Сообщения и отчеты участков</h2>
    <?php
    $news = query('SELECT * FROM bulletin ORDER BY id DESC')->fetchAll();
    if (!$news) echo '<p>Публикаций пока нет.</p>';
    foreach ($news as $n): ?>
        <article style="border-left: 4px solid var(--mvd-blue);">
            <time><?= e($n['created']) ?></time>
            <h3><?= e($n['title']) ?></h3>
            <p class="body"><?= e($n['body']) ?></p>
        </article>
    <?php endforeach ?>
<?php endif ?>