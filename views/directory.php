<h1>Участки и прием</h1>
<p>Участковые пункты полиции города Нижнего Новгорода.</p>

<!-- КАРТА -->
<div id="map" style="height: 400px; width: 100%; margin-bottom: 30px; border-radius: 8px; z-index: 1;"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Центр Нижнего Новгорода
        var map = L.map('map').setView([56.326, 44.005], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var precincts = <?= json_encode($mapPrecincts ?? [], JSON_UNESCAPED_UNICODE) ?>;

        precincts.forEach(function (p) {
            if (p.lat && p.lng) {
                var marker = L.marker([p.lat, p.lng]).addTo(map);
                marker.bindPopup('<b>' + p.name + '</b><br>' + p.address + '<br>Тел: ' + p.phone + '<br>Прием: ' + p.hours);
            }
        });
    });
</script>

<!-- СПИСОК УЧАСТКОВ -->
<?php
$precincts = query('SELECT p.*, u.name person FROM precinct p JOIN profile u ON u.id=p.officer ORDER BY p.district, p.id')->fetchAll();
$grouped = [];
foreach ($precincts as $p) {
    $grouped[$p['district']][] = $p;
}
?>
<div class="district-nav">
    <?php foreach ($grouped as $district => $items): ?>
        <a href="#<?= md5($district) ?>"><?= e($district) ?></a>
    <?php endforeach; ?>
</div>

<?php foreach ($grouped as $district => $items): ?>
    <h2 id="<?= md5($district) ?>" style="margin-top: 30px; border-bottom: 2px solid var(--mvd-rule); padding-bottom: 5px;">
        <?= e($district) ?></h2>
    <div class="grid">
        <?php foreach ($items as $p): ?>
            <article>
                <p class="role"><?= e($p['name']) ?></p>
                <p><strong><?= e($p['person']) ?></strong><br>
                    Часы приема: <?= e($p['hours']) ?><br>
                    Телефон: <?= e($p['phone']) ?><br>
                    Адрес: <?= e($p['address']) ?></p>
            </article>
        <?php endforeach ?>
    </div>
<?php endforeach ?>

<?php if (($user['role'] ?? '') === 'manager'): ?>
    <!-- Управление новостями -->
    <h2 style="margin-top: 40px;">Сообщения и отчеты участков</h2>
    <?php
    $news = query('SELECT * FROM bulletin ORDER BY id DESC')->fetchAll();
    if (!$news)
        echo '<p>Публикаций пока нет.</p>';
    foreach ($news as $n):
        ?>
        <article style="border-left: 4px solid var(--mvd-blue);">
            <time><?= e($n['created']) ?></time>
            <h3><?= e($n['title']) ?></h3>
            <p class="body"><?= e($n['body']) ?></p>

            <!-- Кнопки редактирования/удаления -->
            <div style="margin-top: 10px; display: flex; gap: 10px;">
                <button onclick="editNews(<?= $n['id'] ?>, '<?= e(addslashes($n['title'])) ?>', '<?= e(addslashes($n['body'])) ?>')"
                    class="btn" style="background: #ffc107; color: #000; padding: 5px 10px; font-size: 13px;">✏️
                    Редактировать</button>
                <form method="post" style="display:inline;">
                    <?= token() ?><input type="hidden" name="op" value="delete_news"><input type="hidden" name="id"
                        value="<?= $n['id'] ?>">
                    <button class="btn" style="background: #dc3545; padding: 5px 10px; font-size: 13px;"
                        onclick="return confirm('Удалить новость?');">🗑️ Удалить</button>
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
        <button type="button" onclick="resetNewsForm()" class="btn" style="background: #6c757d; display:none;"
            id="news-cancel">Отмена</button>
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
    if (!$news)
        echo '<p>Публикаций пока нет.</p>';
    foreach ($news as $n):
        ?>
        <article style="border-left: 4px solid var(--mvd-blue);">
            <time><?= e($n['created']) ?></time>
            <h3><?= e($n['title']) ?></h3>
            <p class="body"><?= e($n['body']) ?></p>
        </article>
    <?php endforeach ?>
<?php endif ?>