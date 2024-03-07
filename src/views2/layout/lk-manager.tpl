<!DOCTYPE html>
<html lang="ru">
    
	<?= app()->chunk()->render('firmuser.head')?>
	<body>
		<div class="container container_index px-0 mlv-5">
            <!--#0001-->
			<?
            $url_path = app()->getPathUrl(); // получаем массив открытого пути пример: array(2) { [0]=> string(12) "firm-manager" [1]=> string(10) "parsetable" }
            $close_array = array('parsetable','firmtest','instatable');// страницы где не нужен header
            $show_header = true;
            foreach ($close_array as $path)
            {
                if (in_array($path,$url_path)){
                    $show_header = false;
                }
            }
            
            if ($show_header){
                echo app()->chunk()->render('common.header');// не выводим поиск сверху
            }else{
                ?>

                <style>
                    .mlv-5{
                        margin-left: 200px!important;
                    }
                </style>
                <?
            }
            
            ?>
			<?= app()->chunk()->render('firm_manager.sidebar')?>
			<div class="content">
				<?= isset($content) ? $content : ''?>
			</div>
			<?= app()->chunk()->render('common.footer')?>
			<?= app()->chunk()->render('common.foot')?>
		</div>
	</body>
</html>