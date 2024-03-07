<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">



<div>
    
    <img src="/img3/seostat/stseo1.png" class="img-fluid"/>
    <img src="/img3/seostat/stseo2.png" class="img-fluid"/>
    <img src="/img3/seostat/stseo3.png" class="img-fluid"/>
    <img src="/img3/seostat/stseo4.png" class="img-fluid"/>
    <img src="/img3/seostat/stseo5.png" class="img-fluid"/>
    
    
    <!--<div class="alert alert-info">
        <b>Шаги:</b><br>
        1) Найти фирму на сайте tovaryplus(не в ЛК)<br>
        2) Открыть карточку фирмы на сайте tovaryplus(не в ЛК)<br>
        3) Скопировать ссылку(пример ссылки <a href="https://www.tovaryplus.ru/firm/show/43841/10/" target="_blank">https://www.tovaryplus.ru/firm/show/43841/10/</a>) на карточку фирмы на сайте tovaryplus(не в ЛК)<br>
        4) В личном кабинете, на данной странице, вставить ссылку на фирму в поле ниже и нажать "проверить"
    </div>   
    
    <form>
      <div class="form-group" id="testfirmbox">
        <label for="exampleInputEmail1">Ссылка на ФИРМУ</label>
        <div class="input-group mb-3">
          <input type="text" class="form-control siteurl" placeholder="ПОЛНАЯ ссылка на фирму" aria-label="Recipient's username" aria-describedby="button-addon2">
          <div class="input-group-append">
            <button class="btn btn-info" type="button" onclick="test_parse();">Проверить</button>
          </div>
        </div>   
        <small id="emailHelp" class="form-text text-muted">Введите, пожалуйста, полную ссылку на фирму</small>
      </div>
    </form>


    <div class="firmtest-result">
        <div class="firmtest-name" style="h2"></div>
        <table>
            <tr><td>Оценка:</td><td class="p-3 firmtest-cost"></td></tr>
            <tr><td>Исправить:</td><td class="p-3 firmtest-result"></td></tr>
        </table>
    </div>
    <div class="alert alert-primary">
        Примеры фирм:<br>
        1) <a href="https://www.tovaryplus.ru/firm/show/47499/10/" target="_blank">https://www.tovaryplus.ru/firm/show/47499/10/</a><br>
        2) <a href="https://www.tovaryplus.ru/firm/show/40112/10/" target="_blank">https://www.tovaryplus.ru/firm/show/40112/10/</a><br>
    </div>-->
</div>

<!-- begin http://gy1.ru stats code -->
<!--<script type="text/javascript" src="http://gy1.ru/widget.php?url=tovaryplus.ru&w=230"></script>-->
<!-- end http://gy1.ru stats code -->
<script>
var h_top = document.getElementsByClassName('header-bottom')[0];
h_top.setAttribute('style', 'display:none');

function test_parse(){
    
    $('.firmtest-result .firmtest-name').html('-');
    $('.firmtest-result .firmtest-cost').html('-');
    $('.firmtest-result .firmtest-result').html('-');
    
    $.ajax({
        url: '/public/assets/phpPharser/ph.php',
        method: 'post',
        dataType: 'json',
        data: {ph_url: $('#testfirmbox input.siteurl').val()},
        success: function(data){
            if (data.rez == 1){
                $('.firmtest-result .firmtest-name').html('<b>Наименование:</b> '+data.name);
                $('.firmtest-result .firmtest-cost').html('<b>'+data.cost+'</b> из 5');
                $('.firmtest-result .firmtest-result').html(data.words);
            }else{
                $('.firmtest-result .firmtest-name').html('<b style="color:#F00">ОШИБКА: </b> '+data.mess);
            }
        }
    });
}

</script>						

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
