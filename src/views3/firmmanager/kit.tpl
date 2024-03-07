<?= $bread_crumbs ?>

<div class="search_result">
    <div class="popup wide">
        <div class="top_field">
            <div class="title">Создание новой подборки</div>
        </div>
        <div class="inputs">
            <input type="hidden" id="kit_id" value="<?= $kit->exists() ? $kit->id() : 0 ?>"/>
            <label>Название<input class="e-text-field" type="text" name="name" value="<?= $kit->exists() ? $kit->name() : '' ?>"/></label>
            <input class="e-text-field" type="hidden" name="number" value="<?= $kit->exists() ? $kit->val('number') : 0 ?>"/>
            <label>Анонс подборки<textarea class="e-text-area js-tiny-mce" name="short_text"><?= $kit->exists() ? $kit->val('short_text') : '' ?></textarea></label>
            <div class="error-submit"></div>
        </div>
        <div class="top_field">
            <div class="title">META</div>
        </div>
        <div class="inputs">
            <label>TITLE<input class="e-text-field" type="text" name="meta_title" value="<?= $kit->exists() ? $kit->val('meta_title') : '' ?>"/></label>
            <label>KEYWORDS<input class="e-text-field" type="text" name="meta_keywords" value="<?= $kit->exists() ? $kit->val('meta_keywords') : '' ?>"/></label>
            <label>DESCRIPTION<input class="e-text-field" type="text" name="meta_description" value="<?= $kit->exists() ? $kit->val('meta_description') : '' ?>"/></label>
            <button class="e-button send js-send" id="save_kit" type="button">Сохранить</button>
        </div>
    </div>
</div>