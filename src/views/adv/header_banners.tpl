<?if($items){?><?=app()->adv()->renderBannerPlace($items)?><?}elseif((int)app()->location()->getRegionId() !== 76){?>
<div class="pre_top_adv_block">
	<div class="search_adv_block">
		<!-- Yandex.RTB R-A-63031-4 -->
        <div id="yandex_rtb_R-A-63031-4"></div>
        <script>
            (function (w, d, n, s, t) {
                w[n] = w[n] || [];
                w[n].push(function () {
                    Ya.Context.AdvManager.render({
                        blockId: "R-A-63031-4",
                        renderTo: "yandex_rtb_R-A-63031-4",
                        async: true
                    });
                });
                t = d.getElementsByTagName("script")[0];
                s = d.createElement("script");
                s.src = "//an.yandex.ru/system/context.js";
                s.async = true;
                t.parentNode.insertBefore(s, t);
            })(this, this.document, "yandexContextAsyncCallbacks");
        </script>
	</div>
</div>
<?}?>