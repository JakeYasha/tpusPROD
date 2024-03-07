<!DOCTYPE html>
<html lang="ru">
	<?= app()->chunk()->render('common.head')?>
	<body>
		<div class="container">
			<?= app()->chunk()->render('common.materials_header')?>
            <main class="article-page">
                <div class="page__container">
                    <div class="mdc-layout-grid">
                        <div class="mdc-layout-grid__inner">
                            <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                                <?= isset($content) ? $content : ''?>
                            </div>
                            <div class="mdc-layout-grid__cell--span-3-desktop">
                                <?= app()->chunk()->render('common.sidebar_news')?>
                            </div>
                            <div class="mdc-layout-grid__cell--span-12">
                                <?= app()->chunk()->render('common.materials_pre_footer')?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
			<?/*= app()->chunk()->render('common.direct')*/?>
			<?= app()->chunk()->render('common.footer')?>
			<?= app()->chunk()->render('common.foot')?>
		</div>
	</body>
</html>