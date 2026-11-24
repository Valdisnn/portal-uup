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
        <label>Адрес события<input id="appealAddress" name="address" list="houses" autocomplete="off" maxlength="150" placeholder="Начните вводить улицу и дом Нижнего Новгорода"></label>
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

<script nonce="<?= e($cspNonce) ?>">
    function validateFile(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                alert('Файл слишком большой. Максимум 5 МБ.');
                input.value = ''; // Очищаем поле
            }
        }
    }

    // Подсказки адресов Нижнего Новгорода через Photon / OpenStreetMap.
    // После выбора адрес всё равно сверяется с локальным справочником building.
    const addressInput = document.getElementById('appealAddress');
    const addressList = document.getElementById('houses');
    let addressTimer = null;
    if (addressInput && addressList) {
        addressInput.addEventListener('input', () => {
            clearTimeout(addressTimer);
            const q = addressInput.value.trim();
            if (q.length < 4) return;
            addressTimer = setTimeout(async () => {
                try {
                    const url = 'https://photon.komoot.io/api/?' + new URLSearchParams({
                        q: q + ', Нижний Новгород, Россия',
                        lat: '56.2965', lon: '43.9361', limit: '8', lang: 'ru'
                    });
                    const response = await fetch(url, {headers: {'Accept':'application/json'}});
                    if (!response.ok) return;
                    const data = await response.json();
                    const values = (data.features || []).map(feature => {
                        const p = feature.properties || {};
                        const street = p.street || p.name || '';
                        const house = p.housenumber ? ', д. ' + p.housenumber : '';
                        return ['Нижний Новгород', street + house].filter(Boolean).join(', ');
                    }).filter(Boolean);
                    const existing = Array.from(addressList.options).map(option => option.value);
                    addressList.replaceChildren(...[...new Set([...existing, ...values])].map(value => {
                        const option = document.createElement('option');
                        option.value = value;
                        return option;
                    }));
                } catch (error) {
                    // Ручной ввод остаётся доступен при недоступности сервиса подсказок.
                }
            }, 350);
        });
    }
</script>