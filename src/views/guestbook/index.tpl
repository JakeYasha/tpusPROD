<?= $bread_crumbs?>

<div class="for_clients clearfix guest-book">
	<div class="for_clients_text_c clearfix page for_clients_list">
		<h1><?= $item->name()?></h1>
		<?if($errors) {?>
			<ul>
			<?  foreach ($errors as $k=>$v) {?>
				<li><?=$v['message']?></li>
			<?}?>
			</ul>
		<div class="all_block">
			<?=$form?>
			</div>
		<?} else {?>
			<?=$item->val('text')?>
			<?
                        $i = 0;
                        foreach ($items as $item) {
                            $i++;
                        ?>
                                <div class="guest-book-post<?=$i>5?" hidden":""?>">
                                    <h2><?= $item->val('subject')?></h2>
                                    <p><?=$item->val('text')?></p>
                                    <p class="guest-book-author"><?=$item->val('user_name')?> - <?=date("d.m.Y", CDateTime::toTimestamp($item->val('timestamp_inserting')))?></p>
                                </div>
			<? }?>
            <div class="center"><a class="button_red" href="#" onclick="$('.guest-book-post').not('.hidden').first().nextAll('.hidden:lt(5)').removeClass('hidden'); if ($('.guest-book-post.hidden').length == 0) $(this).remove(); return false;">показать еще...</a></div>
			<p>&nbsp;</p>
			<p>Напишите нам свой отзыв о работе центра в целом или одного из его сервисов в частности, и он обязательно будет рассмотрен нами.</p>
			<div class="all_block">
				<?=$form?>
			</div>
			<p>&nbsp;</p>

		<?}?>
		<p>&nbsp;</p>
		<p style="text-align: center;"><iframe width="480" height="315" style="max-width: 480px; width: 100%;" src="https://www.youtube.com/embed/WnS-uTE1iT0" frameborder="0" allowfullscreen="allowfullscreen"></iframe>&nbsp;<iframe width="480" height="315" style="max-width: 480px; width: 100%;" src="https://www.youtube.com/embed/Y-fDwA8m_lk" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p>
		<p style="text-align: center;"><iframe width="480" height="315" style="max-width: 480px; width: 100%;" src="https://www.youtube.com/embed/4FImoThv4zg" frameborder="0" allowfullscreen="allowfullscreen"></iframe>&nbsp;<iframe width="480" height="315" style="max-width: 480px; width: 100%;" src="https://www.youtube.com/embed/Fls_2w4FqJQ" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p>
		<p style="text-align: center;"><iframe width="480" height="315" style="max-width: 480px; width: 100%;" src="https://www.youtube.com/embed/92bpkdpBZUA" frameborder="0" allowfullscreen="allowfullscreen"></iframe>&nbsp;<iframe width="480" height="315" style="max-width: 480px; width: 100%;" src="https://www.youtube.com/embed/wXE0j4ZXghA" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p>
		<p style="text-align: center;">
			<a class="fancybox" rel="gallery_refs" href="/img/ref/img345.jpg"><img src="/img/ref/img345.jpg" alt="Диплом Золотой Медведь 2010" style="max-width: 480px; width: 48%;" /></a>&nbsp; 
			<a class="fancybox" rel="gallery_refs" href="/img/ref/img346.jpg"><img src="/img/ref/img346.jpg" alt="Благодарственное письмо adconsult" style="max-width: 480px; width: 48%;" /></a>&nbsp; 
			<a class="fancybox" rel="gallery_refs" href="/img/ref/img347.jpg"><img src="/img/ref/img347.jpg" alt="Поздравление от мэрии" style="max-width: 480px; width: 48%;" /></a>&nbsp; 
			<a class="fancybox" rel="gallery_refs" href="/img/ref/img348.jpg"><img src="/img/ref/img348.jpg" alt="Благодарственное письмо Агродортехснаб" style="max-width: 480px; width: 48%;" /></a>&nbsp; 
			<a class="fancybox" rel="gallery_refs" href="/img/ref/img349.jpg"><img src="/img/ref/img349.jpg" alt="Благодарственное письмо Автофирма Светлана" style="max-width: 480px; width: 48%;" /></a>&nbsp; 
			<a class="fancybox" rel="gallery_refs" href="/img/ref/img357.jpg"><img src="/img/ref/img357.jpg" alt="Отзыв бранд проф" style="max-width: 480px; width: 48%;" /></a>&nbsp;
		</p>
	</div>
</div>