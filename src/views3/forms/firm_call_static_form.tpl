<div class="modal" id="callForm<?= $firm->id() ?>">
	<div class="modal__overlay"></div>
	<div class="modal__wrap">
		<div class="modal__content">
			<div class="modal__header">
				<div class="modal__title">
					<?= $heading ?>
				</div>
				<button class="modal__close">
					<i class="material-icons">
						close
					</i>
				</button>
			</div>
			<div class="modal__body">
				<div class="modal__item">
					<strong><?= $firm->name() ?></strong>
				</div>
				<div class="modal__item">
                    <?= $firm->renderPhoneLinks() ?>
				</div>
				<div class="modal__item">
                    <p>Пожалуйста, скажите, что узнали номер на сайте <strong>tovaryplus.ru</strong></p>
				</div>
			</div>
		</div>
	</div>
</div>