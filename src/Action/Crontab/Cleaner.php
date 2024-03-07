<?php

namespace App\Action\Crontab;

use App\Model\Firm;
use App\Model\FirmFile;
use App\Model\FirmPromo;
use App\Model\FirmVideo;
use App\Model\Image;
use Sky4\Helper\DeprecatedDateTime;

class Cleaner extends \App\Action\Crontab {

    public function execute() {
        $this->clearTempImages()
                ->disableVideos()
                ->disablePromos()
                ->cleanCatalog()
                ->cleanCarts()
                ->cleanCartGoods()
                ->cleanAvgStatFirmsPopularPages()
                ->cleanAvgStatFirms727373PopularPages()
                ->cleanAvgStatPopularPages()
                ->cleanAvgStatGeo()
                ->cleanAvgStatGeo727373()
                ->cleanAvgStatObjects()
                ->cleanAvgStatObjects727373()
                ->cleanStatUsers()
                ->cleanStatRequests()
                ->cleanStatObjects()
                ->cleanStatBannerShows()
                ->cleanStatBannerClicks()
                ->cleanStatUsers727373()
                ->cleanStatRequests727373()
                ->cleanStatObjects727373()
                ->cleanStatBanner727373Shows()
                ->cleanStatBanner727373Clicks();
    }

    public function cleanCatalog() {
        $price = new \App\Model\Price();
        $items = array_keys($price->reader()->setSelect(['id'])
                        ->setWhere(['AND', 'flag_is_active = :flag_is_active'], [':flag_is_active' => 0])
                        ->setOrderBy('id DESC')
                        ->setLimit(100000)
                        ->rowsWithKey('id'));

        foreach ($items as $id_price) {
            app()->db()->query()->setText('DELETE FROM `price_catalog_price` WHERE `id_price` = :id_price')
                    ->execute([':id_price' => $id_price]);
        }

        return $this;
    }

    public function disableVideos() {
        $firm_video = new FirmVideo();
        $items = $firm_video->reader()->objects();

        foreach ($items as $video) {
            $firm = new Firm();
            $firm->getByIdFirm($video->id_firm());
            if ($firm->isBlocked()) {
                $video->update([
                    'flag_is_active' => 0
                ]);
            } else {
                $video->update([
                    'flag_is_active' => 1
                ]);
            }
        }

        return $this;
    }

    public function disablePromos() {
        $firm_promo = new FirmPromo();
        $items = $firm_promo->reader()->objects();

        foreach ($items as $promo) {
            $firm = new Firm();
            $firm->getByIdFirm($promo->id_firm());
            if ($firm->isBlocked()) {
                $promo->update([
                    'flag_is_active' => 0
                ]);
            } else {
                $promo->update([
                    'flag_is_active' => 1
                ]);
            }
        }

        return $this;
    }

    public function clearTempImages() {
        $datetime = new \Sky4\Helper\DateTime();
        $dt = $datetime->offsetDays(-1)->format();
        $image = new Image();
        $all = $image->reader()
                ->setWhere(['AND', '`source` = :source', '`timestamp_inserting` < :shift'], [':source' => 'temp', ':shift' => $dt])
                ->objects();

        foreach ($all as $object) {
            $object->delete();
        }

        $file = new FirmFile();
        $all = $file->reader()
                ->setWhere(['AND', 'flag_is_temp = :temp', 'timestamp_inserting < :shift'], [':temp' => 1, ':shift' => $dt])
                ->objects();

        foreach ($all as $object) {
            $object->delete();
        }

        return $this;
    }

    public function cleanCarts() {
        $cart = new \App\Model\Cart();
        while (1) {
            $items = $cart->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_last_updating < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-1 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanCartGoods() {
        $cart_good = new \App\Model\CartGood();
        while (1) {
            $items = $cart_good->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_last_updating < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-1 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    // ----------------- STATISTICS MODELS ----------------- //

    public function cleanAvgStatFirmsPopularPages() {
        $avg = new \App\Model\AvgStatFirmPopularPages();
        while (1) {
            $items = $avg->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanAvgStatFirms727373PopularPages() {
        $avg = new \App\Model\AvgStatFirm727373PopularPages();
        while (1) {
            $items = $avg->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanAvgStatPopularPages() {
        $avg = new \App\Model\AvgStatPopularPages();
        while (1) {
            $items = $avg->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanAvgStatGeo() {
        $avg = new \App\Model\AvgStatGeo();
        while (1) {
            $items = $avg->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanAvgStatGeo727373() {
        $avg = new \App\Model\AvgStatGeo727373();
        while (1) {
            $items = $avg->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanAvgStatObjects() {
        $avg = new \App\Model\AvgStatObject();
        while (1) {
            $items = $avg->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanAvgStatObjects727373() {
        $avg = new \App\Model\AvgStatObject727373();
        while (1) {
            $items = $avg->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanStatUsers() {
        $stat_user = new \App\Model\StatUser();
        while (1) {
            $items = $stat_user->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_beginning< :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanStatRequests() {
        $stat_request = new \App\Model\StatRequest();
        while (1) {
            $items = $stat_request->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanStatObjects() {
        $stat_object = new \App\Model\StatObject();
        while (1) {
            $items = $stat_object->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanStatBannerShows() {
        $stat_banner_show = new \App\Model\StatBannerShow();
        while (1) {
            $items = $stat_banner_show->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanStatBannerClicks() {
        $stat_banner_click = new \App\Model\StatBannerClick();
        while (1) {
            $items = $stat_banner_click->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanStatUsers727373() {
        $stat_user727373 = new \App\Model\StatUser727373();
        while (1) {
            $items = $stat_user727373->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_beginning < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanStatRequests727373() {
        $stat_request727373 = new \App\Model\StatRequest727373();
        while (1) {
            $items = $stat_request727373->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanStatObjects727373() {
        $stat_object727373 = new \App\Model\StatObject727373();
        while (1) {
            $items = $stat_object727373->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanStatBanner727373Shows() {
        $stat_banner_727373_show = new \App\Model\StatBanner727373Show();
        while (1) {
            $items = $stat_banner_727373_show->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

    public function cleanStatBanner727373Clicks() {
        $stat_banner_727373_click = new \App\Model\StatBanner727373Click();
        while (1) {
            $items = $stat_banner_727373_click->reader()
                    ->setLimit(10000)
                    ->setWhere(['AND', 'timestamp_inserting < :clean_date'], [':clean_date' => date('Y-m-d 00:00:00', strtotime('-7 month'))])
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this;
    }

}
