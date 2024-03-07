<!DOCTYPE html>
<html lang="ru">
	<?= app()->chunk()->render('common.head')?>
	<body>
		<div class="container container_index">
			<?= app()->chunk()->setArgs(['header_new', isset($this->vars['rubrics']) ? $this->vars['rubrics'] : '', isset($this->vars['mobile_rubrics']) ? $this->vars['mobile_rubrics'] : ''])->render('common.header')?>
			<?= app()->chunk()->render('common.sidebar')?>
			<div class="content">
                <div class="page__container">
                    <?/*= app()->chunk()->render('adv.header_banners')*/?>
                    <?= isset($content) ? $content : ''?>
                </div>
			</div>
			<?= app()->chunk()->render('common.footer')?>
			<?= app()->chunk()->render('common.foot')?>
		</div>
	</body>
</html>