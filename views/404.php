<div class="page-404">
    <div class="page-404__icon">
        <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="60" cy="60" r="55" stroke="#0066b3" stroke-width="4" stroke-dasharray="8 6"/>
            <path d="M60 35v30" stroke="#c41e3a" stroke-width="6" stroke-linecap="round"/>
            <circle cx="60" cy="82" r="4" fill="#0066b3"/>
        </svg>
    </div>
    <h1 class="page-404__code">404</h1>
    <h2 class="page-404__title">Страница не найдена</h2>
    <p class="page-404__text">
        Запрашиваемая страница не существует, была удалена или временно недоступна.
        Проверьте правильность адреса или вернитесь на главную.
    </p>
    <div class="page-404__actions">
        <?php if ($user): ?>
            <a href="?page=cabinet" class="btn">В личный кабинет</a>
        <?php else: ?>
            <a href="./" class="btn">На главную</a>
        <?php endif; ?>
        <a href="?page=directory" class="btn btn-ghost">Участки и приём</a>
    </div>
    <p class="page-404__hint">
        Если вы считаете, что это ошибка — сообщите в
        <a href="https://52.мвд.рф" target="_blank">ГУ МВД по Нижегородской области</a>
    </p>
</div>