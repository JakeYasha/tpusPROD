<?= $bread_crumbs?>
<div class="black-block">Отчеты</div>
<div class="search_result">
	<div class="popup wide">
		<div class="inputs">
			<div class="attention-info">
				<p>Здесь вы можете выбрать элементы отчета для построения.</p>
			</div>
		</div>	
		<form accept-charset="utf-8" action="/firm-user/reports/" enctype="multipart/form-data" method="post" target="_blank">
			<div class="inputs">
				<label>Выбрать месяц отчета</label>
				<div class="sort_by_field" style="width: 340px;">
					<select name="date" id="sort_by">
						<? foreach ($dates as $key => $date) {?>
							<option value="<?= $key?>"><?= $date?></option>
						<? }?>
					</select>
				</div>
			</div>
			<div class="inputs" style="padding-top: 50px;">
				<label>Выберите отчеты</label>
				<label><input class="e-check-box js-reports-select-all" name="type[]" type="checkbox" value=""><strong>Выбрать все</strong></label>
				<div class="e-check-boxes">
					<ul>
						<li><label><input class="e-check-box" name="type[]" type="checkbox" value="100">Общая информация о фирме</label></li>
						<li><label><input class="e-check-box js-reports-select-childs" name="type[]" type="checkbox" value="200">Статистика tovaryplus.ru</label>
							<ul style="margin-left: 25px;">
								<li><label><input class="e-check-box" name="type[]" type="checkbox" value="210">Обзорная статистика</label></li>
								<?/*
								<li><label><input class="e-check-box  js-reports-select-childs" name="type[]" type="checkbox" value="220">Просмотренные страницы</label>
									<ul style="margin-left: 25px;">
										<li><label><input class="e-check-box" name="type[]" type="checkbox" value="222">Основные страниц фирмы</label></li>
										<li><label><input class="e-check-box" name="type[]" type="checkbox" value="224">Страницы каталогов</label></li>
									</ul>
								</li>
								<li><label><input class="e-check-box" name="type[]" type="checkbox" value="230">Динамика посещений</label></li>
								<li><label><input class="e-check-box" name="type[]" type="checkbox" value="240">География посетителей</label></li>
								*/?>
                                <li><label><input class="e-check-box js-reports-select-childs" name="type[]" type="checkbox" value="300">Статистика баннеров</label>
                                    <ul style="margin-left: 25px;">
                                        <li><label><input class="e-check-box" name="type[]" type="checkbox" value="310">Статистика кликов баннеров</label></li>
                                    </ul>
                                </li>
							</ul>
						</li>
						<? if (app()->firmUser()->id_service() == 10) {
							/*
							?>
							<li><label><input class="e-check-box js-reports-select-childs" name="type[]" type="checkbox" value="600">Статистика 727373.ru</label>
								<ul style="margin-left: 25px;">
									<li><label><input class="e-check-box" name="type[]" type="checkbox" value="620">Просмотренные страницы</label></li>
									<li><label><input class="e-check-box" name="type[]" type="checkbox" value="630">Динамика посещений</label></li>
									<li><label><input class="e-check-box" name="type[]" type="checkbox" value="640">География посетителей</label></li>
                                    <li><label><input class="e-check-box js-reports-select-childs" name="type[]" type="checkbox" value="700">Статистика баннеров</label>
                                        <ul style="margin-left: 25px;">
                                            <li><label><input class="e-check-box" name="type[]" type="checkbox" value="710">Статистика кликов баннеров</label></li>
                                        </ul>
                                    </li>
								</ul>
							</li>
						<? 
						*/
						}?>
						<? if (app()->firmUser()->id_service() == 10) {?>
							<li><label><input class="e-check-box" name="type[]" type="checkbox" value="400">Статистика звонков</label></li>
							<li><label><input class="e-check-box" name="type[]" type="checkbox" value="500">Статистика email</label></li>
						<? }?>
					</ul>
				</div>	
			</div>
			<div class="error-submit"></div>
			<button class="e-button send js-send-blank" type="button">Построить отчет</button></form>
	</div>
	<div class="delimiter-block"></div>
	<?= app()->chunk()->render('firmuser.call_support_block')?>
</div>