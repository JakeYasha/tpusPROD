<div class="mdc-layout-grid">
    <?= $breadcrumbs ?>
    <div class="for_clients clearfix">
        <style>
            .sp-al-warning {
                padding: 10px;
                background-color: #e91c231a;
                border-radius: 20px;
                text-align: center;
            }

            .sp-img-logo {
                width: 100%;
            }

            .hb-center {
                text-align: center;
            }

            .hb-fs12 {
                font-size: 12px;
            }

            .hb-fs14 {
                font-size: 14px;
            }

            .title-box {
                margin-top: 40px;
            }

            @media (min-width: 512px) {
                .sp-img-logo {
                    width: 50%;
                }

                .hb-pc-left {
                    text-align: left;
                }

                .hb-fs18 {
                    font-size: 18px;
                }

                .hb-fs20 {
                    font-size: 20px;
                }
            }
        </style>
        <div class="for_clients_text_c clearfix page sp-al-warning">
            <img class="sp-img-logo" style="text-align:center;" src="/uploaded/0/f/logosp.jpg">
            <div class="for_clients_list page-sitemap hb-fs12 hb-fs18">
                <span class="hb-fs14 hb-fs20">Представляем вам наш новый проект - <b>«Электронный справочник Ярославля»</b></span><br>В нем вы найдете необходимые вам товары и услуги, как в цветных модулях, так и в строчной информации, а удобный поиск информации по рубрикатору поможет вам быстро найти нужные компании Ярославля.

            </div>
        </div>
    </div>
    <link href="/public/flipbook/css/dflip.css" rel="stylesheet" type="text/css">
    <link href="/public/flipbook/css/themify-icons.css" rel="stylesheet" type="text/css">
    <div class="title-box">
        <h2>
            Актуальный справочник организаций:
        </h2>
    </div>
    <div class="hb-center hb-pc-left">
        <div class="_df_thumb" id="df_manual_thumb7" source="/uploaded/0/f/spravitt2024sjat.pdf" thumb="/uploaded/0/f/prev7.png">2024 - 7</div>
        <?
        if (isset($_GET["test1"])) {
        ?>
            <div class="_df_thumb" id="df_manual_thumb7" source="/uploaded/a/b/ab8edd813c4ff14b6d1d8318b1d92b89.pdf" thumb="/uploaded/0/f/ngakc1.jpg">НОВОГОДНИЕ ПРЕДЛОЖЕНИЯ</div>
            <div class="_df_thumb" id="df_manual_thumb" source="/uploaded/0/f/COOKING.pdf" thumb="/uploaded/0/f/COOKING.jpg">COOKING - TEST</div>

        <?
        }
        ?>
    </div>
    <?
        if (isset($_GET["test1"])) {
        ?>
    <div class="title-box">
        <h2>
            Архив прошлых выпусков:
        </h2>
    </div>
    <div class="hb-center hb-pc-left">
        <div class="_df_thumb" id="df_manual_thumb6" source="/uploaded/0/f/spravitor6.pdf" thumb="/uploaded/0/f/prev6.jpg">2023 - 6</div>
        <div class="_df_thumb" id="df_manual_thumb5" source="/uploaded/0/f/spravitor5.pdf" thumb="/uploaded/0/f/prev5.jpg">2023 - 5</div>
        
        <!-- <div class="_df_thumb" id="df_manual_thumb4" source="/uploaded/0/f/spravitor4.pdf" thumb="/uploaded/0/f/prev4.jpg">2022 - 4</div>
        <div class="_df_thumb" id="df_manual_thumb3" source="/uploaded/0/f/spravitor3.pdf" thumb="/uploaded/0/f/prev3.jpg">2022 - 3</div>
        <div class="_df_thumb" id="df_manual_thumb2" source="/uploaded/0/f/spravitor2.pdf" thumb="/uploaded/0/f/prev2.jpg">2021 - 2</div>
        <div class="_df_thumb" id="df_manual_thumb" source="/uploaded/0/f/spravitor.pdf" thumb="/uploaded/0/f/pregz.jpg">2021</div> -->
        

    </div>
    <?
    }
    ?>
    <div id="bookf" class=""></div>

    <script src="/public/flipbook/js/libs/jquery.min.js" type="text/javascript"></script>

    <script src="/public/flipbook/js/dflip.js?vv=<?= time(); ?>" type="text/javascript"></script>

    <script>
        setInterval(function() {
            if ($(".df-lightbox-wrapper").length) {
                if ($(".df-lightbox-wrapper").css('display').toLowerCase() == 'none') {
                    $("body").css('overflow-y', 'visible');
                } else {
                    $("body").css('overflow-y', 'hidden');
                }
            }

        }, 1000);
    </script>
    <? if (isset($_GET['s']) && $_GET['s'] == '1') {
    ?>
        <script>
            setTimeout(function() {
                if ($("#df_manual_thumb7").length) {
                    $('#df_manual_thumb7').click();
                }
            }, 3000);
        </script>
    <?
    } ?>
    <? if (isset($_GET['n']) && $_GET['n'] == '1') {
    ?>
        <script>
            setTimeout(function() {
                if ($("#df_manual_thumb7").length) {
                    $('#df_manual_thumb7').click();
                }
            }, 3000);
        </script>
    <?
    } ?>
</div>