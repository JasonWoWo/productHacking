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
        foreach ($resultItems as $item) {
            echo sprintf("%s;%d;%s;%d \n", $item['create_on'], $item['id'], $item['udid'], $item['appid']);
        }
    }

    public function main()
    {
        $current = new \DateTime('2016-05-24');
        $current->modify('-1 day');
        $currentStamp = $current->getTimestamp();
        $endDate = new \DateTime('2016-05-14');
        $endStamp = $endDate->getTimestamp();
        echo "createOn;uid;udid;appid \n";
        while ($endStamp <= $currentStamp) {
            $daliyEndStamp = $currentStamp + 86400;
            $this->getPointBirthCntDetail($currentStamp, $daliyEndStamp);
            $currentStamp = $current->modify('-1 day')->getTimestamp();
        }
    }
}
$query = new QueryBirthCntInZero();
$query->main();