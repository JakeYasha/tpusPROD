<?= $bread_crumbs ?>

<div class="search_result">
    <div class="popup wide">
        <div class="top_field">
            <div class="title">Создание новой подборки</div>
        </div>
        <div class="inputs">
            <input type="hidden" id="kit_id" value="<?= $kit->exists() ? $kit->id() : 0 ?>"/>
            <label>Название<input class="e-text-field" type="text" name="name" value="<?= $kit->exists() ? $kit->name() : '' ?>"/></label>
            <label>Номер подборки<input class="e-text-field" type="text" name="number" value="<?= $kit->exists() ? $kit->val('number') : '' ?>"/></label>
            <label>Описание подборки<textarea class="e-text-area js-tiny-mce" name="short_text"> value="<?= $kit->exists() ? $kit->val('short_text') : '' ?>"</textarea></label>
            <div class="error-submit"></div>
            <button class="e-button send js-send" type="button">Сохранить</button>
        </div>
    </div>
</div>