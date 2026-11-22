<?php
if (!defined('PORTAL_REQUEST')) {
    http_response_code(404);
    exit;
}
$showNotice = ($_GET['page'] ?? '') === 'notice' && !empty($_SESSION['acknowledged']);
?>
<!doctype html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Порядок подачи обращения | Портал участковых</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="entry">
    <header <?= $showNotice ? 'inert' : '' ?>>
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
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="color:#0066b3;">
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
        </div>
    </header>

    <main <?= $showNotice ? 'inert' : '' ?>>
        <div class="entry-doc">
            <p class="crumb">Для граждан и сотрудников / Обращения</p>
            <h1 style="text-align: center; font-size: 24px; margin-bottom: 30px;">Уважаемые граждане!</h1>

            <p>Обращения в форме электронного документа рассматриваются в соответствии с <a
                    href="//static.mvd.ru/upload/site1/59FZ0609.pdf" target="_blank" class="doc-link">Федеральным
                    законом от 2 мая 2006 года № 59-ФЗ «О порядке рассмотрения обращений граждан Российской
                    Федерации»</a> и <a href="https://static.mvd.ru/upload/site1/prikaz200.pdf" target="_blank"
                    class="doc-link">приказом МВД России от 4 апреля 2025 года № 200 «Об установлении особого порядка
                    направления гражданами обращений в форме электронного документа и направления ответов на обращения,
                    поступившие в форме электронного документа, уведомлений в системе Министерства внутренних дел
                    Российской Федерации»</a>.</p>

            <div class="entry-warning">
                <p style="margin: 0;">Обращаем внимание, что согласно части 1 статьи 474.2 Уголовно-процессуального
                    кодекса Российской Федерации заявления о преступлении не могут быть поданы в форме электронного
                    документа. Сообщения о происшествиях (преступлениях, событиях, угрожающих личной или общественной
                    безопасности, а также иных обстоятельствах, требующих проверки возможных признаков преступления или
                    административного правонарушения), круглосуточно принимаются и незамедлительно регистрируются в
                    дежурных частях территориальных органов внутренних дел или по телефонам 02 (со стационарных
                    телефонов) и 102 (с мобильных средств связи).</p>
            </div>

            <p>Обращения подлежат регистрации в течение трех дней с момента поступления в орган, организацию,
                подразделение системы МВД России.</p>

            <p>Для направления обращений в форме электронного документа необходимо пройти процедуры идентификации и
                аутентификации с использованием подтвержденной учетной записи в федеральной государственной
                информационной системе «Единая система идентификации и аутентификации в инфраструктуре, обеспечивающей
                информационно-технологическое взаимодействие информационных систем, используемых для предоставления
                государственных и муниципальных услуг в электронной форме» (ЕСИА).</p>

            <p><b>При заполнении формы обращения в обязательном порядке следует указать адрес электронной почты,
                    доменное имя которого находится в российской национальной доменной зоне и четко сформулировать суть
                    обращения.</b> Также реализована возможность прикрепления к обращению необходимых документов и
                материалов в электронной форме.</p>

            <p>Поступившие обращения рассматриваются в течение 30 дней со дня регистрации. При необходимости, срок
                рассмотрения может быть продлен не более чем на 30 дней с направлением уведомления.</p>

            <p>Если в обращении не содержится информация о результатах рассмотрения обозначенных вопросов
                соответствующими территориальными органами внутренних дел и разрешение этих вопросов не относится к
                исключительной компетенции подразделений центрального аппарата министерства, то оно будет направлено для
                рассмотрения по существу в территориальные органы внутренних дел. При этом уведомление гражданину о
                переадресации обращения между органами, организациями и подразделениями системы МВД России не
                направляется.</p>

            <p>Обращение, содержащее вопросы, решение которых не входит в компетенцию органов внутренних дел Российской
                Федерации, направляется в течение семи дней со дня регистрации в соответствующий государственный орган,
                орган местного самоуправления или должностному лицу, в компетенцию которых входит решение поставленных в
                обращении вопросов, с уведомлением об этом гражданина, направившего обращение.</p>

            <p>Ответ на обращение будет направлен в форме электронного документа на указанный адрес электронной почты. В
                целях обеспечения безопасности может быть принято решение о направлении ответа на обращение в письменной
                форме по почтовому адресу, указанному в обращении.</p>

            <p>При обжаловании процессуальных решений, вынесенных сотрудниками органов внутренних дел, рекомендуем
                одновременно прикреплять копии соответствующих документов.</p>

            <p>Обжалование судебных решений осуществляется в соответствии с требованиями процессуального
                законодательства. Рассмотрение такого рода обращений не входит в компетенцию МВД России.</p>

            <p>В случае если текст обращения не позволяет определить суть предложения, заявления или жалобы, ответ на
                обращение не дается, о чём в течение семи дней со дня регистрации обращения сообщается гражданину. Кроме
                того, без ответа по существу поставленных вопросов останется обращение, в котором содержатся
                нецензурные, либо оскорбительные выражения, угрозы жизни, здоровью и имуществу должностного лица, а
                также членов его семьи.</p>

            <p>Обращаем внимание на недопустимость злоупотребления правом на обращение в государственные органы и
                предусмотренную законодательством ответственность в этой сфере общественных отношений. В случае, если в
                обращении указаны заведомо ложные сведения, расходы, понесенные в связи с его рассмотрением, могут быть
                взысканы с автора.</p>

            <h2>Персональные данные</h2>
            <p>Информация о персональных данных, направленных в электронном виде, хранится и обрабатывается с
                соблюдением требований <a
                    href="http://pravo.gov.ru/proxy/ips/?docbody=&link_id=0&nd=102108261&intelsearch=+%CE+%EF%E5%F0%F1%EE%ED%E0%EB%FC%ED%FB%F5+%E4%E0%ED%ED%FB%F5&firstDoc=1"
                    target="_blank" class="doc-link">Федерального закона Российской Федерации от 27 июля 2006 г. №152-ФЗ
                    «О персональных данных»</a> и Правил обработки персональных данных в системе МВД России,
                утвержденных <a
                    href="http://pravo.gov.ru/proxy/ips/?docbody=&link_id=0&nd=102461819&intelsearch=+%CE+%ED%E5%EA%EE%F2%EE%F0%FB%F5+%EC%E5%F0%E0%F5&firstDoc=1"
                    target="_blank" class="doc-link">приказом МВД России от 21 декабря 2017 г. №949</a> «О некоторых
                мерах, направленных на обеспечение выполнения МВД России обязанностей, предусмотренных <a
                    href="http://pravo.gov.ru/proxy/ips/?docbody=&link_id=0&nd=102108261&intelsearch=+%CE+%EF%E5%F0%F1%EE%ED%E0%EB%FC%ED%FB%F5+%E4%E0%ED%ED%FB%F5&firstDoc=1"
                    target="_blank" class="doc-link">Федеральным законом от 27 июля 2006 г. №152-ФЗ «О персональных
                    данных».</a></p>

            <p>Порядок выполнения мероприятий по защите персональных данных, содержащихся в информационных системах
                органов внутренних дел Российской Федерации, меры по обеспечению безопасности персональных данных при их
                обработке в информационных системах персональных данных, а также соответствующие обязанности должностных
                лиц определены Инструкцией по организации защиты персональных данных, содержащихся в информационных
                системах органов внутренних дел Российской Федерации, утвержденной <a
                    href="https://mvd.ru/upload/site1/folder_appeal/Prikaz_MVD_RF_ot_6_iyulya_2012_g_N_678_Ob_utverzhdenii_Instruktsii_po_organizatsii_za.pdf"
                    target="_blank" class="doc-link">приказом МВД России от 6 июля 2012 г. №678.</a></p>

            <?php if ($error): ?>
                <p class="notice error" role="alert"><?= e($error) ?></p>
            <?php endif ?>

            <form class="entry-actions-bottom" method="post" action="./">
                <?= token() ?>
                <input type="hidden" name="op" value="acknowledge">

                <div style="flex: 1; min-width: 100%; margin-bottom: 10px;">
                    <label class="check" style="font-size: 16px; margin-bottom: 20px;">
                        <input type="checkbox" name="ack" value="1" required <?= $showNotice ? 'checked' : '' ?>>
                        <b>С информацией ознакомлен</b>
                    </label>
                    <label class="check" style="display:none;">
                        <input type="checkbox" name="consent" value="1" checked>
                    </label>
                </div>

                <button type="submit" name="action" value="citizen" style="background: var(--mvd-blue);">Подать
                    обращение от гражданина</button>
                <button type="submit" name="action" value="org" style="background: #6c757d; cursor: not-allowed;"
                    disabled title="Для юридических лиц подача временно недоступна">Подать обращение от юридического
                    лица</button>
            </form>
        </div>
    </main>

    <footer class="footer" <?= $showNotice ? 'inert' : '' ?>>
        <div class="footer__wrapper">
            <div class="footer__nav">
                <div class="footer__menu-box footer-menu">
                    <h3 class="footer__title footer-menu__title">Меню</h3>
                    <ul class="footer-menu__list">
                        <li class="footer-menu__item"><a class="footer-menu__link" href="?page=directory">Участки и
                                прием</a></li>
                        <li class="footer-menu__item"><a class="footer-menu__link" href="?page=cabinet">Личный
                                кабинет</a></li>
                        <li class="footer-menu__item"><a class="footer-menu__link" href="https://мвд.рф"
                                target="_blank">Сайт МВД РФ</a></li>
                        <li class="footer-menu__item"><a class="footer-menu__link" href="https://52.мвд.рф"
                                target="_blank">ГУ МВД по Нижегородской области</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer__bottom">
                <div class="logo footer__logo">
                    <a class="logo__link" href="./">
                        <img class="icon logo__pic" src="service/mvd-emblem-monochrome.svg" alt="Эмблема МВД России">
                    </a>
                </div>
                <p class="footer__label">Официальный сайт Министерства внутренних дел Российской Федерации<br>© 2026,
                    МВД России</p>
                <div class="footer__contacts">
                    <dl class="footer__phone">
                        <dt class="footer__phone-number">102</dt>
                        <dd class="footer__phone-desc">Всегда на&nbsp;связи!</dd>
                    </dl>
                    <ul class="social footer-social">
                        <li class="social__item footer-social__item">
                            <a class="social__link footer-social__link" href="http://vk.com/news_mvdnn" target="_blank"
                                aria-label="ВКонтакте"><svg class="footer-social__icon" viewBox="0 0 24 24">
                                    <path
                                        d="M12.785 16.241s.288-.032.436-.191c.136-.145.132-.418.132-.418s-.02-1.275.574-1.463c.585-.186 1.335 1.234 2.13 1.78.603.413 1.06.322 1.06.322l2.132-.03s1.116-.069.587-.946c-.043-.071-.308-.648-1.584-1.835-1.334-1.241-1.155-1.04.451-3.187 1.095-1.379 1.569-2.22 1.429-2.582-.133-.344-.964-.253-.964-.253l-2.4.015s-.179-.024-.31.055c-.131.08-.213.264-.213.264s-.371.987-.867 1.827c-1.037 1.762-1.45 1.856-1.622 1.746-.39-.252-.29-1.017-.29-1.561 0-1.697.256-2.403-.5-2.586-.252-.06-.434-.1-1.075-.107-.824-.009-1.52.003-1.917.196-.262.129-.464.418-.341.434.152.02 1.772.33 1.772 2.056 0 1.773-.332 2.182-.673 2.182-.719 0-1.597-1.553-2.279-3.232-.25-.596-.432-1.08-.617-1.287-.22-.24-.622-.312-1.025-.312h-1.832s-.44.012-.63.209c-.17.18-.132.54-.132.54s1.15 3.293 3.107 4.927c1.956 1.633 3.751 1.461 3.751 1.461h.947z" />
                                </svg></a>
                        </li>
                        <li class="social__item footer-social__item">
                            <a class="social__link footer-social__link" href="https://max.ru/news_mvdnn" target="_blank"
                                aria-label="MAX"><svg class="footer-social__icon" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                                </svg></a>
                        </li>
                        <li class="social__item footer-social__item">
                            <a class="social__link footer-social__link" href="https://ok.ru/group/60128422592683"
                                target="_blank" aria-label="Одноклассники"><svg class="footer-social__icon"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 5c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                                </svg></a>
                        </li>
                        <li class="social__item footer-social__item">
                            <a class="social__link footer-social__link" href="https://rutube.ru/channel/25513721/"
                                target="_blank" aria-label="RuTube"><svg class="footer-social__icon"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M21.33 2H2.67C1.2 2 0 3.2 0 4.67v14.67C0 20.8 1.2 22 2.67 22h18.67C22.8 22 24 20.8 24 19.33V4.67C24 3.2 22.8 2 21.33 2zM12 16.67c-1.84 0-3.33-1.49-3.33-3.33S10.16 10 12 10s3.33 1.49 3.33 3.33-1.49 3.34-3.33 3.34z" />
                                </svg></a>
                        </li>
                        <li class="social__item footer-social__item">
                            <a class="social__link footer-social__link" href="https://t.me/NiNoMVD" target="_blank"
                                aria-label="Telegram"><svg class="footer-social__icon" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2-.08-.06-.19-.04-.27-.02-.12.02-1.96 1.25-5.54 3.69-.52.36-1 .53-1.42.52-.47-.01-1.37-.26-2.03-.48-.82-.27-1.47-.42-1.42-.88.03-.24.35-.48.96-.74 3.76-1.64 6.27-2.73 7.54-3.26 3.57-1.47 4.3-1.73 4.79-1.74.11 0 .35.03.51.16.12.1.16.24.18.41.01.11.02.22.01.32z" />
                                </svg></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <?php if ($showNotice): ?>
        <div class="notice-overlay">
            <dialog open aria-modal="true" id="entry-notice" aria-labelledby="notice-title" aria-describedby="notice-body">
                <div class="notice-head">
                    <h2 id="notice-title">Уважаемые граждане!</h2>
                    <a href="./" class="close" aria-label="Закрыть уведомление">×</a>
                </div>
                <p id="notice-body">Для направления обращения в орган, организацию, подразделение системы МВД России
                    необходимо пройти процедуру идентификации и аутентификации через Единую систему идентификации и
                    аутентификации (ЕСИА)</p>
                <div class="notice-actions">
                    <a class="official-link" href="./" style="border-color: #d5dbe1; color: #1d2026;">Отмена</a>
                    <form method="post" action="./" style="display:inline;">
                        <?= token() ?>
                        <input type="hidden" name="op" value="enter">
                        <button type="submit"
                            style="background: #ffffff; border: 1px solid #0055ff; color: #0055ff; padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"
                                    fill="#0055ff" />
                                <path d="M7 12l3 3 7-7" stroke="white" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            Войти через Госуслуги
                        </button>
                    </form>
                </div>
            </dialog>
        </div>
    <?php endif ?>
</body>

</html>