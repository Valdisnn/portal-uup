<header>
    <div class="header-top__wrapper">
        <div class="header-top__logo top-logo">
            <a class="top-logo__img" href="./"><img src="service/Gerb.png" alt="МВД России"></a>
            <div class="top-logo__title">
                <h1>ГЛАВНОЕ УПРАВЛЕНИЕ МВД РОССИИ<br>ПО НИЖЕГОРОДСКОЙ ОБЛАСТИ</h1>
                <span class="top-logo__subtitle">Служим России, служим закону!</span>
            </div>
        </div>
        <div class="top-line__item top-regions">
            <a href="https://52.мвд.рф" class="link-regions" target="_blank">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0066b3;">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                г. Нижний Новгород
            </a>
        </div>
        <div class="header-top__info top-info">
            <div class="top-info__descript">Всегда<br>на связи!</div>
            <div class="top-info__phone">102</div>
        </div>
        <div class="header-logout">
            <form method="post" action="./" style="display:inline;">
                <?= token() ?>
                <input type="hidden" name="op" value="logout">
                <button type="submit">Выйти</button>
            </form>
        </div>
    </div>
    <?php if ($user): ?>
    <nav style="max-width:1100px; margin:0 auto; padding:12px 20px 0;">
        <a href="?page=cabinet" style="text-decoration:none; padding:10px 16px; border-bottom:3px solid <?= $page==='cabinet'?'var(--mvd-red)':'transparent' ?>; font-weight:600;">Личный кабинет</a>
        <a href="?page=directory" style="text-decoration:none; padding:10px 16px; border-bottom:3px solid <?= $page==='directory'?'var(--mvd-red)':'transparent' ?>; font-weight:600;">Участки и прием</a>
        <?php if (in_array($user['role'], ['manager', 'department_head'], true)): ?>
            <a href="?page=stats" style="text-decoration:none; padding:10px 16px; border-bottom:3px solid <?= $page==='stats'?'var(--mvd-red)':'transparent' ?>; font-weight:600;">Статистика</a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>
</header>