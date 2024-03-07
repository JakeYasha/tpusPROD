<div class="modal js-modal-ajax" id="feedbackForm" style="display: block;">
    <div class="modal__overlay"></div>
    <div class="modal__wrap">
        <div class="modal__content">
            <div class="modal__header">
                <div class="modal__title"><?=$heading?></div>
                <button class="modal__close"><i class="material-icons">close</i></button>
            </div>
            <form<?=html()->renderAttrs($attrs)?>>
                <div class="modal__body">
                    <?if(!$feedback_option->exists()) {?>
                        <div class="modal__item">
                            Кому
                            <input type="text" class="form__control form__control_modal" value="<?=$firm->name()?>, &lt;<?=$firm->firstEmail()?>&gt;" readonly="readonly" disabled="disabled" />
                        </div>
                    <?}?>
                    <input type="hidden" name="id_firm" value="<?=$firm->id()?>" />
                    <input type="hidden" name="feedback_option" value="<?=$feedback_option->id()?>" />
                    <input type="hidden" name="request_uri" value="<?= app()->request()->getRequestUri('') ?>" />
                    <? foreach ($fields as $field) {?>
                        <div class="modal__item">
                            <?= $field['label']?>
                            <?= $field['html']?>
                        </div>
                    <? }?>
                    <?= app()->chunk()->render('common.agreement_block') ?>
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