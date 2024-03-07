<!--88881-->
    <style>
        .adv-box-parent{
            display: flex;
            flex-direction: row;
            justify-content: space-evenly;
            flex-wrap: wrap;
        }
        .adv-box-parent>div{
            position: relative;
        }
        .adv-box-parent .adv-box-elem a>img{
            width:100%;
            height: auto;
            min-height:250px;
        }
        .adv-box-elem{
            position: relative;
            display: block;
            margin-bottom: 20px;    
            padding: 2px;
            margin-top: 10px;
        }
        .adv-be-text{
            bottom: -18px;
            position: absolute;
            text-align: center;
            width: 100%;
        }
    </style>
    <div class="adv-box-parent">
        <div class="adv-box-elem">
            <!-- admitad.banner: y0vbq4pf790b6a4d04de16525dc3e8 AliExpress WW -->
<a target="_blank" rel="nofollow" href="https://alitems.site/g/y0vbq4pf790b6a4d04de16525dc3e8/?i=4"><img border="0" src="https://cdn.admitad-connect.com/public/bs/2018/03/29/9780295b9ddd35472c2010fe6c07cc9a.gif" alt="AliExpress WW"/></a>
<!-- /admitad.banner -->
            <!-- /admitad.banner -->
            <div class="adv-be-text">
                Выгодная цена!
            </div>
        </div>
        <div class="adv-box-elem">
            <!-- admitad.banner: qm6bcp3hm30b6a4d04de452201a8af Музторг -->
            <a target="_blank" rel="nofollow" href="https://ad.admitad.com/g/qm6bcp3hm30b6a4d04de452201a8af/?i=4"><img border="0" src="https://cdn.admitad-connect.com/public/bs/2019/05/21/4f82fc19227d3496be96f616322384bf.jpg" alt="Музторг"/></a>
            <!-- /admitad.banner -->
            <div class="adv-be-text">
                Музыкальные инструменты 40%
            </div>
        </div>
        


    </div>
    <div class="adv-box-parent">
        <?
        $rand = random_int(1,2);
            if ($rand==1){
                
        ?>
        <!-- Yandex.Market Widget -->
        <script async src="https://aflt.market.yandex.ru/widget/script/api" type="text/javascript"></script>
        <script type="text/javascript">
            (function (w) {
                function start() {
                    w.removeEventListener("YaMarketAffiliateLoad", start);
                    w.YaMarketAffiliate.createWidget({type:"models",
            containerId:"marketWidget",
            params:{clid:2564601,
                searchText:"подарки на выпускной",
                themeRows:1,
                themeId:1 } });
                }
                w.YaMarketAffiliate
                    ? start()
                    : w.addEventListener("YaMarketAffiliateLoad", start);
            })(window);
        </script>
        <!-- End Yandex.Market Widget -->
        <?
        }
        if ($rand==2){
        ?>
        <!-- Yandex.Market Widget -->
        <script async src="https://aflt.market.yandex.ru/widget/script/api" type="text/javascript"></script>
        <script type="text/javascript">
            (function (w) {
                function start() {
                    w.removeEventListener("YaMarketAffiliateLoad", start);
                    w.YaMarketAffiliate.createWidget({type:"offers",
            containerId:"marketWidget",
            params:{clid:2564601,
                searchText:"стройинструменты",
                themeId:5,
                themeRows:1,
                themeShowOfferName:true } });
                }
                w.YaMarketAffiliate
                    ? start()
                    : w.addEventListener("YaMarketAffiliateLoad", start);
            })(window);
        </script>
        <!-- End Yandex.Market Widget -->

        <?
        }
    ?>
        <div id="marketWidget" style="width:100%"></div>
    </div>
