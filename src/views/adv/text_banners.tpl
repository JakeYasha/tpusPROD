<?if($items){?><?=app()->adv()->renderBannerPlace($items)?><?}elseif((int)app()->location()->getRegionId() !== 76){?>
<?/*
<div class="search_adv_block">
<span class="title">РЕКЛАМА</span>
<!-- Яндекс.Директ -->
<div id="yandex_ad_middle"></div>
<script>
(function(w, d, n, s, t) {
    w[n] = w[n] || [];
    w[n].push(function() {
        Ya.Direct.insertInto(63031, "yandex_ad_middle", {
            ad_format: "direct",
            font_size: 1,
            type: "horizontal",
            limit: 2,
            title_font_size: 2,
            links_underline: false,
            site_bg_color: "FFFFFF",
            title_color: "444444",
            url_color: "DF2645",
            text_color: "727373",
            hover_color: "444444",
            sitelinks_color: "DF2645",
            favicon: true,
            no_sitelinks: false
        });
    });
    t = d.getElementsByTagName("script")[0];
    s = d.createElement("script");
    s.src = "//an.yandex.ru/system/context.js";
    s.async = true;
    t.parentNode.insertBefore(s, t);
})(window, document, "yandex_context_callbacks");
</script>

</div>
*/?>
<?}?>