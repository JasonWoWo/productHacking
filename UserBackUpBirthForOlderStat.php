<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/19
 * Time: 下午5:44
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\UserRegisterBackUpBirthQuery;

class UserBackUpBirthForOlderStat extends UserRegisterBackUpBirthQuery
{
    public function showUserBackUpForOlderSrcItems()
    {
        echo "== older User Detail == \n";
        $srcSummationItem = $this->getSummationSrcItems(false);
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
$backUp = new UserBackUpBirthForOlderStat();
$backUp->showUserBackUpForOlderSrcItems();