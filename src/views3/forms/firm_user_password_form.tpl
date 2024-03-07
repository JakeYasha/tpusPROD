<div class="modal js-modal-ajax" id="restoreForm" style="display: block;">
    <div class="modal__overlay"></div>
    <div class="modal__wrap">
        <div class="modal__content js-ajax-form js-ajax-send-submit-enter">
            <div class="modal__header">
                <div class="modal__title"><?=$heading?></div>
                <button class="modal__close"><i class="material-icons">close</i></button>
            </div>
            <form<?=html()->renderAttrs($attrs)?>>
                <div class="modal__body">
                    <h2><?=$sub_heading?></h2>
                    <?foreach ($fields as $field) {?>
                        <div class="modal__item">
                            <?=$field['label']?><?=$field['html']?>
                        </div>
                    <?}?>
                <div class="modal__item">
                    <div class="error-submit"></div>
                </div>
            	<?=$controls['submit']['html']?>
                </div>
            </form>
        </div>
    </div>
</div>