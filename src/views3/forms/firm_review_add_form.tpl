<div class="modal js-modal-ajax" id="reviewAddForm" style="display: block;">
    <div class="modal__overlay"></div>
    <div class="modal__wrap">
        <div class="modal__content">
            <div class="modal__header">
                <div class="modal__title"><?=$heading?></div>
                <button class="modal__close"><i class="material-icons">close</i></button>
            </div>
            <form<?=html()->renderAttrs($attrs)?>>
                <div class="modal__body">
                    <input type="hidden" name="id_firm" value="<?=$firm->id()?>" />
                    <input type="hidden" name="score" value="" class="js-rate-score" />
                    <div class="modal__item">Ваша оценка фирмы <?=$firm->name()?> по 5 бальной системе</div>
                    <div class="modal__item">
                        <div class="js-rating">
                            <i class="material-icons" data-rate="1">star</i>
                            <i class="material-icons" data-rate="2">star</i>
                            <i class="material-icons" data-rate="3">star</i>
                            <i class="material-icons" data-rate="4">star</i>
                            <i class="material-icons" data-rate="5">star</i>
                        </div>
                    </div>
                    <? foreach ($fields as $field) {?>
                        <div class="modal__item">
                            <?= $field['label']?>
                            <?= $field['html']?>
                        </div>
                    <? }?>
                    <div class="modal__item"><a href="/page/show/pravila-publikacii-otzyvov.htm" target="_popup">Правила написания и публикации отзывов</a></div>
                    <?= app()->capcha()->render()?>
                    <div class="modal__item">
                        <div class="error-submit"></div>
                    </div>
                    <?= $controls['submit']['html']?>
                </div>
            </form>
		</div>
	</div>
</div>