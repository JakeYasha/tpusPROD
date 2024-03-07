<div class="modal js-modal-ajax" id="callbackForm" style="display: block;">
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
                    <input type="hidden" name="referer" value="<?=$referer?>" />
                    <? foreach ($fields as $field) {?>
                        <div class="modal__item">
                            <?= $field['label']?>
                            <?= $field['html']?>
                        </div>
                    <? }?>
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