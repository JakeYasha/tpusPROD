<script>
    sessionStorage.setItem('tpc-template', 
        <?=$material->exists() ? 'JSON.stringify(' . $material->val('constructor_data') . ')' : '\'\'' ?>
    );
    alert('ОШИБКА РЕДАКТОРА!!! Откройте, пожалуйста, материал вновь, через личный кабинет.');
    <?
    setcookie('theme_name', "NULL", time()+24*60*60*1000, '/');
    ?>
    document.location.href = document.location.href;;
    <?
    setcookie('theme_name', "NULL", time()+24*60*60*1000, '/');
    ?>
    console.log('view3'); 
</script>
<style>
    .constructor-images{
        position: fixed;
        left: 0;
        width: 480px;
        height: 86%;
        top: 60px;
        z-index: 2;
        padding: 20px;
        background-color: #ff0080;
    }
    .constructor-images .c-i-settings{
        position: relative;
        height: 100%;
        width: 100%;
        background-color: #FFFFFF;
        text-align: center;
        overflow-y: scroll;
    }
    .constructor-images .c-i-settings .c-i-s-title{
        font-size: 24px;
        font-weight: bold;
        padding-top: 5px;
        padding-bottom: 5px;
    }
    .c-i-s-img-box{
        padding-left: 20px;
        padding-right: 20px;
    }
    .c-i-img{
        max-width: 320px;
        object-fit: cover;
        border: 1px solid #ff0080;
    }
    .c-d-ig{
        display: inline-grid;
    }
    .c-d-flex{
        display: flex;
    }
    .c-mt-5{
        margin-top: 5px;
    }
    .c-text-left{
        text-align: left;
    }
    .c-i-s-inputbox>input{
        display: block;
        width: 100%;
        padding: .375rem .75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    .c-i-s-inputbox>label{
        font-size: 14px;
        font-weight: bold;
    }
    .c-small-input{
        display: block;
        max-width: 48px;
        padding: .375rem .75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    .c-flex-dop{
        line-height: 40px;
    }
    .c-mr-5{
        margin-right: 5px;
    }
    .c-just-between{
        justify-content: space-between;
    }
    .c-box-btn{
        border: 2px solid #cd203c;
        overflow: hidden;
        max-width: 44px;
        max-height: 44px;
    }
    .c-box-btn>img{
        object-fit: cover;
    }
    
    .c-box-btn.choose{
        border: 2px solid #20cdc5;
    }
    .c-micro-btn{
        display: inline-block;
        min-width: 20px;
        min-height: 20px;
        line-height: 20px;
        text-align: center;
        border-radius: 20px;
        border: 1px solid #000;
        background-color: #999999;
        color: #000;
        font-size: 12px;
    }
    .c-lh-20{
        line-height: 20px;
    }
    
    .c-i-checkbox{
        min-width: 24px;
        min-height: 24px;
        background-size: cover;
    }
    .c-i-checkbox.chbox-off{
        background-image: url('/img/ico/ico_chboxoff.png');
    }
    .c-i-checkbox.chbox-on{
        background-image: url('/img/ico/ico_chboxon.png');
    }
    
</style>
<!-- jquery-->
<script src="/public/testing/js/jquery.min.js"></script>
<!-- Enable shortcut support such as ctrl+z for undo and ctrl+e for export etc-->
<script src="/public/testing/js/jquery.hotkeys.js"></script>


<!-- bootstrap-->
<script src="/public/testing/js/popper.min.js"></script>
<script src="/public/testing/js/bootstrap.min.js"></script>

<!-- builder code-->
<!-- This is the main editor code -->
<script src="/public/testing/libs/builder/builder.js"></script>

<!-- undo manager-->
<script src="/public/testing/libs/builder/undo.js"></script>

<!-- inputs-->
<!-- The inputs library, here is the code for inputs such as text, select etc used for component properties -->
<script src="/public/testing/libs/builder/inputs.js"></script>

<!-- components-->
<!-- Components for Bootstrap 4 group -->
<script src="/public/testing/libs/builder/components-bootstrap4.js"></script>
<!-- Components for Widgets group -->
<script src="/public/testing/libs/builder/components-widgets.js"></script>


<!-- plugins -->

<!-- code mirror libraries - code editor syntax highlighting for html editor -->
<link href="/public/testing/libs/codemirror/lib/codemirror.css" rel="stylesheet"/>
<link href="/public/testing/libs/codemirror/theme/material.css" rel="stylesheet"/>
<script src="/public/testing/libs/codemirror/lib/codemirror.js"></script>
<script src="/public/testing/libs/codemirror/lib/xml.js"></script>
<script src="/public/testing/libs/codemirror/lib/formatting.js"></script>

<!-- code mirror vvveb plugin -->
<!-- replaces default textarea as html code editor with codemirror-->
<script src="/public/testing/libs/builder/plugin-codemirror.js"></script>	
<script>
$(document).ready(function() 
{
	Vvveb.Builder.init('demo/index.html', function() {
		//load code after page is loaded here
		Vvveb.Gui.init();
	});
});
</script>

<div class="constructor-content">
    <input type="hidden" id="material_id" value="<?= $material->exists() ? $material->id() : 0 ?>"/>
    <input type="hidden" id="preview_link" value="<?= $material->exists() && $material->val('preview_link') ? $material->val('preview_link') : md5(microtime()) ?>"/>
    <input type="hidden" id="mnemonic" value="<?= $material->exists() ? $material->val('mnemonic') : '' ?>"/>
    <div class="tpc-wrapper" style="margin-bottom:50px;margin-top: 60px;">
        <div class="tpc-wrapper-left">
            <div class="tpc-left">
                <div class="tpc-container"></div>
            </div>
        </div>
        <div class="tpc-wrapper-right">
            <div class="tpc-right">
                <div class="tpc-tab-headers">
                   <div id="tpc-th-0" class="tpc-tab-header">блоки</div>
                   <div id="tpc-th-1" class="tpc-tab-header">стили</div>
                   <div id="tpc-th-3" class="tpc-tab-header">действия</div>
                   <div id="tpc-th-2" class="tpc-tab-header">настройки</div>
                </div>
                <div class="tpc-tab-contents">
                    <div id="tpc-tc-0" class="tpc-tab-content">
                        <h4>Блоки</h4>
                        <div class="tpc-item tpc-item-div tpc-cols-1">
                            <div class="tpc-item tpc-item-inner tpc-col-1">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                        proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                        </div>
                        <div class="tpc-item tpc-item-div tpc-cols-2">
                            <div class="tpc-item tpc-item-inner tpc-col-1">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</div>
                            <div class="tpc-item tpc-item-inner tpc-col-2">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</div>
                        </div>
                        <div class="tpc-item tpc-item-div tpc-cols-3">
                            <div class="tpc-item tpc-item-inner tpc-col-1">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</div>
                            <div class="tpc-item tpc-item-inner tpc-col-2">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</div>
                            <div class="tpc-item tpc-item-inner tpc-col-3">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</div>
                        </div>
                        <div class="tpc-item tpc-item-div tpc-cols-3 " style="display:none;">
                            <div class="tpc-item tpc-item-inner tpc-col-1">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</div>
                            <div  class="tpc-item tpc-item-inner tpc-col-2x2">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</div>
                        </div>
                        <div style="display:none;" class="tpc-item tpc-item-div tpc-cols-3">
                            <div class="tpc-item tpc-item-inner tpc-col-1x2">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</div>
                            <div class="tpc-item tpc-item-inner tpc-col-2">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</div>
                        </div>
                        <h4>Текст</h4>
                        <div class="tpc-item tpc-item-text tpc-cols-1-wide">
                            <div class="tpc-item tpc-item-inner tpc-col-1 tpc-tiny-mce">Lorem ipsum dolor sit amet...</div>
                            <div class="text-mode text-mode-off"></div>
                        </div>
                        <h4>Изображения/Мультимедиа</h4>
                        <div class="tpc-item tpc-item-multimedia tpc-cols-1">
                            <div class="tpc-item tpc-item-inner tpc-col-1"><img class="tpc-item tpc-item-image" src="/img/photo_block.png" /></div>
                        </div>
                        <div class="tpc-item tpc-item-multimedia tpc-cols-1">
                            <div class="tpc-item tpc-item-inner tpc-col-1"><img class="tpc-item tpc-item-video" src="/img/video_block.png" /></div>
                        </div>
                        <h4>Заголовки</h4>
                        <div class="tpc-item tpc-item-headers tpc-cols-1-wide">
                            <div class="tpc-item tpc-item-inner tpc-col-1"><h2 class="tpc-item tpc-item-header">Lorem ipsum dolor sit amet...</h2></div>
                        </div>
                        <h4>Кнопки</h4>
                        <div class="tpc-item tpc-item-buttons tpc-cols-1-wide">
                            <div class="tpc-item tpc-item-inner tpc-col-1"><button class="tpc-item tpc-item-button">кнопка</button></div>
                        </div>
                        <h4>Разделители</h4>
                        <div class="tpc-item tpc-item-hr tpc-cols-1-wide">
                            <div class="tpc-item tpc-item-inner tpc-col-1">
                                <hr>
                            </div>
                        </div>
                    </div>
                    <div id="tpc-tc-1" class="tpc-tab-content">
                    </div>
                    <div id="tpc-tc-2" class="tpc-tab-content">
                        <h4>Общие</h4>
                        <div class="tpc-settings-block"><label>Заголовок<input type="text" name="name" value="<?= $material ? $material->name() : ''?>"/></label></div>
                        <div class="tpc-settings-block"><label style="display: flex;justify-content: space-between;">Тип блока: 
                            <select id="js-constructor-theme-setter" name="type">
                                <option <?= (isset($material) && $material->val('type')=='material') ? 'selected' : ''?> value="material">Материал</option>
                                <option  <?= (isset($material) && $material->val('type')=='news') ? 'selected' : ''?> value="news">Новость</option>
                                <option  <?= (isset($material) && $material->val('type')=='afisha') ? 'selected' : ''?> value="afisha">Афиша</option>
                            </select>
</label>
                        </div>
                        <div class="tpc-settings-block">
                            <label style="display: none;">
                                <select disabled="disabled" >
                                    <optgroup label="Пусто">
                                    <? $current_type = ''; ?>
                                    <? foreach ($rubrics as $_rubric) {?>
                                        <? if ($current_type !== $_rubric->val('type')) {?>
                                                    </optgroup>
                                                </select>
                                            </label>
                                            <label style="display: none;"><?= $_rubric->types()[$_rubric->val('type')] ?>
                                                <select name="rubricator" class="rubric-<?=$_rubric->val('type')?>">
                                                    <optgroup label="<?= $_rubric->types()[$_rubric->val('type')] ?>">
                                        <? } ?>
                                        <option <?= isset($rubric) && $rubric == $_rubric->id() ? 'selected="selected"' : '' ?> value="<?= $_rubric->id() ?>"><?= $_rubric->name() ?></option>
                                        <? $current_type = $_rubric->val('type'); ?>
                                    <? } ?>
                                    </optgroup>
                                </select>
                            </label>
                        </div>
                        <div class="tpc-settings-block"><label>Анонс<input type="text" name="short_text" value="<?= $material ? $material->val('short_text') : ''?>"/></label></div>
                        <div class="tpc-settings-block"><label>Место проведения (адрес)<input type="text" name="address" value="<?= $material ? $material->val('address') : ''?>"/></label></div>
                        <div class="tpc-settings-block"><label>Организация<input type="text" name="organization" value="<?= $material ? $material->val('organization') : ''?>"/></label></div>
                        
                        <h4>ОСНОВНОЕ ИЗОБРАЖЕНИЕ</h4>
                        <div class="tpc-settings-block">
                            <div style="text-align: center;"><img id="img_previewmaterial" style="max-width:100%;height:auto;" src="" name="img_previewmaterial"/>
                                <input type="text" class="tpc-content-input hidden" id="tpc-file-orig" value="<?= ($material->exists() && $material->image()) ? $material->image()->iconLink('-thumb') : '' ?>"/>
                            </div>
                            <div id="previewmaterial_box"></div>
                            <script>
                                window.onload = function() {
                                    loadImagePreviewMaterial('previewmaterial_box');
                                    console.log('load ok ');
                                    function loadImagePreviewMaterial(idbox) {
                                        console.log('data1');
                                        img_preview = $('#img_previewmaterial');
                                        parent_box = $('#'+idbox);
                                        input_image_hidden = $('<input type="text" value="<?= ($material->exists() && $material->val('image')) ? $material->val('image') : '0'?>" class="tpc-content-input hidden" name="image"/>');
                                        $(parent_box).append(input_image_hidden);        
                                        // Manual url image
                                        var img_box = $('<div class="tpc-content-spacer"></div>')
                                        // Upload image
                                        var upload_wrapper = $('<div class="tpc-content-spacer"></div>');
                                        var upload_file_label = $('<label class="tpc-content-label" for="tpc-file-content">IMG</label>');
                                        var upload_file = $('<input type="file" class="tpc-content-input" id="tpc-file-content" name="tpc-file-content"/>');
                                        $(upload_wrapper).append(upload_file_label).append(upload_file);
                                        var upload_button = $('<button id="tpc-item-upload" class="tpc-item-button wide">Загрузить</button>');
                                        $(upload_wrapper).append(upload_button);

                                        $(upload_button).on('click', function () {
                                            var _this = this;
                                            if ($('#tpc-file-content').prop('files').length === 0) return;

                                            var upload_file_data = $('#tpc-file-content').prop('files')[0];
                                            var upload_form_data = new FormData();
                                            upload_form_data.append('file', upload_file_data);
                                            upload_form_data.append('material_id', $('#material_id').val());
                                            $.ajax({
                                                url: 'https://www.tovaryplus.ru/firm-manager/ajax/upload/material-images/',
                                                dataType: 'json',
                                                cache: false,
                                                contentType: false,
                                                processData: false,
                                                context: _this,
                                                data: upload_form_data,
                                                type: 'post',
                                                success: function (data) {
                                                    if (data) {
                                                        var img = '/img/photo_block.png';
                                                        input_image_hidden.val(data.composite_id);
                                                        if (data.thumb_path) {
                                                            img = data.thumb_path;
                                                        } else {
                                                            input_image_hidden.val(0);
                                                        }
                                                        img_preview.attr('src', img);
                                                    }
                                                },
                                                error: function (response) {
                                                    alert(response);
                                                }
                                            });
                                        });
                                        $(img_box).append(upload_wrapper);

                                        // Select from ImageList
                                        var list_wrapper = $('<div class="tpc-content-spacer"></div>');
                                        var list_label = $('<label class="tpc-content-label" for="tpc-image-list-content" style="text-align: center; width: 100%;">Последние изображения</label>');
                                        var list_contenter = $('<ul class="tpc-content-image-list" id="tpc-image-list-content"><ul/>');
                                        $(list_wrapper).append(list_label).append(list_contenter);
                                        
                                        $(parent_box).append(img_box);
                                        
                                        console.log('data2');
                                        //$('#tpc-tc-1').append(list_wrapper);
                                        //this.getLastImages(item);
                                        
                                        
                                        
                                        var image_list_content = $('<div id="tpc-image-list-content"></div>');
                                        $(parent_box).append(image_list_content);
                                        $('#tpc-image-list-content').html('');
                                        $.ajax({
                                            url: 'https://www.tovaryplus.ru/firm-manager/ajax/image/image-list/',
                                            dataType: 'json',
                                            contentType: false,
                                            type: 'post',
                                            success: function (data) {
                                                if (data) {
                                                    var img = '/img/photo_block.png';
                                                    console.log(data);
                                                    $.each(data, function(i,e) {
                                                        $('#tpc-image-list-content').append('<li src-full="'+ e.full_path +'" id="tpc-image-list-item-' + e.image_id + '"><img src="' + e.thumb_path + '"/><span class="tpc-image-item-text">' + e.name + '.' + e.extension + '</span></li>');
                                                        $('li#tpc-image-list-item-' + e.image_id).click(function () {
                                                            
                                                            input_image_hidden.val(e.composite_id);
                                                            if (e.thumb_path) {
                                                                img = e.thumb_path;
                                                            } else {
                                                                input_image_hidden.val(0);
                                                            }
                                                            img_preview.attr('src', img);
        
                                                        });        
                                                    });
                                                } else {
                                                    console.log('nothing');
                                                }
                                            },
                                            error: function (response) {
                                                alert(response);
                                            }
                                        });
                                        
                                    }
                                    img_preview.attr('src',$('#tpc-file-orig').val());
                                };
                                /*var input_preview = document.getElementById("input_previewmaterial");
                                var img_preview = document.getElementById("img_previewmaterial");
                                var input_preview_val = input_preview.value;
                                if (input_preview_val.length>0){
                                    img_preview.setAttribute('src', input_preview_val);
                                }
                                input_preview.onchange = function() {
                                    input_preview_val = input_preview.value;
                                    img_preview.setAttribute('src', input_preview_val);
                                }*/
                                
                            </script>
                        </div>
                        <div class="tpc-settings-block"><label  style="display: flex;justify-content: space-between;">Популярный

                            <a href="javascript:void(0);" onclick="checkbox_choose(this);" class="c-i-checkbox <?= (isset($material) && $material->val('is_popular')) ? 'chbox-on' : 'chbox-off'?>" data-is='is_popular'></a></label>
                        </div>

                        <div class="tpc-settings-block"><label style="display: flex;justify-content: space-between;">Рекомендуемый 
                            <a href="javascript:void(0);" onclick="checkbox_choose(this);" class="c-i-checkbox <?= (isset($material) && $material->val('is_recommend')) ? 'chbox-on' : 'chbox-off'?>" data-is='is_recommend'></a></label>
                        </div>
                        
                        <div class="tpc-settings-block">
                            <h5 style="text-align: center;">Источник</h5>
                            <label>Название<input type="text" name="material_source_name" value="<?= $material ? $material->val('material_source_name') : ''?>"/></label><br/>
                            <label>Сайт<input type="text" name="material_source_url" value="<?= $material ? $material->val('material_source_url') : ''?>"/></label>
                        </div>
                        <div class="tpc-settings-block"><label>Предупреждения
                            <select name="advert_restrictions">
                                <? foreach($advert_restrictions as $advert_restriction) { ?>
                                    <option value="<?= $advert_restriction ?>"><?= $advert_restriction ?></option>
                                <? } ?>
                            </select></label>
                        </div>
                        <h4>SEO</h4>
                        <div class="tpc-settings-block"><label>Заголовок<input type="text" name="meta_title" value="<?= $material ? $material->val('meta_title') : ''?>"/></label></div>
                        <div class="tpc-settings-block"><label>Ключевые слова<input type="text" name="meta_keywords" value="<?= $material ? $material->val('meta_keywords') : ''?>"/></label></div>
                        <div class="tpc-settings-block"><label>Описание<input type="text" name="meta_description" value="<?= $material ? $material->val('meta_description') : ''?>"/></label></div>
                        <h4>ТЕГИ</h4>
                        <div class="tpc-settings-block"><label><textarea name="tags"></textarea></label></div>
                    </div>
                    <div id="tpc-tc-3" class="tpc-tab-content">
                        <h4>Удаление</h4>
                        <div class="tpc-item tpc-item-trash-can ui-droppable"></div>
                        <h4>Шаблон</h4>
                        <button class="tpc-item-button tpc-load-template-button" style="display: none;">загрузить</button>
                        <button class="tpc-item-button tpc-save-template-button">сохранить</button>
                        <? if ($material->exists()) {?>
                            <button class="tpc-item-button tpc-view-template-button">посмотреть</button>
                        <? } ?>
                        <? if ($material->exists()) {?>
                            <? if ($material->isPublished()) {?>
                                <button class="tpc-item-button tpc-unpublish-template-button">закрыть</button>
                                <button class="tpc-item-button tpc-publish-template-button" style="display: none;">опубликовать</button>
                            <? } else {?>
                                <button class="tpc-item-button tpc-unpublish-template-button" style="display: none;">закрыть</button>
                                <button class="tpc-item-button tpc-publish-template-button">опубликовать</button>
                            <? } ?>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                    
<script>
    
</script>
                    