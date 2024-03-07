old_footer
<div class="footer">
    <?= app()->chunk()->render('common.personal_data')?>
	<?= app()->chunk()->render('common.footer_menu')?>
    
    <?if($service->hasSocialLinks()){?>

                <div class="subscribe-block-wrapper">
                        <div class="subscribe-block">
                            <?if (app()->location()->currentId() == '76004') {?>
                                <p style="text-align: center; margin-bottom: 5px;">Хотите узнавать о скидках и розыгрышах товаров и услуг компаний в Ярославле?</p>
                                <p style="text-align: center; margin-bottom: 5px;">Присоединяйтесь к нашим группам в социальных сетях и подписывайтесь на рассылку.</p>
                            <?} else {?>
                                <p style="text-align: center; margin-bottom: 5px;">Хотите узнавать о скидках и розыгрышах товаров и услуг компаний?</p>
                                <p style="text-align: center; margin-bottom: 5px;">Присоединяйтесь к нашим группам в социальных сетях.</p>
                            <?}?>
                                <br/>
                                <div style="text-align:center;">
                                        <div class="subscribe-block-social">
                                                <?if($service->hasSocialLinks()){?>
                                                <div class="soc_block">
                                                        <ul class="subscribe-list-social">
                                                                <!--noindex-->
                                                                        <?=($service->hasVKLink()) ? '<li><a target="_blank" href="' . $service->getVKLink() . '" class="vk" title="' . CHtml::encode($service->name()) . ' в ВКонтакте" rel="nofollow"></a></li>' : ''?> 
                                                                        <?=($service->hasFBLink()) ? '<li><a target="_blank" href="' . $service->getFBLink() . '" class="fb" title="' . CHtml::encode($service->name()) . ' в Facebook" rel="nofollow"></a></li>' : ''?> 
                                                                        <?=($service->hasTWLink()) ? '<li><a target="_blank" href="' . $service->getTWLink() . '" class="tw" title="' . CHtml::encode($service->name()) . ' в Twitter" rel="nofollow"></a></li>' : ''?> 
                                                                        <?=($service->hasINLink()) ? '<li><a target="_blank" href="' . $service->getINLink() . '" class="ig" title="' . CHtml::encode($service->name()) . ' в Instagram" rel="nofollow"></a></li>' : ''?> 
                                                                        <?=($service->hasOKLink()) ? '<li><a target="_blank" href="' . $service->getOKLink() . '" class="ok" title="' . CHtml::encode($service->name()) . ' в Однокласниках" rel="nofollow"></a></li>' : ''?> 
                                                                        <?=($service->hasGPLink()) ? '<li><a target="_blank" href="' . $service->getGPLink() . '" class="gp" title="' . CHtml::encode($service->name()) . ' в Google+" rel="nofollow"></a></li>' : ''?> 
                                                                        <?=($service->hasYTLink()) ? '<li><a target="_blank" href="' . $service->getYTLink() . '" class="yt" title="' . CHtml::encode($service->name()) . ' на YouTube" rel="nofollow"></a></li>' : ''?> 
                                                                <!--/noindex-->	
                                                        </ul>
                                                </div>
                                                <?}?>
                                        </div>
                                        <?if (app()->location()->currentId() == '76004') {?>
                                        <div class="subscribe-block-email">
                                                <?if (true==false) {?>
                                                <!-- SendPulse Form -->
                                                <div class="sp-form-outer sp-force-hide">
                                                    <div id="sp-form-83435" sp-id="83435" sp-hash="a2acc31d1cc0e616bf8718592c036ed80730939e942abf536eb8869e68ec260c" sp-lang="ru" class="sp-form sp-form-regular sp-form-embed" sp-show-options="%7B%22amd%22%3Afalse%2C%22condition%22%3A%22onEnter%22%2C%22scrollTo%22%3A25%2C%22delay%22%3A10%2C%22repeat%22%3A3%2C%22background%22%3A%22rgba(0%2C%200%2C%200%2C%200.5)%22%2C%22position%22%3A%22bottom-right%22%2C%22animation%22%3A%22%22%2C%22hideOnMobile%22%3Afalse%2C%22urlFilter%22%3Afalse%2C%22urlFilterConditions%22%3A%5B%7B%22force%22%3A%22hide%22%2C%22clause%22%3A%22contains%22%2C%22token%22%3A%22%22%7D%5D%7D">
                                                        <div class="sp-form-fields-wrapper">
                                                            <div class="sp-element-container sp-sm sp-field-nolabel">
                                                                <div class="sp-field " sp-id="sp-40ab5e3b-2863-46ef-9800-6838929e1500"><input type="email" sp-type="email" name="sform[email]" class="sp-form-control subscribe-email" placeholder="username@gmail.com" sp-tips="%7B%22required%22%3A%22%D0%9E%D0%B1%D1%8F%D0%B7%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5%20%D0%BF%D0%BE%D0%BB%D0%B5%22%2C%22wrong%22%3A%22%D0%9D%D0%B5%D0%B2%D0%B5%D1%80%D0%BD%D1%8B%D0%B9%20email-%D0%B0%D0%B4%D1%80%D0%B5%D1%81%22%7D" required="required"></div><div class="sp-field sp-hidden" sp-id="sp-6f648d3a-2523-4ccf-b712-aeae14251ee2"><input sp-type="input" name="sform[0KLQuNC/]" class="sp-form-control " placeholder="" sp-tips="%7B%7D" type="text" value="tovaruplus.ru"></div><div class="sp-field sp-button-container " sp-id="sp-f4734018-91dc-414c-9b85-6133fa602f21"><button id="sp-f4734018-91dc-414c-9b85-6133fa602f21" class="sp-button subscribe-button disabled">@ </button></div>
                                                                <span class="subscribe-hint">
                                                                        <input type="checkbox" class="subscribe-agreement"/> подписка на рассылку означает принятие пользовательского соглашения и предоставление согласия на обработку личных данных
                                                                </span>
                                                            </div>
                                                            <div class="sp-message">
                                                                <div></div>
                                                            </div>
                                                            <div class="sp-link-wrapper sp-brandname__left"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script src="//static-login.sendpulse.com/apps/fc3/build/default-handler.js?1506679365509"></script> 
                                                <!-- /SendPulse Form -->
                                                <?//} else {?>
                                                <input class="subscribe-email" type="text" placeholder="example@mail.ru"/>
                                                <button class="subscribe-button disabled">@</button>
                                                <span class="subscribe-hint">
                                                        <input type="checkbox" class="subscribe-agreement"/> подписка на рассылку означает принятие пользовательского соглашения и предоставление согласия на обработку личных данных
                                                </span>
                                                <?}?>
                                        </div>
                                        <?}?>
                                </div>
                        </div>
                </div>
            <?}?>
    
        <div class="clearfix footer_info">
                <div class="footer_col regional_info">
                        <div class="service_block">
                            <strong>TovaryPlus.ru - <?=app()->location()->currentName()?></strong>
                        </div>

                        <p><strong>Региональное представительство:</strong>&nbsp;<a href="/ratiss/service/<?=$service->id()?>/"><?=$service->name()?></a></p>
                        <?if ($service->hasAddress()) { ?><p><strong>Адрес:</strong> <?=$service->val('address')?></p><?}?>
                        <?if ($service->hasPhone()) { ?><p><strong>Телефон:</strong> <?=$service->val('phone')?></p><?}?>
                        <?if ($service->hasModeWork()) { ?><p><strong>Режим работы:</strong> <?=$service->val('mode_work')?></p><?}?>
                        <p><strong>Email:</strong> <?=$service->val('email')?></p>
                </div>
                <div class="footer_col add_company">
                        <p><strong>Добавьте свою фирму на TovaryPlus.ru и начните продавать уже сейчас!</strong></p>
                        <br/>
                        <p><a class="bubble_button" href="/request/add/" rel="nofollow">Добавить фирму</a></p>
                </div>

                <button class="to_top"></button>
        </div>
	<div class="counters">
		<?=app()->chunk()->render('counters.metrika')?>
		<?=app()->chunk()->render('counters.google')?>
		<?=app()->chunk()->render('counters.liveinternet')?>
	</div>
        <!-- VK -->
        <script>(window.Image ? (new Image()) : document.createElement('img')).src='https://vk.com/rtrg?p=VK-RTRG-156769-7sJLI';</script>
        <!-- /VK -->
</div>