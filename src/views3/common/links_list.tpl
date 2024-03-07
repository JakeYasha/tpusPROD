<?

if ($items) {
	$links = [];
	foreach ($items as $item) {
		$links[] = '<a href="'.$item['href'].'"'.(isset($item['target']) ? (' target="'.$item['target'].'"') : '').(isset($item['rel']) ? (' rel="'.$item['rel'].'"') : '').(isset($item['class']) ? (' class="'.$item['class'].'"') : '').(isset($item['data-firm-id']) ? (' data-firm-id="'.$item['data-firm-id'].'"') : '').'>'.$item['name'].'</a>';
	}

	echo implode('', $links);
}?>