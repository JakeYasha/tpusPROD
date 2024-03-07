<div id="firm-call-form-<?= $firm->id() ?>" class="popup firm-call-form" style="display:none;">
    <div class="top_field">
        <div class="title"><?= $heading ?></div>
    </div>
    <div>
        <div class="search-result-element-block firm-wrapper">
            <div class="element-info-block">
                <div class="firm-contacts">	
                    <div class="firm-name"><?= $firm->name() ?></div>
                    <div class="firm-contacts-line">
                        <span>
                            <?= $firm->renderPhoneLinks() ?>
                        </span>
                    </div>
                </div>
                <div class="for_clients">
                    <h3>Пожалуйста, скажите, что узнали номер на сайте <strong>tovaryplus.ru</strong></h3>
                </div>
            </div>
        </div>
    </div>
</div>