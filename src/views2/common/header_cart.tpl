<div class="header">
	<?=  app()->chunk()->render('common.header_tp_logo')?>
	<?if(app()->firmManager()->exists()){?>
	<div class="exit_field">
		<div class="mail_field">
			<a href="/firm-manager/" rel="nofollow"><?=app()->firmManager()->val('email')?></a>
		</div>
		<div class="exit">
			<a href="/firm-manager/logout/" rel="nofollow">Выход</a>
		</div>
	</div>
	<?} elseif(app()->firmUser()->exists()){?>
	<div class="exit_field">
		<div class="mail_field">
			<a href="/firm-user/" rel="nofollow"><?=app()->firmUser()->val('email')?></a>
		</div>
		<div class="exit">
			<a href="/firm-user/logout/" rel="nofollow">Выход</a>
		</div>
	</div>
	<?} else {?>
	<div class="add_field">
		<a href="/request/add/" class="add">Добавить организацию</a>
	</div>
	<div class="login_field">
		<a href="/firm-user/get-login-form/" class="login fancybox fancybox.ajax js-login-form-btn" rel="nofollow">Войти</a>
		<a style="display: none;" href="/firm-user/get-restore-form/" class="fancybox fancybox.ajax js-recover-form-btn" rel="nofollow">Напомнить пароль</a>
	</div>
	<?}?>
</div>
