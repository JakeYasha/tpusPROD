<? if ($items) { ?>
    <? $i = 0;
    foreach ($items as $item) {
        $i++; ?>
        <div class="search_result_cell no_span"><span class="number"><?= $i ?></span>
            <div class="title"><a href="<?= $item['link'] ?>"><?= $item['title'] ?></a> / <? if ($item['sites']) { ?><strong><?= $item['sites'] ?></strong><? } ?></div>
            <div class="consumer_metadata rate-block">
                <a href="<?= $item['vote'] ? '#' : ($item['link'] . '?thumb=up')?>" class="like <?=$item['vote_result'] == 'up' ? 'voted' : ''?>"></a> <?= $item['likes'] ?> 
                &nbsp; 
                <a href="<?= $item['vote'] ? '#' : ($item['link'] . '?thumb=down')?>" class="dislike <?=$item['vote_result'] == 'down' ? 'voted' : ''?>"></a> <?= $item['dislikes'] ?></div>
            <div class="description" style="font-size: 10px;padding: 5px 0 8px 5px;color: #ad7d7d;">
                <?= date("d.m.Y H:i", CDateTime::toTimestamp($item['timestamp_last_updating'])) ?> / <?= $item['flag_is_hidden'] ? 'только для сотрудников' : 'для всех' ?>
            </div>
            <div class="description notice-blue">
        <?= $item['text'] ?>
            </div>
        </div>
    <? } ?>
<? } ?>