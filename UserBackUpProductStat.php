<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/7
 * Time: 下午6:48
 */

require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\UserRegisterBackUpBirthQuery;

class UserBackUpProductStat extends UserRegisterBackUpBirthQuery
{
    public function showPlatBackUpSrcItemsOnProduct()
    {
        $srcSummationItem = $this->getSummationSrcItems();
        foreach ($srcSummationItem['srcSummation'] as $key => $value) {
            echo "==== src: " . $key . ";" . $value . " ==== \n";
        }

        foreach ($srcSummationItem['phoneSummation'] as $key => $value) {
            echo " phone ==== src: " . $key . ";" . $value . " ==== \n";
        }
        $summation = $this->getBirthSummation();
        echo " ==== summation: " . $summation . " ==== \n";
    }
}
$userProductStat = new UserBackUpProductStat();
$userProductStat->showPlatBackUpSrcItemsOnProduct();