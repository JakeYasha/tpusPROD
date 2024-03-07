<?=$bread_crumbs?>
<div class="cat_description">
	<h1><?=app()->metadata()->getHeader()?></h1>
	<p style="text-align: center;">
		<a href="/<?=app()->location()->currentId()?>/advert-module/" ><img title="Все спецпредложения, скидки, акции на Товарыплюс" src="/uploaded/3/6/366d1d7a778f3986002cb7fd31773ef1.gif" alt="Все спецпредложения, скидки, акции на Товарыплюс" style="max-width:700px; width:100%;"></a>
	</p>
	<?=str()->replace(app()->config()->get('app.firm-promo.index', ''), ['_Cp_', '_Cg_', '_L_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId()])?>
</div>
<div class="item_info">
	<?= app()->chunk()->set('filter', $filters)->set('items', $tags)->set('link', app()->link('/firm-promo/'))->render('common.catalog_tags')?>
	<div class="search_result promo">
		<?=$items?>
		<?=$pagination?>
	</div>
	<div class="cat_description">
		<div class="notice-dark-grey">
		<h2>Хотите разместить здесь свои акции?</h2>
		<p style="text-align: center;">Зарегистрируйте свою фирму и размещайте свои акции и специальные предложения для покупателей и получайте новых клиентов с сайта tovaryplus.ru!</p>
		<div style="text-align: center;"><a class="button_red" href="/request/add/">Добавить фирму</a><a class="button_red fancybox fancybox.ajax" href="/firm-user/get-login-form/" rel="nofollow" >Войти в личный кабинет</a></div>
		</div>
	</div>
</div>
<?=$advert_restrictions?>