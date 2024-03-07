<script>
    sessionStorage.setItem('tpc-template',
<?= $material->exists() ? 'JSON.stringify(' . $material->val('constructor_data') . ')' : '\'\'' ?>
    );
    console.log('view2');
</script>
123
 jquery
<script src="/public/testing/js/jquery.min.js"></script>
 Enable shortcut support such as ctrl+z for undo and ctrl+e for export etc
<script src="/public/testing/js/jquery.hotkeys.js"></script>


 bootstrap
<script src="/public/testing/js/popper.min.js"></script>
<script src="/public/testing/js/bootstrap.min.js"></script>

 builder code
 This is the main editor code 
<script src="/public/testing/libs/builder/builder.js"></script>

 undo manager
<script src="/public/testing/libs/builder/undo.js"></script>

 inputs
 The inputs library, here is the code for inputs such as text, select etc used for component properties 
<script src="/public/testing/libs/builder/inputs.js"></script>

 components
 Components for Bootstrap 4 group 
<script src="/public/testing/libs/builder/components-bootstrap4.js"></script>
 Components for Widgets group 
<script src="/public/testing/libs/builder/components-widgets.js"></script>


 plugins 

 code mirror libraries - code editor syntax highlighting for html editor 
<link href="/public/testing/libs/codemirror/lib/codemirror.css" rel="stylesheet"/>
<link href="/public/testing/libs/codemirror/theme/material.css" rel="stylesheet"/>
<script src="/public/testing/libs/codemirror/lib/codemirror.js"></script>
<script src="/public/testing/libs/codemirror/lib/xml.js"></script>
<script src="/public/testing/libs/codemirror/lib/formatting.js"></script>

 code mirror vvveb plugin 
 replaces default textarea as html code editor with codemirror
<script src="/public/testing/libs/builder/plugin-codemirror.js"></script>	
<script>
$(document).ready(function() 
{
	Vvveb.Builder.init('/public/testing/demo/material/loadhtml.php?id=181', function() {
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
                        <div class="tpc-item tpc-item-div tpc-cols-1" style="display:none;">
                            <div class="tpc-item tpc-item-inner tpc-col-1">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                                cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                                proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                        </div>
                        
                        <style>
                            .tpc-item-quote{
                                padding: 0px!important;
                            }
                            .tpc-box-quote{
                                font-size: 16px;
                                font-style: italic;
                                line-height: 1.5;
                                padding: 8px 10px 8px 20px!important;
                                border-left: 3px solid #000!important;
                                background-color: transparent!important;
                            }
                            .tpc-quote-parent{
                                padding: 5px;
                            }
                            .quote-author{
                                width: 100%;
                                text-align: right;
                                font-weight: bold;
                                display: block;
                            }
                        </style>
                        
                        <div class="tpc-item tpc-item-quote tpc-quote">
                            <div class="tpc-item-inner tpc-quote-parent">
                                <div class="tpc-item tpc-box-quote">
                                    <span class="text-quote">
                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                        consequat.
                                    </span>
                                    <span class="quote-author">- Автор А.А. 1996г</span>
                                </div>
                            </div>
                        </div>
                        <style>
                            .tpc-item-afisha{
                                display:flex!important;
                            }
                            .tpc-item-afisha>.tp-mt-afisha-imgbox{
                                max-width: 30%;
                                position: relative;
                                display: flex;
                            }
                            .tpc-item-afisha>.tp-mt-afisha-imgbox>img{
                                object-fit: cover;
                                max-width: 100%;
                            }
                            .tp-mt-afisha-imgbox>.tp-mt-afisha-age{
                                bottom: 0;
                                text-align: center;
                                color: #FFFFFF;
                                font-weight: bold;
                                padding-top: 5px;
                                padding-bottom: 5px;
                                width: 100%;
                                position: absolute;
                                background-color: #699bff;
                            }


                            .tpc-item-afisha>.tp-mt-afisha-text{
                                display: flex;
                                flex-direction: column;
                                min-height: 100%;
                                width: 100%;
                            }
                            .tp-mt-afisha-text-body{
                                flex: 1 0 auto;
                            }
                            .tp-mt-afisha-text-date{
                                flex: 0 0 auto;
                            }
                            .tp-mt-afisha-text>.tp-mt-afisha-text-header{
                                background-color: #699bff;
                                min-height: 32px;
                                text-transform: uppercase;
                                display: flex;
                            }
                            .tp-mt-afisha-text-header>.tp-mt-afisha-text-header-content{
                                margin-top: auto;
                                margin-bottom: auto;
                                color: #FFFFFF;
                            }
                            .tp-mt-afisha-text>div{
                                padding: 5px;
                            }
                            .blur-box{
                                background: rgb(0 0 0 / 20%);
                                backdrop-filter: blur(8px);
                                height: 100%;
                                width: 100%;
                                z-index: -1;
                                position: absolute;
                            }
                        </style>
                        <div class="tpc-item tpc-item-afishadiv tpc-afisha-1">
                            <div class="tpc-item tpc-item-afisha">
                                <div class="tp-mt-afisha-imgbox">
                                    <div class="blur-box"></div>
                                    <img class="img-fluid tp-mt-afisha-img" src="/img/photo_block.png" />
                                    <div class="tp-mt-afisha-age">12+</div>
                                </div>
                                <div class="tp-mt-afisha-text mt-modal-afisha">
                                    <div class="tp-mt-afisha-text-header">
                                        <div class="tp-mt-afisha-text-header-content">
                                            Премьера. "Я - ворона" (история из жизни людей и птиц)
                                        </div>
                                    </div>
                                    <div class="tp-mt-afisha-text-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                        consequat.
                                    </div>
                                    <div class="tp-mt-afisha-text-date">
                                        - 10 августа  по 4 октября
                                    </div>
                                </div>
                            </div>
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
                        123
                    </div>
                    <div id="tpc-tc-2" class="tpc-tab-content">
                        <h4>Общие</h4>
                        <div class="tpc-settings-block"><label>Заголовок<input type="text" name="name" value="<?= $material ? $material->name() : '' ?>"/></label></div>
                        <div class="tpc-settings-block"><label style="display: flex;justify-content: space-between;">Тип блока: 
                                <select id="js-constructor-theme-setter" name="type">
                                    <option <?= (isset($material) && $material->val('type') == 'material') ? 'selected' : '' ?> value="material">Материал</option>
                                    <option  <?= (isset($material) && $material->val('type') == 'news') ? 'selected' : '' ?> value="news">Новость</option>
                                    <option  <?= (isset($material) && $material->val('type') == 'afisha') ? 'selected' : '' ?> value="afisha">Афиша</option>
                                </select>
                            </label>
                        </div>
                        <div class="tpc-settings-block">
                            <label style="display: none;">
                                <select disabled="disabled" >
                                    <optgroup label="Пусто">
                                        <? $current_type = ''; ?>
                                        <? foreach ($rubrics as $_rubric) { ?>
                                            <? if ($current_type !== $_rubric->val('type')) { ?>
                                            </optgroup>
                                        </select>
                                    </label>
                                    <label style="display: none;"><?= $_rubric->types()[$_rubric->val('type')] ?>
                                        <select name="rubricator" class="rubric-<?= $_rubric->val('type') ?>">
                                            <optgroup label="<?= $_rubric->types()[$_rubric->val('type')] ?>">
                                            <? } ?>
                                            <option <?= isset($rubric) && $rubric == $_rubric->id() ? 'selected="selected"' : '' ?> value="<?= $_rubric->id() ?>"><?= $_rubric->name() ?></option>
                                            <? $current_type = $_rubric->val('type'); ?>
                                        <? } ?>
                                    </optgroup>
                                </select>
                            </label>
                        </div>
                        <div class="tpc-settings-block"><label>Анонс<input type="text" name="short_text" value="<?= $material ? $material->val('short_text') : '' ?>"/></label></div>
                        <div class="tpc-settings-block"><label>Место проведения (адрес)<input type="text" name="address" value="<?= $material ? $material->val('address') : '' ?>"/></label></div>
                        <div class="tpc-settings-block"><label>Организация<input type="text" name="organization" value="<?= $material ? $material->val('organization') : '' ?>"/></label></div>

                        <h4>ОСНОВНОЕ ИЗОБРАЖЕНИЕ</h4>
                        <div class="tpc-settings-block">
                            <div style="text-align: center;"><img id="img_previewmaterial" style="max-width:100%;height:auto;" src="" name="img_previewmaterial"/>
                                <input type="text" class="tpc-content-input hidden" id="tpc-file-orig" value="<?= ($material->exists() && $material->image()) ? $material->image()->iconLink('-thumb') : '' ?>"/>
                            </div>
                            <div id="previewmaterial_box"></div>
                            <script>
                                window.onload = function () {
                                    loadImagePreviewMaterial('previewmaterial_box');
                                    console.log('load ok ');
                                    function loadImagePreviewMaterial(idbox) {
                                        console.log('data1');
                                        img_preview = $('#img_previewmaterial');
                                        parent_box = $('#' + idbox);
                                        input_image_hidden = $('<input type="text" value="<?= ($material->exists() && $material->val('image')) ? $material->val('image') : '0' ?>" class="tpc-content-input hidden" name="image"/>');
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
                                            if ($('#tpc-file-content').prop('files').length === 0)
                                                return;

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
                                                    $.each(data, function (i, e) {
                                                        $('#tpc-image-list-content').append('<li src-full="' + e.full_path + '" id="tpc-image-list-item-' + e.image_id + '"><img src="' + e.thumb_path + '"/><span class="tpc-image-item-text">' + e.name + '.' + e.extension + '</span></li>');
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
                                    img_preview.attr('src', $('#tpc-file-orig').val());
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

                                <a href="javascript:void(0);" onclick="checkbox_choose(this);" class="c-i-checkbox <?= (isset($material) && $material->val('is_popular')) ? 'chbox-on' : 'chbox-off' ?>" data-is='is_popular'></a></label>
                        </div>

                        <div class="tpc-settings-block"><label style="display: flex;justify-content: space-between;">Рекомендуемый 
                                <a href="javascript:void(0);" onclick="checkbox_choose(this);" class="c-i-checkbox <?= (isset($material) && $material->val('is_recommend')) ? 'chbox-on' : 'chbox-off' ?>" data-is='is_recommend'></a></label>
                        </div>

                        <div class="tpc-settings-block">
                            <h5 style="text-align: center;">Источник</h5>
                            <label>Название<input type="text" name="material_source_name" value="<?= $material ? $material->val('material_source_name') : '' ?>"/></label><br/>
                            <label>Сайт<input type="text" name="material_source_url" value="<?= $material ? $material->val('material_source_url') : '' ?>"/></label>
                        </div>
                        <div class="tpc-settings-block"><label>Предупреждения
                                <select name="advert_restrictions">
                                    <? foreach ($advert_restrictions as $advert_restriction) { ?>
                                        <option value="<?= $advert_restriction ?>" <?
                                        if ($material) { 
                                            if ($material->val('advert_restrictions')==$advert_restriction){
                                                echo "selected";
                                            }
                                            
                                        }
                                            ?>><?= $advert_restriction ?></option>
                                    <? } ?>
                                </select></label>
                        </div>
                        <h4>SEO</h4>
                        <div class="tpc-settings-block"><label>Заголовок<input type="text" name="meta_title" value="<?= $material ? $material->val('meta_title') : '' ?>"/></label></div>
                        <div class="tpc-settings-block"><label>Ключевые слова<input type="text" name="meta_keywords" value="<?= $material ? $material->val('meta_keywords') : '' ?>"/></label></div>
                        <div class="tpc-settings-block"><label>Описание<input type="text" name="meta_description" value="<?= $material ? $material->val('meta_description') : '' ?>"/></label></div>
                        <h4>ТЕГИ</h4>
                        <div class="tpc-settings-block"><label><textarea name="tags"><?= $material ? $material->val('tags') : '' ?></textarea></label></div>
                    </div>
                    <div id="tpc-tc-3" class="tpc-tab-content">
                        <h4>Удаление</h4>
                        <div class="tpc-item tpc-item-trash-can ui-droppable"></div>
                        <h4>Шаблон</h4>
                        <button class="tpc-item-button tpc-load-template-button" style="display: none;">загрузить</button>
                        <button class="tpc-item-button tpc-save-template-button">сохранить</button>
                        <? if ($material->exists()) { ?>
                                <button class="tpc-item-button tpc-view-template-button">посмотреть</button>
                        <? } ?>
                        <? if ($material->exists()) { ?>
                            <? if ($material->isPublished()) { ?>
                                        <button class="tpc-item-button tpc-unpublish-template-button">закрыть</button>
                                        <button class="tpc-item-button tpc-publish-template-button" style="display: none;">опубликовать</button>
                            <? } else { ?>
                                        <button class="tpc-item-button tpc-unpublish-template-button" style="display: none;">закрыть</button>
                                        <button class="tpc-item-button tpc-publish-template-button">опубликовать</button>
                            <? } ?>
                                    <a class="tpc-item-button tpc-green-btn" target="_blank" href="<?= $material->link(); ?>">Посмотреть на сайте</a>
                        <? } ?>
                                
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<style>
    .right-box{
        position: fixed;
        display: flex;
        flex-direction: column;
        height: 100vh;
        width: 512px;
        background-color: #f3e2e5;
        right: -512px;
        top: 0;
        z-index: 9999;
    }
    
    .const-right-box .const-container{
        display: flex;
        position: relative;
        flex-direction: column;
        margin-top: auto;
        margin-bottom: auto;
        margin-left: 20px;
        margin-right: 20px;
    }
    .const-w-100{
        width: 100%;
    }
    .const-mt-20{
        margin-top: 20px;
    }
    .const-container input, .const-container textarea{
        width: 100%;
    }
</style>

<div id="AfishaModal" class="right-box const-right-box" style="overflow-y: scroll;">
    <div class="const-container">
        ТЕКСТ
        <div class="const-w-100 const-mt-20">
            <label><b>Наименование:</b></label>
            <input id="edit-afisha-zag" name="" placeholder="Заголовок">
        </div>
        <div class="const-w-100 const-mt-20">
            <label><b>Текст:</b></label>
            <div id="e-a-t-box">
                <textarea id="edit-afisha-text"></textarea>
            </div>
        </div>
        <div class="const-w-100 const-mt-20">
            <label><b>Дата:</b></label>
            <div id="e-a-f-box">
                <textarea id="edit-afisha-footer"></textarea>
            </div>
        </div>
        <div class="const-w-100 const-mt-20">
            <label><b>Возраст:</b></label>
            <input id="edit-afisha-age" name="" placeholder="12+">
        </div>
        <div class="const-w-100 const-mt-20">
            <a class="tpc-item-button tpc-green-btn" href="javascript:void(0);" onclick="close_modal_afisha();">Закрыть</a> 
        </div>
    </div>
</div>




<script>
    function open_modal_afisha(){
        $('#AfishaModal').animate({
	      right: "0",
	      opacity: 1
	    });
    }
    function close_modal_afisha(){
        $('#AfishaModal').animate({
	      right: "-512px",
	      opacity: 0
	    });
        tinyMCE.get('edit-afisha-text').remove();
        $('#edit-afisha-text').remove();
        $('#e-a-t-box').append($('<textarea id="edit-afisha-text"></textarea>'));
        tinyMCE.get('edit-afisha-footer').remove();
        $('#edit-afisha-footer').remove();
        $('#e-a-f-box').append($('<textarea id="edit-afisha-footer"></textarea>'));
    }
</script>



                    

<!--

	<div id="vvveb-builder">
				
				<div id="top-panel">
					<img src="img/logo.png" alt="Vvveb" class="float-start" id="logo">
					
					<div class="btn-group float-start" role="group">
					  <button class="btn btn-light" title="Toggle file manager" id="toggle-file-manager-btn" data-vvveb-action="toggleFileManager" data-bs-toggle="button" aria-pressed="false">
						  <img src="libs/builder/icons/file-manager-layout.svg" width="20px" height="20px">
					  </button>

					  <button class="btn btn-light" title="Toggle left column" id="toggle-left-column-btn" data-vvveb-action="toggleLeftColumn" data-bs-toggle="button" aria-pressed="false">
						  <img src="libs/builder/icons/left-column-layout.svg" width="20px" height="20px">
					  </button>
					  
					  <button class="btn btn-light" title="Toggle right column" id="toggle-right-column-btn" data-vvveb-action="toggleRightColumn" data-bs-toggle="button" aria-pressed="false">
						  <img src="libs/builder/icons/right-column-layout.svg" width="20px" height="20px">
					  </button>
					</div>
					
					<div class="btn-group me-3" role="group">
					  <button class="btn btn-light" title="Undo (Ctrl/Cmd + Z)" id="undo-btn" data-vvveb-action="undo" data-vvveb-shortcut="ctrl+z">
						  <i class="la la-undo"></i>
					  </button>

					  <button class="btn btn-light la-flip-horizontal"  title="Redo (Ctrl/Cmd + Shift + Z)" id="redo-btn" data-vvveb-action="redo" data-vvveb-shortcut="ctrl+shift+z">
						  <i class="la la-undo"></i>
					  </button>
					</div>
										
					
					<div class="btn-group me-3" role="group">
					  <button class="btn btn-light" title="Designer Mode (Free component dragging)" id="designer-mode-btn" data-bs-toggle="button" aria-pressed="false" data-vvveb-action="setDesignerMode">
						  <i class="la la-hand-rock"></i>
					  </button>

					  <button class="btn btn-light" title="Preview" id="preview-btn" type="button" data-bs-toggle="button" aria-pressed="false" data-vvveb-action="preview">
						  <i class="la la-eye"></i>
					  </button>

					  <button class="btn btn-light" title="Fullscreen (F11)" id="fullscreen-btn" data-bs-toggle="button" aria-pressed="false" data-vvveb-action="fullscreen">
						  <i class="la la-expand-arrows-alt"></i>
					  </button>

					  <button class="btn btn-light" title="Download" id="download-btn" data-vvveb-action="download" data-v-download="index.html">
						  <i class="la la-download"></i>
					  </button>

					</div>
					
								
					<div class="btn-group me-3 float-end" role="group">
					  <button class="btn btn-primary btn-icon" title="Export (Ctrl + E)" id="save-btn" data-vvveb-action="saveAjax" data-vvveb-url="save.php" data-v-vvveb-shortcut="ctrl+e">
						  <i class="la la-save"></i> <span data-v-gettext>Save page</span>
					  </button>
					</div>	

					<div class="btn-group float-end me-3 responsive-btns" role="group">
		 			 <button id="mobile-view" data-view="mobile" class="btn btn-light"  title="Mobile view" data-vvveb-action="viewport">
						  <i class="la la-mobile"></i>
					  </button>

					  <button id="tablet-view"  data-view="tablet" class="btn btn-light"  title="Tablet view" data-vvveb-action="viewport">
						  <i class="la la-tablet"></i>
					  </button>
					  
					  <button id="desktop-view"  data-view="" class="btn btn-light"  title="Desktop view" data-vvveb-action="viewport">
						  <i class="la la-laptop"></i>
					  </button>

					</div>
										
				</div>	
				
				<div id="left-panel">

					  <div id="filemanager"> 
							<div class="header">
								<a href="#" class="text-secondary">Страницы</a>

									<div class="btn-group responsive-btns me-4 float-end" role="group">
									  <button class="btn btn-link btn-sm" title="New file" id="new-file-btn" data-vvveb-action="newPage" data-vvveb-shortcut="">
										  <i class="la la-file"></i> <small>Новая страница</small>
									  </button>
									  
									    &ensp;
									  <button class="btn btn-link text-dark p-0"  title="Delete file" id="delete-file-btn" data-vvveb-action="deletePage" data-vvveb-shortcut="">
										  <i class="la la-trash"></i> <small>Delete</small>
									  </button> 
									</div>

								</div>

								<div class="tree">
									<ol>
									</ol>
								</div>
					  </div>
					  
					  
					 <div class="drag-elements">
						
						<div class="header">
							<ul class="nav nav-tabs  nav-fill" id="elements-tabs" role="tablist">
							  <li class="nav-item sections-tab">
								<a class="nav-link active" id="sections-tab" data-bs-toggle="tab" href="#sections" role="tab" aria-controls="sections" aria-selected="true" title="Sections">
									<i class="la la-stream"></i>
									 img src="../../../js/vvvebjs/icons/list_group.svg" height="23"  
									 div><small>Sections</small></div 
								</a>
							  </li>
							  <li class="nav-item component-tab">
								<a class="nav-link" id="components-tab" data-bs-toggle="tab" href="#components-tabs" role="tab" aria-controls="components" aria-selected="false" title="Components">
									<i class="la la-box"></i>
									 img src="../../../js/vvvebjs/icons/product.svg" height="23"  
									 div><small>Components</small></div 
								</a>
							  </li>
							   li class="nav-item sections-tab">
								<a class="nav-link" id="sections-tab" data-bs-toggle="tab" href="#sections" role="tab" aria-controls="sections" aria-selected="false" title="Sections"><img src="../../../js/vvvebjs/icons/list_group.svg" width="24" height="23"> <div><small>Sections</small></div></a>
							  </li 
							  <li class="nav-item component-properties-tab" style="display:none">
								<a class="nav-link" id="properties-tab" data-bs-toggle="tab" href="#properties" role="tab" aria-controls="properties" aria-selected="false" title="Properties">
									<i class="la la-cog"></i>
									 img src="../../../js/vvvebjs/icons/filters.svg" height="23" 
									 div><small>Properties</small></div 
								</a>
							  </li>
							  <li class="nav-item component-configuration-tab">
								<a class="nav-link" id="configuration-tab" data-bs-toggle="tab" href="#configuration" role="tab" aria-controls="configuration" aria-selected="false" title="Configuration">
									<i class="la la-tools"></i>
									 img src="../../../js/vvvebjs/icons/filters.svg" height="23" 
									 div><small>Properties</small></div 
								</a>
							  </li>
							</ul>
					
							<div class="tab-content">
							  
							  
							  <div class="tab-pane fade show active sections" id="sections" role="tabpanel" aria-labelledby="sections-tab">
								  

										<ul class="nav nav-tabs nav-fill sections-tabs" id="properties-tabs" role="tablist">
										  <li class="nav-item content-tab">
											<a class="nav-link active" data-bs-toggle="tab" href="#sections-new-tab" role="tab" aria-controls="components" aria-selected="false">
												<i class="la la-plus"></i> <div><span>Добавить блок</span></div></a>
										  </li>
										  <li class="nav-item style-tab">
											<a class="nav-link" data-bs-toggle="tab" href="#sections-list" role="tab" aria-controls="sections" aria-selected="true">
												<i class="la la-th-list"></i> <div><span>Блоки страницы</span></div></a>
										  </li>
										</ul>
								
										<div class="tab-content">
		
											 <div class="tab-pane fade" id="sections-list" data-section="style" role="tabpanel" aria-labelledby="style-tab">
												<div class="drag-elements-sidepane sidepane">
												  <div>
													<div class="sections-container">
																													  
															<div class="section-item" draggable="true">
																<div class="controls">
																	<div class="handle"></div>
																	<div class="info">
																		<div class="name">&nbsp;
																			<div class="type">&nbsp;</div>
																		</div>
																	</div>
																</div>
															</div> 
															<div class="section-item" draggable="true">
																<div class="controls">
																	<div class="handle"></div>
																	<div class="info">
																		<div class="name">&nbsp;
																			<div class="type">&nbsp;</div>
																		</div>
																	</div>
																</div>
															</div> 
															<div class="section-item" draggable="true">
																<div class="controls">
																	<div class="handle"></div>
																	<div class="info">
																		<div class="name">&nbsp;
																			<div class="type">&nbsp;</div>
																		</div>
																	</div>
																</div>
															</div> 
															 div class="section-item" draggable="true">
																<div class="controls">
																	<div class="handle"></div>
																	<div class="info">
																		<div class="name">welcome area
																			<div class="type">section</div>
																		</div>
																	</div>
																	<div class="buttons"> <a class="delete-btn" href="" title="Remove section"><i class="la la-trash text-danger"></i></a>
																		
																		<a class="properties-btn" href="" title="Properties"><i class="la la-cog"></i></a> </div>
																</div>
																<input class="header_check" type="checkbox" id="section-components-9338">
																<label for="section-components-9338">
																	<div class="header-arrow"></div>
																</label>
																<div class="tree">
																	<ol></ol>
																</div>
															</div  
																											
															
													  </div>
													</div>
												</div>
											</div>
											
											<div class="tab-pane fade show active" id="sections-new-tab" data-section="content" role="tabpanel" aria-labelledby="content-tab">


													   <div class="search">
															  <input class="form-control form-control-sm block-search" placeholder="Search sections" type="text" data-vvveb-action="sectionSearch" data-vvveb-on="keyup">
															  <button class="clear-backspace"  data-vvveb-action="clearSectionSearch">
																  <i class="la la-times"></i>
															  </button>
														</div>

											  
														<div class="drag-elements-sidepane sidepane">
															  <div class="block-preview"><img src=""></div>
															  <div>
																<ul class="sections-list clearfix" data-type="leftpanel">
																</ul>

															  </div>
														</div>

											</div>
											
										</div>
							
							  </div>
							
								<div class="tab-pane fade show" id="components-tabs" role="tabpanel" aria-labelledby="components-tab">
								  
								  
										<ul class="nav nav-tabs nav-fill sections-tabs" role="tablist">
										  <li class="nav-item components-tab">
											<a class="nav-link active" data-bs-toggle="tab" href="#components" role="tab" aria-controls="components" aria-selected="true">
												<i class="la la-box"></i> <div><span>Элементы</span></div></a>
										  </li>
										  <li class="nav-item blocks-tab">
											<a class="nav-link" data-bs-toggle="tab" href="#blocks" role="tab" aria-controls="components" aria-selected="false">
												<i class="la la-copy"></i> <div><span>Готовые блоки</span></div></a>
										  </li>
										</ul>
								
										<div class="tab-content">
		
											 <div class="tab-pane fade show active components" id="components" data-section="components" role="tabpanel" aria-labelledby="components-tab">
												 
												   <div class="search">
														  <input class="form-control form-control-sm component-search" placeholder="Search components" type="text" data-vvveb-action="componentSearch" data-vvveb-on="keyup">
														  <button class="clear-backspace"  data-vvveb-action="clearComponentSearch">
															  <i class="la la-times"></i>
															</button>
													</div>

													<div class="drag-elements-sidepane sidepane">	
														 <div>
														  
														<ul class="components-list clearfix" data-type="leftpanel">
														</ul>

													</div>											 
												</div>
											</div>

											
											
											<div class="tab-pane fade show active blocks" id="blocks" data-section="content" role="tabpanel" aria-labelledby="content-tab">

													   <div class="search">
															  <input class="form-control form-control-sm block-search" placeholder="Search blocks" type="text" data-vvveb-action="blockSearch" data-vvveb-on="keyup">
															  <button class="clear-backspace"  data-vvveb-action="clearBlockSearch">
																  <i class="la la-times"></i>
															  </button>
														</div>

											  
														<div class="drag-elements-sidepane sidepane">
															  <div class="block-preview"><img src=""></div>
															  <div>
																<ul class="blocks-list clearfix" data-type="leftpanel">
																</ul>

															  </div>
														</div>
											</div>
											
										</div>
							</div>

								<div class="tab-pane fade" id="properties" role="tabpanel" aria-labelledby="properties-tab">
									<div class="component-properties-sidepane">
										<div>
											<div class="component-properties">
												<ul class="nav nav-tabs nav-fill" id="properties-tabs" role="tablist">
													  <li class="nav-item content-tab">
														<a class="nav-link active" data-bs-toggle="tab" href="#content-left-panel-tab" role="tab" aria-controls="components" aria-selected="true">
															<i class="la la-lg la-sliders-h"></i> <div><span>Content</span></div></a>
													  </li>
													  <li class="nav-item style-tab">
														<a class="nav-link" data-bs-toggle="tab" href="#style-left-panel-tab" role="tab" aria-controls="style" aria-selected="false">
															<i class="la la-lg la-paint-brush"></i> <div><span>Style</span></div></a>
													  </li>
													  <li class="nav-item advanced-tab">
														<a class="nav-link" data-bs-toggle="tab" href="#advanced-left-panel-tab" role="tab" aria-controls="advanced" aria-selected="false">
															<i class="la la-lg la-tools"></i> <div><span>Advanced</span></div></a>
													  </li>
													</ul>
											
													<div class="tab-content">
														 <div class="tab-pane fade show active" id="content-left-panel-tab" data-section="content" role="tabpanel" aria-labelledby="content-tab">
															<div class="alert alert-dismissible fade show alert-light m-3" role="alert" style="">		  
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>		  
																<strong>Не выбран элемент!!</strong><br> Выберите элемент, чтобы его отредактировать!		
															</div>
														</div>
														
														 <div class="tab-pane fade show" id="style-left-panel-tab" data-section="style" role="tabpanel" aria-labelledby="style-tab">
														</div>
														
														 <div class="tab-pane fade show" id="advanced-left-panel-tab" data-section="advanced"  role="tabpanel" aria-labelledby="advanced-tab">
															<div class="alert alert-dismissible fade show alert-info m-3" role="alert" style="">		  
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>		  
																<strong>Нет расширенных настроек!</strong><br> У этого компонента нет расширенных настроек!		
															</div>
														</div>
													</div>
											</div>
										</div>
									</div>
							  </div>
							
								<div class="tab-pane fade" id="configuration" role="tabpanel" aria-labelledby="configuration-tab">
									
									 color palette 
									<label class="header" data-header="default" for="header_pallette"><span>Палитра</span>
										<div class="header-arrow"></div>
									</label>
									<input class="header_check" type="checkbox" checked="true" id="header_pallette">
									<div class="section" data-section="default">

										
									</div>
										
										
									 typography 	
									<label class="header" data-header="element_header" for="header_element_typography"><span>Настройки текста</span>
										<div class="header-arrow"></div>
									</label>
									
									<input class="header_check" type="checkbox" checked="true" id="header_element_typography">
									<div class="section" data-section="element_header">
										
										
									</div>
									
								
								</div> end configuration tab 
							
							</div>
						</div>							
					
					  </div>
				</div>	


				<div id="canvas">
					<div id="iframe-wrapper">
						<div id="iframe-layer">
							
							<div class="loading-message active">
									<div class="animation-container">
									  <div class="dot dot-1"></div>
									  <div class="dot dot-2"></div>
									  <div class="dot dot-3"></div>
									</div>

									<svg xmlns="http://www.w3.org/2000/svg" version="1.1">
									  <defs>
										<filter id="goo">
										  <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
										  <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 21 -7"/>
										</filter>
									  </defs>
									</svg>
									 https://codepen.io/Izumenko/pen/MpWyXK 
							</div>
							
							<div id="highlight-box">
								<div id="highlight-name"></div>
								
								<div id="section-actions">
									<a id="add-section-btn" href="" title="Add element"><i class="la la-plus"></i></a>
								</div>
							</div>

							<div id="select-box">

								<div id="wysiwyg-editor">
										<a id="bold-btn" href="" title="Bold"><i class="la la-bold"></i></a>
										<a id="italic-btn" href="" title="Italic"><i class="la la-italic"></i></a>
										<a id="underline-btn" href="" title="Underline"><i class="la la-underline"></i></a>
										<a id="strike-btn" href="" title="Strikeout"><del>S</del></a>
										<a id="link-btn" href="" title="Create link"><i class="la la-link"></i></a>
										
										<div class="dropdown">
										  <a class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<i class="la la-align-left"></i>
										  </a>

											  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
												<a class="dropdown-item" href="#"><i class="la la-lg la-align-left"></i> Align Left</a>
												<a class="dropdown-item" href="#"><i class="la la-lg la-align-center"></i> Align Center</a>
												<a class="dropdown-item" href="#"><i class="la la-lg la-align-right"></i> Align Right</a>
												<a class="dropdown-item" href="#"><i class="la la-lg la-align-justify"></i> Align Justify</a>
											  </div>
										</div>
										
										<input name="color" type="color" pattern="#[a-f0-9]{6}" class="form-control form-control-color">
										
										<select class="form-select">
											<option value="">Default</option>
											<option value="Arial, Helvetica, sans-serif">Arial</option>
											<option value="'Lucida Sans Unicode', 'Lucida Grande', sans-serif">Lucida Grande</option>
											<option value="'Palatino Linotype', 'Book Antiqua', Palatino, serif">Palatino Linotype</option>
											<option value="'Times New Roman', Times, serif">Times New Roman</option>
											<option value="Georgia, serif">Georgia, serif</option>
											<option value="Tahoma, Geneva, sans-serif">Tahoma</option>
											<option value="'Comic Sans MS', cursive, sans-serif">Comic Sans</option>
											<option value="Verdana, Geneva, sans-serif">Verdana</option>
											<option value="Impact, Charcoal, sans-serif">Impact</option>
											<option value="'Arial Black', Gadget, sans-serif">Arial Black</option>
											<option value="'Trebuchet MS', Helvetica, sans-serif">Trebuchet</option>
											<option value="'Courier New', Courier, monospace">Courier New</option>
											<option value="'Brush Script MT', sans-serif">Brush Script</option>
										</select>
								</div>

								<div id="select-actions">
									<a id="drag-btn" href="" title="Drag element"><i class="la la-arrows-alt"></i></a>
									<a id="parent-btn" href="" title="Select parent" class="la-rotate-180"><i class="la la-level-up-alt"></i></a>
									
									<a id="up-btn" href="" title="Move element up"><i class="la la-arrow-up"></i></a>
									<a id="down-btn" href="" title="Move element down"><i class="la la-arrow-down"></i></a>
									<a id="clone-btn" href="" title="Clone element"><i class="la la-copy"></i></a>
									<a id="delete-btn" href="" title="Remove element"><i class="la la-trash"></i></a>
								</div>
							</div>
							
							 add section box 
							<div id="add-section-box" class="drag-elements">

									<div class="header">							
										<ul class="nav nav-tabs" id="box-elements-tabs" role="tablist">
										  <li class="nav-item component-tab">
											<a class="nav-link active" id="box-components-tab" data-bs-toggle="tab" href="#box-components" role="tab" aria-controls="components" aria-selected="true"><i class="la la-lg la-cube"></i> <div><small>Компоненты</small></div></a>
										  </li>
										  <li class="nav-item sections-tab">
											<a class="nav-link" id="box-sections-tab" data-bs-toggle="tab" href="#box-blocks" role="tab" aria-controls="blocks" aria-selected="false"><i class="la la-lg la-image"></i> <div><small>Блоки</small></div></a>
										  </li>
										  <li class="nav-item component-properties-tab" style="display:none">
											<a class="nav-link" id="box-properties-tab" data-bs-toggle="tab" href="#box-properties" role="tab" aria-controls="properties" aria-selected="false"><i class="la la-lg la-cog"></i> <div><small>Настройки</small></div></a>
										  </li>
										</ul>
										
										<div class="section-box-actions">

											<div id="close-section-btn" class="btn btn-light btn-sm bg-white btn-sm float-end"><i class="la la-times"></i></div>
										
											<div class="small mt-1 me-3 float-end">
											
												<div class="d-inline me-2">
												  <input type="radio" id="add-section-insert-mode-after" value="after" checked="checked" name="add-section-insert-mode" class="form-check-input">
												  <label class="form-check-label" for="add-section-insert-mode-after">После</label>
												</div>
												
												<div class="d-inline">
												  <input type="radio" id="add-section-insert-mode-inside" value="inside" name="add-section-insert-mode" class="form-check-input">
												  <label class="form-check-label" for="add-section-insert-mode-inside">Внутри</label>
												</div>
										
											</div>
											
										</div>
										
										<div class="tab-content">
										  <div class="tab-pane fade show active" id="box-components" role="tabpanel" aria-labelledby="components-tab">
											  
											   <div class="search">
													  <input class="form-control form-control-sm component-search" placeholder="Search components" type="text" data-vvveb-action="addBoxComponentSearch" data-vvveb-on="keyup">
													  <button class="clear-backspace" data-vvveb-action="clearComponentSearch">
														  <i class="la la-times"></i>
													  </button>
												  </div>

												<div>
												  <div>
													  
													<ul class="components-list clearfix" data-type="addbox">
													</ul>

												  </div>
												</div>
										  
										  </div>
										  <div class="tab-pane fade" id="box-blocks" role="tabpanel" aria-labelledby="blocks-tab">
											  
											   <div class="search">
													  <input class="form-control form-control-sm block-search" placeholder="Search blocks" type="text" data-vvveb-action="addBoxBlockSearch" data-vvveb-on="keyup">
													  <button class="clear-backspace"  data-vvveb-action="clearBlockSearch">
														  <i class="la la-times"></i>
													  </button>
												  </div>

												<div>
												  <div>
													  
													<ul class="blocks-list clearfix"  data-type="addbox">
													</ul>

												  </div>
												</div>
										  
										  </div>
										
											 div class="tab-pane fade" id="box-properties" role="tabpanel" aria-labelledby="blocks-tab">
												<div class="component-properties-sidepane">
													<div>
														<div class="component-properties">
															<div class="mt-4 text-center">Click on an element to edit.</div>
														</div>
													</div>
												</div>
											</div 
										</div>
									</div>		

							</div>
							 //add section box 
						</div>
						<iframe src="" id="iframe1" style="background-color: #FFF;">
						</iframe>
					</div>
					
					
				</div>

				<div id="right-panel">
					<div class="component-properties">
						
						<ul class="nav nav-tabs nav-fill" id="properties-tabs" role="tablist">
							  <li class="nav-item content-tab">
								<a class="nav-link active" data-bs-toggle="tab" href="#content-tab" role="tab" aria-controls="components" aria-selected="true">
									<i class="la la-lg la-sliders-h"></i> <div><span>Содержимое</span></div></a>
							  </li>
							  <li class="nav-item style-tab">
								<a class="nav-link" data-bs-toggle="tab" href="#style-tab" role="tab" aria-controls="blocks" aria-selected="false">
									<i class="la la-lg la-paint-brush"></i> <div><span>Стиль</span></div></a>
							  </li>
							  <li class="nav-item advanced-tab">
								<a class="nav-link" data-bs-toggle="tab" href="#advanced-tab" role="tab" aria-controls="blocks" aria-selected="false">
									<i class="la la-lg la-tools"></i> <div><span>Расширенное</span></div></a>
							  </li>
							</ul>
					
							<div class="tab-content">
								 <div class="tab-pane fade show active" id="content-tab" data-section="content" role="tabpanel" aria-labelledby="content-tab">
									<div class="alert alert-dismissible fade show alert-light m-3" role="alert" style="">		  
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>		  
										<strong>Не выбран элемент!</strong><br> Выберите элемент для редактирования!		
									</div>
								</div>
								
								 <div class="tab-pane fade show" id="style-tab" data-section="style" role="tabpanel" aria-labelledby="style-tab">
								</div>
								
								 <div class="tab-pane fade show" id="advanced-tab" data-section="advanced"  role="tabpanel" aria-labelledby="advanced-tab">
										<div class="alert alert-dismissible fade show alert-info m-3" role="alert" style="">		  
											<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>		  
											<strong>Нет детальных настроек!</strong><br> Этот компонент не имеет детальных настроек!		
										</div>
								</div>
								
								
							</div>
							
							
							
					</div>
				</div>
				
				<div id="bottom-panel">

				<div class="btn-group" role="group">

		 			 <button id="code-editor-btn" data-view="mobile" class="btn btn-sm btn-light btn-sm"  title="Code editor" data-vvveb-action="toggleEditor">
						  <i class="la la-code"></i> Редактировать HTML
					  </button>
					 
						<div id="toggleEditorJsExecute" class="form-check mt-1" style="display:none">
							<input type="checkbox" class="form-check-input" id="runjs" name="runjs" data-vvveb-action="toggleEditorJsExecute">
							<label class="form-check-label" for="runjs"><small>Запускать JS в редакторе</small></label>
						</div>
					</div>
					
					<div id="vvveb-code-editor">
						<textarea class="form-control"></textarea>
					<div>

				</div>	
			</div>
		</div>


 templates 

<script id="vvveb-input-textinput" type="text/html">
	
	<div>
		<input name="{%=key%}" type="text" class="form-control"/>
	</div>
	
</script>

<script id="vvveb-input-textareainput" type="text/html">
	
	<div>
		<textarea name="{%=key%}" rows="3" class="form-control"/>
	</div>
	
</script>

<script id="vvveb-input-checkboxinput" type="text/html">
	
	<div class="form-check">
		  <input name="{%=key%}" class="form-check-input" type="checkbox" id="{%=key%}_check">
		  <label class="form-check-label" for="{%=key%}_check">{% if (typeof text !== 'undefined') { %} {%=text%} {% } %}</label>
	</div>
	
</script>

<script id="vvveb-input-radioinput" type="text/html">
	
	<div>
	
		{% for ( var i = 0; i < options.length; i++ ) { %}

		<label class="form-check-input  {% if (typeof inline !== 'undefined' && inline == true) { %}custom-control-inline{% } %}"  title="{%=options[i].title%}">
		  <input name="{%=key%}" class="form-check-input" type="radio" value="{%=options[i].value%}" id="{%=key%}{%=i%}" {%if (options[i].checked) { %}checked="{%=options[i].checked%}"{% } %}>
		  <label class="form-check-label" for="{%=key%}{%=i%}">{%=options[i].text%}</label>
		</label>

		{% } %}

	</div>
	
</script>

<script id="vvveb-input-radiobuttoninput" type="text/html">
	
	<div class="btn-group {%if (extraclass) { %}{%=extraclass%}{% } %} clearfix" role="group">
		{% var namespace = 'rb-' + Math.floor(Math.random() * 100); %}
		
		{% for ( var i = 0; i < options.length; i++ ) { %}

		<input name="{%=key%}" class="btn-check" type="radio" value="{%=options[i].value%}" id="{%=namespace%}{%=key%}{%=i%}" {%if (options[i].checked) { %}checked="{%=options[i].checked%}"{% } %} autocomplete="off">
		<label class="btn btn-outline-primary {%if (options[i].extraclass) { %}{%=options[i].extraclass%}{% } %}" for="{%=namespace%}{%=key%}{%=i%}" title="{%=options[i].title%}">
		  {%if (options[i].icon) { %}<i class="{%=options[i].icon%}"></i>{% } %}
		  {%=options[i].text%}
		</label>

		{% } %}
				
	</div>
	
</script>


<script id="vvveb-input-toggle" type="text/html">
	
    <div class="toggle">
        <input 
		type="checkbox" 
		name="{%=key%}" 
		value="{%=on%}" 
		{%if (off) { %} data-value-off="{%=off%}" {% } %}
		{%if (on) { %} data-value-on="{%=on%}" {% } %} 
		class="toggle-checkbox" 
		id="{%=key%}">
        <label class="toggle-label" for="{%=key%}">
            <span class="toggle-inner"></span>
            <span class="toggle-switch"></span>
        </label>
    </div>
	
</script>

<script id="vvveb-input-header" type="text/html">

		<h6 class="header">{%=header%}</h6>
	
</script>

	
<script id="vvveb-input-select" type="text/html">

	<div>

		<select class="form-select">
			{% for ( var i = 0; i < options.length; i++ ) { %}
			<option value="{%=options[i].value%}">{%=options[i].text%}</option>
			{% } %}
		</select>
	
	</div>
	
</script>

<script id="vvveb-input-dateinput" type="text/html">
	
	<div>
		<input name="{%=key%}" type="date" class="form-control" 
			{% if (typeof min_date === 'undefined') { %} min="{%=min_date%}" {% } %} {% if (typeof max_date === 'undefined') { %} max="{%=max_date%}" {% } %}
		/>
	</div>
	
</script>

<script id="vvveb-input-listinput" type="text/html">

	<div class="row">

		{% for ( var i = 0; i < options.length; i++ ) { %}
		<div class="col-6">
			<div class="input-group">
				<input name="{%=key%}_{%=i%}" type="text" class="form-control" value="{%=options[i].text%}"/>
				<div class="input-group-append">
					<button class="input-group-text btn btn-sm btn-danger">
						<i class="la la-trash la-lg"></i>
					</button>
				</div>
			  </div>
			  <br/>
		</div>
		{% } %}


		{% if (typeof hide_remove === 'undefined') { %}
		<div class="col-12">
		
			<button class="btn btn-sm btn-outline-primary">
				<i class="la la-trash la-lg"></i> Добавить новое
			</button>
			
		</div>
		{% } %}
			
	</div>
	
</script>

<script id="vvveb-input-grid" type="text/html">

	<div class="row">
		<div class="col-6 mb-2">
		
			<label>Flexbox</label>
			<select class="form-select" name="col">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col !== 'undefined') && col == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>

		<div class="col-6 mb-2">
			<label>Очень маленький</label>
			<select class="form-select" name="col-xs">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_xs !== 'undefined') && col_xs == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		 div class="col-6">
			<label>Small</label>
			<select class="form-select" name="col-sm">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_sm !== 'undefined') && col_sm == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
			<br/>
		</div 
		
		<div class="col-6 mb-2">
			<label>Средний</label>
			<select class="form-select" name="col-md">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_md !== 'undefined') && col_md == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		<div class="col-6 mb-2">
			<label>Большой</label>
			<select class="form-select" name="col-lg">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_lg !== 'undefined') && col_lg == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		
		<div class="col-6 mb-2">
			<label>Очень большой</label>
			<select class="form-select" name="col-xl">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_lg !== 'undefined') && col_lg == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		<div class="col-6 mb-2">
			<label>Самый очень большой</label>
			<select class="form-select" name="col-xxl">
				
				<option value="">None</option>
				{% for ( var i = 1; i <= 12; i++ ) { %}
				<option value="{%=i%}" {% if ((typeof col_lg !== 'undefined') && col_lg == i) { %} selected {% } %}>{%=i%}</option>
				{% } %}
				
			</select>
		</div>
		
		{% if (typeof hide_remove === 'undefined') { %}
		<div class="col-12">
		
			<button class="btn btn-sm btn-outline-light text-danger">
				<i class="la la-trash la-lg"></i> Удалить
			</button>
			
		</div>
		{% } %}
		
	</div>
	
</script>

<script id="vvveb-input-textvalue" type="text/html">
	
	<div class="row">
		<div class="col-6 mb-1">
			<label>Значение</label>
			<input name="value" type="text" value="{%=value%}" class="form-control"/>
		</div>

		<div class="col-6 mb-1">
			<label>Текст</label>
			<input name="text" type="text" value="{%=text%}" class="form-control"/>
		</div>

		{% if (typeof hide_remove === 'undefined') { %}
		<div class="col-12">
		
			<button class="btn btn-sm btn-outline-light text-danger">
				<i class="la la-trash la-lg"></i> Удалить
			</button>
			
		</div>
		{% } %}

	</div>
	
</script>

<script id="vvveb-input-rangeinput" type="text/html">
	
	<div class="input-range">
		
		<input name="{%=key%}" type="range" min="{%=min%}" max="{%=max%}" step="{%=step%}" class="form-range" data-input-value/>
		<input name="{%=key%}" type="number" min="{%=min%}" max="{%=max%}" step="{%=step%}" class="form-control" data-input-value/>
	</div>
	
</script>

<script id="vvveb-input-imageinput" type="text/html">
	
	<div>
		<input name="{%=key%}" type="text" class="form-control"/>
		<input name="file" type="file" class="form-control"/>
	</div>
	
</script>

<script id="vvveb-input-colorinput" type="text/html">
	
	<div>
		<input name="{%=key%}" type="color" {% if (typeof value !== 'undefined' && value != false) { %} value="{%=value%}" {% } %}  pattern="#[a-f0-9]{6}" class="form-control form-control-color"/>
	</div>
	
</script>

<script id="vvveb-input-bootstrap-color-picker-input" type="text/html">
	
	<div>
		<div id="cp2" class="input-group" title="Using input value">
		  <input name="{%=key%}" type="text" {% if (typeof value !== 'undefined' && value != false) { %} value="{%=value%}" {% } %}	 class="form-control"/>
		  <span class="input-group-append">
			<span class="input-group-text colorpicker-input-addon"><i></i></span>
		  </span>
		</div>
	</div>

</script>

<script id="vvveb-input-numberinput" type="text/html">
	<div>
		<input name="{%=key%}" type="number" value="{%=value%}" 
			  {% if (typeof min !== 'undefined' && min != false) { %}min="{%=min%}"{% } %} 
			  {% if (typeof max !== 'undefined' && max != false) { %}max="{%=max%}"{% } %} 
			  {% if (typeof step !== 'undefined' && step != false) { %}step="{%=step%}"{% } %} 
		class="form-control"/>
	</div>
</script>

<script id="vvveb-input-button" type="text/html">
	<div>
		<button class="btn btn-sm btn-primary">
			<i class="la  {% if (typeof icon !== 'undefined') { %} {%=icon%} {% } else { %} la-plus {% } %} la-lg"></i> {%=text%}
		</button>
	</div>		
</script>

<script id="vvveb-input-cssunitinput" type="text/html">
	<div class="input-group" id="cssunit-{%=key%}">
		<input name="number" type="number"  {% if (typeof value !== 'undefined' && value != false) { %} value="{%=value%}" {% } %} 
			  {% if (typeof min !== 'undefined' && min != false) { %}min="{%=min%}"{% } %} 
			  {% if (typeof max !== 'undefined' && max != false) { %}max="{%=max%}"{% } %} 
			  {% if (typeof step !== 'undefined' && step != false) { %}step="{%=step%}"{% } %} 
		class="form-control"/>
		 <div class="input-group-append">
		<select class="form-select small-arrow" name="unit">
			<option value="em">em</option>
			<option value="px">px</option>
			<option value="%">%</option>
			<option value="rem">rem</option>
			<option value="auto">auto</option>
		</select>
		</div>
	</div>
	
</script>


<script id="vvveb-filemanager-folder" type="text/html">
	<li data-folder="{%=folder%}" class="folder">
		<label for="{%=folder%}"><span>{%=folderTitle%}</span></label> <input type="checkbox" id="{%=folder%}" />
		<ol></ol>
	</li>
</script>

<script id="vvveb-filemanager-page" type="text/html">
	<li data-url="{%=url%}" data-file="{%=file%}" data-page="{%=name%}" class="file">
		<label for="{%=name%}"><span>{%=title%}</span></label> <input type="checkbox" id="{%=name%}" />
		<ol></ol>
	</li>
</script>

<script id="vvveb-filemanager-component" type="text/html">
	<li data-url="{%=url%}" data-component="{%=name%}" class="component">
		<a href="{%=url%}"><span>{%=title%}</span></a>
	</li>
</script>

<script id="vvveb-input-sectioninput" type="text/html">

		<label class="header" data-header="{%=key%}" for="header_{%=key%}"><span>&ensp;{%=header%}</span> <div class="header-arrow"></div></label> 
		<input class="header_check" type="checkbox" {% if (typeof expanded !== 'undefined' && expanded == false) { %} {% } else { %}checked="true"{% } %} id="header_{%=key%}"> 
		<div class="section" data-section="{%=key%}"></div>		
	
</script>


<script id="vvveb-property" type="text/html">

	<div class="mb-3 {% if (typeof col !== 'undefined' && col != false) { %} col-sm-{%=col%} d-inline-block px-2 {% } else { %}row{% } %}" data-key="{%=key%}" {% if (typeof group !== 'undefined' && group != null) { %}data-group="{%=group%}" {% } %}>
		
		{% if (typeof name !== 'undefined' && name != false) { %}<label class="{% if (typeof inline === 'undefined' ) { %}col-sm-4{% } %} control-label" for="input-model">{%=name%}</label>{% } %}
		
		<div class="{% if (typeof inline === 'undefined') { %}col-sm-{% if (typeof name !== 'undefined' && name != false) { %}8{% } else { %}12{% } } %} input"></div>
		
	</div>		 
	
</script>

<script id="vvveb-input-autocompletelist" type="text/html">
	
	<div>
		<input name="{%=key%}" type="text" class="form-control"/>
		
		<div class="form-control autocomplete-list" style="min=height: 150px; overflow: auto;">
                  </div>
                  </div>
	
</script>

<script id="vvveb-input-tagsinput" type="text/html">
	
	<div>
		<div class="form-control tags-input" style="height:auto;">
				

				<input name="{%=key%}" type="text" class="form-control" style="border:none;min-width:60px;"/>
                  </div>
                  </div>
	
</script>

<script id="vvveb-section" type="text/html">
	{% var suffix = Math.floor(Math.random() * 10000); %}

	<div class="section-item" draggable="true">
		<div class="controls">
			<div class="handle"></div>
			<div class="info">
				<div class="name">{%=name%} 
					<div class="type">{%=type%}</div>
				</div>
			</div>
			<div class="buttons">
				<a class="delete-btn" href="" title="Remove section"><i class="la la-trash text-danger"></i></a>
				 
				<a class="up-btn" href="" title="Move element up"><i class="la la-arrow-up"></i></a>
				<a class="down-btn" href="" title="Move element down"><i class="la la-arrow-down"></i></a>
				
				<a class="properties-btn" href="" title="Properties"><i class="la la-cog"></i></a>
		</div>
		</div>


		<input class="header_check" type="checkbox" id="section-components-{%=suffix%}">

		<label for="section-components-{%=suffix%}"> 
			<div class="header-arrow"></div>
		</label>
		
		<div class="tree">
			<ol >
				<li data-component="Products" class="file">							
					<label for="idNaN" style="background-image:url(http://demo.givan.ro/js/vvvebjs/icons/products.svg)"><span>Товары</span></label>							
					<input type="checkbox" id="idNaN">
				</li>
				<li data-component="Posts" class="file">							
					<label for="idNaN" style="background-image:url(http://demo.givan.ro/js/vvvebjs/icons/posts.svg)"><span>Записи</span></label>							
					<input type="checkbox" id="idNaN">
				</li>
			</ol>
		</div>
	</div>
	
</script>


// end templates 


 export html modal
<div class="modal fade" id="textarea-modal" tabindex="-1" role="dialog" aria-labelledby="textarea-modal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <p class="modal-title text-primary"><i class="la la-lg la-save"></i> Экспорт html</p>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
           span aria-hidden="true"><small><i class="la la-times"></i></small></span 
        </button>
      </div>
      <div class="modal-body">
        
        <textarea rows="25" cols="150" class="form-control"></textarea>
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal"><i class="la la-times"></i> Close</button>
      </div>
    </div>
  </div>
</div>

 message modal
<div class="modal fade" id="message-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <p class="modal-title text-primary"><i class="la la-lg la-comment"></i> Редактор</p>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
           span aria-hidden="true"><small><i class="la la-times"></i></small></span 
        </button>
      </div>
      <div class="modal-body">
        <p>Страница успешно сохранена!</p>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-primary">Ok</button> 
        <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal"><i class="la la-times"></i> Закрыть</button>
      </div>
    </div>
  </div>
</div>

 new page modal
<div class="modal fade" id="new-page-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    
    <form action="save.php">
		
    <div class="modal-content">
      <div class="modal-header">
        <p class="modal-title text-primary"><i class="la la-lg la-file"></i> Новая страница</p>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
           span aria-hidden="true"><small><i class="la la-times"></i></small></span 
        </button>
      </div>

      <div class="modal-body text">
		<div class="mb-3 row" data-key="type">      
			<label class="col-sm-3 control-label">
				Шаблон 
				<abbr class="badge badge-pill badge-secondary" title="This template will be used as a start">?</abbr> 
			</label>      
			<div class="col-sm-9 input">
				<div>    
					<select class="form-select" name="startTemplateUrl">        
						<option value="new-page-blank-template.html">Чистый шаблон</option>        
						<option value="demo/narrow-jumbotron/index.html">Узкий макет</option>       
						<option value="demo/album/index.html">Альбом</option>       
					</select>    
				</div>
			</div>     
		</div>

		<div class="mb-3 row" data-key="href">     
			 <label class="col-sm-3 control-label">Наименование страницы</label>      
			<div class="col-sm-9 input">
				<div>   
					<input name="title" type="text" value="My page" class="form-control" placeholder="My page" required>  
				</div>
			</div>     
		</div>
		
		<div class="mb-3 row" data-key="href">     
			 <label class="col-sm-3 control-label">Наименование файла</label>      
			<div class="col-sm-9 input">
				<div>   
					<input name="file" type="text" value="my-page.html" class="form-control" placeholder="my-page.html" required>  
				</div>
			</div>     
		</div>
		
		 
		<div class="mb-3 row" data-key="href">     
			 <label class="col-sm-3 control-label">Url</label>      
			<div class="col-sm-9 input">
				<div>   
					<input name="url" type="text" value="my-page.html" class="form-control" placeholder="/my-page.html" required>  
				</div>
			</div>     
		</div>
		
		
		<div class="mb-3 row" data-key="href">     
			 <label class="col-sm-3 control-label">Папка</label>      
			<div class="col-sm-9 input">
				<div>   
					<input name="folder" type="text" value="my-pages" class="form-control" placeholder="/" required>  
				</div>
			</div>     
		</div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary btn-lg" type="submit"><i class="la la-check"></i>Создать страницу</button>
        <button class="btn btn-secondary btn-lg" type="reset" data-bs-dismiss="modal"><i class="la la-times"></i>Отмена</button>
      </div>
    </div>
    
   </form>		

  </div>
</div>
</div>
	
 jquery
<script src="/public/testing/js/jquery.min.js"></script>
 Enable shortcut support such as ctrl+z for undo and ctrl+e for export etc
<script src="/public/testing/js/jquery.hotkeys.js"></script>


 bootstrap
<script src="/public/testing/js/popper.min.js"></script>
<script src="/public/testing/js/bootstrap.min.js"></script>

 builder code
 This is the main editor code 
<script src="/public/testing/libs/builder/builder.js"></script>

 undo manager
<script src="/public/testing/libs/builder/undo.js"></script>

 inputs
 The inputs library, here is the code for inputs such as text, select etc used for component properties 
<script src="/public/testing/libs/builder/inputs.js"></script>

 components
 Components for Bootstrap 4 group 
<script src="/public/testing/libs/builder/components-bootstrap4.js"></script>
 Components for Widgets group 
<script src="/public/testing/libs/builder/components-widgets.js"></script>


 plugins 

 code mirror libraries - code editor syntax highlighting for html editor 
<link href="/public/testing/libs/codemirror/lib/codemirror.css" rel="stylesheet"/>
<link href="/public/testing/libs/codemirror/theme/material.css" rel="stylesheet"/>
<script src="/public/testing/libs/codemirror/lib/codemirror.js"></script>
<script src="/public/testing/libs/codemirror/lib/xml.js"></script>
<script src="/public/testing/libs/codemirror/lib/formatting.js"></script>

 code mirror vvveb plugin 
 replaces default textarea as html code editor with codemirror
<script src="/public/testing/libs/builder/plugin-codemirror.js"></script>	
<?
    $urr = explode("/",$_SERVER["REQUEST_URI"]);
    $urr = $urr[count($urr)-2];
?>
PRINTED <?var_dump($urr);?>
<script>
$(document).ready(function() 
{
	//if url has #no-right-panel set one panel demo
	if (window.location.hash.indexOf("no-right-panel") != -1)
	{
		$("#vvveb-builder").addClass("no-right-panel");
		$(".component-properties-tab").show();
		Vvveb.Components.componentPropertiesElement = "#left-panel .component-properties";
	} else
	{
		$(".component-properties-tab").hide();
	}

	Vvveb.Builder.init('demo/material/loadhtml.php?id=<?=$urr?>', function() {
		//run code after page/iframe is loaded
        console.log('123');
	});

	Vvveb.Gui.init();
	Vvveb.FileManager.init();
	Vvveb.SectionList.init();
	Vvveb.FileManager.addPages(
	[
		{name:"narrow-jumbotron", title:"Узкий макет",  url: "demo/narrow-jumbotron/index.html", file: "demo/narrow-jumbotron/index.html", assets: ['demo/narrow-jumbotron/narrow-jumbotron.css']},
		{name:"landing-page", title:"Одностраничник",  url: "demo/startbootstrap-landing-page/index.html", file: "demo/startbootstrap-landing-page/index.html", assets: ['demo/startbootstrap-landing-page/css/landing-page.min.css']},
		{name:"album", title:"Альбом",  url: "demo/album/index.html", file: "demo/album/index.html", folder:"content", assets: ['demo/album/album.css']},
		{name:"blog", title:"Блог",  url: "demo/blog/index.html", file: "demo/blog/index.html", folder:"content", assets: ['demo/blog/blog.css']},
		{name:"carousel", title:"Карусель",  url: "demo/carousel/index.html",  file: "demo/carousel/index.html", folder:"content", assets: ['demo/carousel/carousel.css']},
		{name:"offcanvas", title:"Афиша/форум",  url: "demo/offcanvas/index.html", file: "demo/offcanvas/index.html", folder:"content", assets: ['demo/offcanvas/offcanvas.css','demo/offcanvas/offcanvas.js']},
		{name:"pricing", title:"Карточки цен",  url: "demo/pricing/index.html", file: "demo/pricing/index.html", folder:"ecommerce", assets: ['demo/pricing/pricing.css']},
		{name:"product", title:"Карточки товаров",  url: "demo/product/index.html", file: "demo/product/index.html", folder:"ecommerce", assets: ['demo/product/product.css']},
		//uncomment php code below and rename file to .php extension to load saved html files in the editor
		/*
		<?php 
		   $htmlFiles = glob('{my-pages/*.html,demo/*\/*.html, demo/*.html}',  GLOB_BRACE);
		   foreach ($htmlFiles as $file) { 
			   if (in_array($file, array('new-page-blank-template.html', 'editor.html'))) continue;//skip template files
			   $pathInfo = pathinfo($file);
			   $filename = $pathInfo['filename'];
			   $folder = preg_replace('@/.+?$@', '', $pathInfo['dirname']);
			   $subfolder = preg_replace('@^.+?/@', '', $pathInfo['dirname']);
			   if ($filename == 'index' && $subfolder) {
				   $filename = $subfolder;
			   }
			   $url = $pathInfo['dirname'] . '/' . $pathInfo['basename'];
		?>
		{name:"<?php echo ucfirst($filename);?>", file:"<?php echo ucfirst($filename);?>", title:"<?php echo ucfirst($filename);?>",  url: "<?php echo $url;?>", folder:"<?php echo $folder?>"},
		<?php } ?>
		*/
	]);
	
	//Vvveb.FileManager.loadPage("narrow-jumbotron",);
    
    Vvveb.Builder.loadUrl('demo/material/loadhtml.php?id=<?=$urr?>');//загрузка материала из php
});
</script>

                    
                    
                    -->
                    
                    
                    
                    
                    
                    
                    
                    
                    