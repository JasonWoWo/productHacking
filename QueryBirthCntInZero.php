<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/24
 * Time: 上午10:31
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\TmpCoreTaskBirthCntZero;

class QueryBirthCntInZero extends TmpCoreTaskBirthCntZero
{
    public function getPointBirthCntDetail($startStamp = 0, $endStamp = 0)
    {
        $resultItems = $this->getCurrentDayBirthCntZeroDetail($startStamp, $endStamp);
        echo $startStamp . " Label: uid;udid;appid \n";
        foreach ($resultItems as $item) {
            echo sprintf(" %d;%s;%d \n", $item['id'], $item['udid'], $item['appid']);
        }
    }
}
$query = new QueryBirthCntInZero();
// 5.23
$query->getPointBirthCntDetail(1463932800, 1464019200);
// 5.22
$query->getPointBirthCntDetail(1463846400, 1463932800);
// 5.21
$query->getPointBirthCntDetail(1463760000, 1463846400);