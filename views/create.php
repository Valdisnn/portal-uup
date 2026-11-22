<h1>Подать обращение</h1>
<article>
    <form method="post" enctype="multipart/form-data">
        <?= token() ?>
        <input type="hidden" name="op" value="create">

        <!-- Honeypot (ловушка для спам-ботов) -->
        <div style="display:none;">
            <input type="text" name="website" value="" tabindex="-1" autocomplete="off">
        </div>

        <label>Категория<select
                name="category"><?php foreach ($categories as $c)
                    echo '<option>' . e($c) . '</option>'; ?></select></label>
        <label>Тема<input name="subject" required maxlength="180"></label>
        <label>Адрес события<input name="address" list="houses" placeholder="По умолчанию: <?= e($user['residence']) ?>"
                maxlength="150"></label>
        <datalist
            id="houses"><?php foreach (query('SELECT address FROM building') as $b)
                echo '<option>' . e($b['address']) . '</option>'; ?></datalist>
        <label>Текст обращения<textarea name="body" required maxlength="5000"></textarea></label>

        <label>Вложение (PDF, JPEG или PNG до 5 Мбайт)
            <input type="file" name="file" accept=".pdf,.png,.jpg,.jpeg" onchange="validateFile(this)">
        </label>

        <button>Зарегистрировать обращение</button>
    </form>
</article>

<script>
    function validateFile(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                alert('Файл слишком большой. Максимум 5 МБ.');
                input.value = ''; // Очищаем поле
            }
        }
    }
</script>