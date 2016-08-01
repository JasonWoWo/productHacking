<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/8/1
 * Time: 下午3:07
 */

namespace QueryCollection;


use MiddlewareSpace\UnCoreUseAuthorize;

class QueryUserContactInfo extends UnCoreUseAuthorize
{
    public function main($extendStamp = 0)
    {
        $timestamp = empty($extendStamp) ? strtotime(date('Y-m-d')) : $extendStamp;
        $currentDate = new \DateTime($timestamp);
        $currentDate->modify('-1 day');
        $this->getUnCoreUser($currentDate->getTimestamp(), 0, -1, 5);
        $this->getAuthorizeStatus();
        $detailItems = $this->userDetailItem;
        foreach ($detailItems as $item) {
            echo sprintf("%s;%s;%s;%s;%s \n", $item['udid'], $item['uid'], $item['max_bct'], $item['contactAuth'], $item['channelId']);
        }
    }
}
$userContact = new QueryUserContactInfo();
$userContact->main();