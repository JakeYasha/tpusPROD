<? if ($items) { ?>
				<!-- Yandex.RTB R-A-63031-12 -->
				<div id="yandex_rtb_R-A-63031-12"></div>
				<script type="text/javascript">
					(function(w, d, n, s, t) {
						w[n] = w[n] || [];
						w[n].push(function() {
							Ya.Context.AdvManager.render({
								blockId: "R-A-63031-12",
								renderTo: "yandex_rtb_R-A-63031-12",
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
				</script>
	<div class="delimiter-block"></div>
    <?= app()->adv()->renderBannerPlace($items) ?>
<? } else { ?>
    <div class="header_adv_block">
		<div class="delimiter-block"></div>
		<!-- Yandex.RTB R-A-63031-12 -->
		<div id="yandex_rtb_R-A-63031-12"></div>
		<script type="text/javascript">
			(function(w, d, n, s, t) {
				w[n] = w[n] || [];
				w[n].push(function() {
					Ya.Context.AdvManager.render({
						blockId: "R-A-63031-12",
						renderTo: "yandex_rtb_R-A-63031-12",
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
		</script>
		<div class="delimiter-block"></div>
	</div>
<?}?>