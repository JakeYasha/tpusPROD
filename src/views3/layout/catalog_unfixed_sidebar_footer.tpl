<!DOCTYPE html>
<html lang="ru">
	<?= app()->chunk()->render('common.head')?>
	<body>
		<div class="container">
			<?= app()->chunk()->setArgs(['header_new', isset($this->vars['rubrics']) ? $this->vars['rubrics'] : '', isset($this->vars['mobile_rubrics']) ? $this->vars['mobile_rubrics'] : ''])->render('common.header')?>
            <main>
                <div class="page__container">
                    <div class="mdc-layout-grid">
                        <div class="mdc-layout-grid__inner">
                            <div class="mdc-layout-grid__cell--span-3-desktop">
                                <?= app()->chunk()->render('common.sidebar')?>
                            </div>
                            <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                                <?/*= app()->chunk()->render('adv.header_banners')*/?>
                                <?= isset($content) ? $content : ''?>
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