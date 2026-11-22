<header>
    <div class="header-top__wrapper">
        <!-- Левая часть (Логотип) -->
        <div class="header-top__logo top-logo">
            <a class="top-logo__img" href="./"><img src="service/Gerb.png" alt="МВД России"></a>
            <div class="top-logo__title">
                <h1>ГЛАВНОЕ УПРАВЛЕНИЕ МВД РОССИИ<br>ПО НИЖЕГОРОДСКОЙ ОБЛАСТИ</h1>
                <span class="top-logo__subtitle">Служим России, служим закону!</span>
            </div>
        </div>

        <!-- Правая часть (Город, Телефон, Кнопка) -->
        <div class="header-top__right">
            <!-- Город -->
            <div class="top-line__item top-regions">
                <a href="https://52.мвд.рф" class="link-regions" target="_blank">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="color:#0066b3;">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    г. Нижний Новгород
                </a>
            </div>

            <!-- Телефон -->
            <div class="header-top__info top-info">
                <div class="top-info__descript">Всегда<br>на связи!</div>
                <div class="top-info__phone">102</div>
            </div>

            <!-- Кнопка выйти -->
            <?php if ($user): ?>
                <div class="header-logout"><?= postForm('logout') ?><button>Выйти</button></form>
                </div>
            <?php endif ?>
        </div>
    </div>
</header>