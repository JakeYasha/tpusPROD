
<?if (isset($position)){?>
<style>
.box-advt-bottom{
    margin-top: 25px;
    padding: 5px;
    padding-left: 15px;
    padding-right: 15px;
    background-color: #006bb71f;
}
</style>
    <div class="box-advt-bottom">
        <style><?=$position[0]['css'];?></style>
        <?=$position[0]['text'];?>
    </div>
<?
}
?>