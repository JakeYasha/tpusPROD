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
                            <div class="mdc-layout-grid__cell--span-3-desktop tp-mt-d-none tp-mt-lg-d-block"><!--8998-->
                                

                            <!-- Yandex.RTB R-A-63031-17 -->
                            <div id="yandex_rtb_R-A-63031-17"></div>
                            <script type="text/javascript">
                                window.addEventListener("load", function(){
                                (function(w, d, n, s, t) {
                                         w[n] = w[n] || [];
                                         w[n].push(function() {
                                             Ya.Context.AdvManager.render({
                                                 blockId: "R-A-63031-17",
                                                 renderTo: "yandex_rtb_R-A-63031-17",
                                                 async: true
                                             });
                                         });
                                         t = d.getElementsByTagName("script")[0];
                                         s = d.createElement("script");
                                         s.type = "text/javascript";
                                         s.src = "//an.yandex.ru/system/context.js";
                                         s.async = true;
                                         t.parentNode.insertBefore(s, t);
                                     })(this, this.document, "yandexContextAsyncCallbacks");
                            });


                            </script>
                                <?//=strpos($_SERVER['REQUEST_URI'], '/catalog') !== false ? app()->chunk()->render('common.sidebar') : '';?>
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