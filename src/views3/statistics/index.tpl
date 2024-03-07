<div class="mdc-layout-grid">
    <?= $breadcrumbs?>
    <?/*=app()->chunk()->render('common.print_button')*/?>
    <div class="for_clients clearfix">
        <div class="for_clients_text_c clearfix page">
            <?= str()->replace($text->val('text'), ['_Cp_', '_Cg_', '_L_', '_Ci_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId(), app()->location()->currentName()])?>
            <? if (APP_IS_DEV_MODE) {?>
                <br/><br/>
                <ul>
                    <li>
                        <p>
                            <a href="/statistics/empty/">Пустые рубрики каталога</a><br>Список рубрик каталога товаров и услуг, которые вообще не представлены в каталоге или у них меньше 3 фирм с группировкой по группе.
                        </p>
                    </li>
                </ul>
            <? }?>
        </div>
    </div>
</div>