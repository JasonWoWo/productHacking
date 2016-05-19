<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/18
 * Time: 下午3:51
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\UserRegisterBackUpBirthQuery;

class UserBackUpBirthStat extends UserRegisterBackUpBirthQuery
{
    public function showUserBackUpSrcItems()
    {
        $srcSummationItem = $this->getSummationSrcItems();
        foreach ($srcSummationItem as $key => $value) {
            echo "==== src: " . $key . " ==== value: " . $value . " ==== \n";
        }
        $summation = $this->getBirthSummation();
        echo " ==== summation: " . $summation . " ==== \n";
    }
}
$backUp = new UserBackUpBirthStat();
$backUp->showUserBackUpSrcItems();