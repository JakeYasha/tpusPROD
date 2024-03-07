


<?/*<aside class="sidebar">
	<div>

									<!-- Yandex.RTB R-A-63031-6 -->
									<div id="yandex_rtb_R-A-63031-6"></div>
									<script type="text/javascript">
										(function(w, d, n, s, t) {
											w[n] = w[n] || [];
											w[n].push(function() {
												Ya.Context.AdvManager.render({
												blockId: "R-A-63031-6",
												renderTo: "yandex_rtb_R-A-63031-6",
												async: true
											});
										});
										t = d.getElementsByTagName("script")[0];
										s = d.createElement("script");
										s.type = "text/javascript";
										s.src = "//an.yandex.ru/system/context.js";
										s.async = true;
										t.parentNode.insertBefore(s, t);
										})(this, this.document, "yandexContextAsyncCallbacks");
									</script>

	</div>
</aside>*/?>
<?/*
<div class="side_bar index_side_bar">
	<?= app()->chunk()->render('common.header_tp_logo') ?>
	<div class="js-sidebar-town-holder top_menu_item">
		<a class="cms_tree_current name" href="#"><?= app()->location()->currentName()?></a>
		<? if (!empty($topCities)) {?>
			<ul>
				<?
				$i = 0;
				$current = app()->location()->city()->id();
				foreach ($topCities as $city) {
					if ($current == $city->id()) continue;$i++;
					?>
					<li<? if ($i == 1) {?> class="cms_tree_first"<? }?>><!--noindex--><a rel="nofollow" href="<?= app()->location()->changeLink($city->locationId())?>"><?= $city->name()?></a><!--/noindex--></li>
				<? }?>
				<li class="cms_tree_last">
					<label for="sidebar-town-autocomplete" class="another">Другой город:<br/>
						<?= $autocomplete?>
					</label>
				</li>
			</ul>
		<? }?>
	</div>
	<div class="side_menu_field">
		<ul class="main_top_menu">
			<? foreach ($main_top_menu_items as $item) {?>
			<li<?if(isset($item['class'])){?> class="<?=$item['class']?>"<?}?>>
					<div class="clearfix">
						<? if ($item['active']) {?>
							<span class="name_catalog"><?= $item['title']?></span>
							<? if (isset($item['count'])) {?>
								<span class="count"><?= $item['count']?></span>
							<? }?>
						<? } else {?>
							<a href="<?= $item['link']?>" class="name"><?= $item['title']?></a>
							<? if (isset($item['count'])) {?>
								<span class="count"><?= $item['count']?></span>
							<? }?>
						<? }?>
					</div>
				</li>
			<? }?>
		</ul>
		<ul class="main_middle_menu">
			<? foreach ($main_middle_menu_items as $item) {?>
				<li class="<?= isset($item['class']) ? $item['class'] : ''?>">
					<div class="clearfix">
						<? if ($item['active']) {?>
							<span class="menu_item"><?= $item['title']?></span>
							<? if (isset($item['count'])) {?>
								<span class="count"><?= $item['count']?></span>
							<? }?>
						<? } else {?>
							<a href="<?= $item['link']?>" class="name"><?= $item['title']?></a>
							<? if (isset($item['count'])) {?>
								<span class="count"><?= $item['count']?></span>
							<? }?>
						<? }?>
					</div>
				</li>
			<? }?>
		</ul>
		<?= app()->chunk()->render('common.left_menu_default_by_region') ?>
		<a href="/firm-user/get-login-form/" class="fancybox fancybox.ajax btn1 popup-handler" rel="nofollow">Войти</a>
		<a href="/request/add/" class="btn2 popup-handler">Добавить организацию</a>
	</div>
	<?= app()->chunk()->render('adv.left_banners')?>
</div>
 */ ?>