<?



if (in_array($_SERVER['REMOTE_ADDR'], ['78.106.194.157'])){
    echo '<!--THIS IS DEAD SPACE!!!-->';

    $banner = false;
    if ($banner){
        include_once('/new_modules/new_banners.php');
        $nBanner = new nBanners;
        $nBanner->get_banners();
    }

}

?>