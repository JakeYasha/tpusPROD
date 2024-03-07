<?= $breadcrumbs ?>
<div class="for_clients clearfix">
    <div class="for_clients_text_c clearfix page">
        <div class="for_clients_list page-sitemap">
            <h1>Карта сайта</h1>
            <p>Сайт TovaryPlus.ru (Товары Плюс) – межрегиональная информационно-рекламная площадка, портал товаров и услуг. На сайте размещаются рекламные и справочно-информационные материалы и сведения о компаниях центральных регионов России, включая информацию о прайс-листах, текущих специальных предложениях и акциях компаний, а также их видеоматериалы.</p>
            <p>Условно все представленные данные по предложениям компаний подразделяются на следующие разделы: товары, услуги, оборудование, акции.</p>
            <h2>География представленных данных на сайте</h2>
            <ul>
                <? foreach ($matrix as $id_region => $id_cities) {
                    if (!$id_cities)
                        continue;
                    $show = false;
                    foreach ($id_cities as $_id => $id_city) {
                        if ($id_city == 0) continue;
                        if ($counts[$id_city]['firms'] >= 100) {
                            $show = true;
                            break;
                        }
                    }
                    if ($show) { ?>
                        <li>
                            <span><?= $regions[$id_region] ?></span> (<a href="<?= APP_URL ?>/ratiss/service/<?= $services[$id_region]['id_service'] ?>/">региональный представитель</a>)
                        </li>
                        <? foreach ($id_cities as $__id => $id_city) {
                            if (!$cities[$id_city] || $counts[$id_city]['firms'] < 100) {
                                continue;
                            }
                            $city_link = APP_URL . '/' . $id_city;
                            $links = [];
                            $links[] = '<a href="' . $city_link . '/firm/catalog/">фирм ' . $counts[$id_city]['firms'] . '</a>';
                            if ($counts[$id_city]['goods_1']) {
                                $links[] = '<a href="' . $city_link . '/catalog/">товаров ' . $counts[$id_city]['goods_1'] . '</a>';
                            }
                            if ($counts[$id_city]['goods_2']) {
                                $links[] = '<a href="' . $city_link . '/catalog/44/">услуг ' . $counts[$id_city]['goods_2'] . '</a>';
                            }
                            if ($counts[$id_city]['goods_3']) {
                                $links[] = '<a href="' . $city_link . '/catalog/22/">оборудования ' . $counts[$id_city]['goods_3'] . '</a>';
                            }
                            if ($counts[$id_city]['promos']) {
                                $links[] = '<a href="' . $city_link . '/advert-module/">акций ' . $counts[$id_city]['promos'] . '</a>';
                            }
                            $links[] = 'популярные: <a href="' . $city_link . '/firm/new/">фирмы</a>';
                            $links[] = '<a href="' . $city_link . '/statistics/popular/catalogs/">рубрики</a>';
                            ?>
                            <li class="tab"><a href="<?= $id_city === '76004' ? '/' : $city_link ?>"><?= $cities[$id_city] ?></a> (представлено: <?= implode(', ', $links) ?>)</li>
                        <? } ?>
                    <? } ?>
                <? } ?>
            </ul>
            <h2>Дополнительные страницы</h2>
            <ul>
                <li><a href="<?= APP_URL ?>/page/show/reklamno-informacionnyj-proekt-o-firmah-tovarah-i-uslugah-tovaryplus.htm">О проекте</a></li>
                <li><a href="<?= APP_URL ?>/statistics/">Статистика сайта</a></li>
                <li><a href="<?= APP_URL ?>/page/show/law.htm">Правовая информация</a></li>
            </ul>
        </div>
    </div>
</div>