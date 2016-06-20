<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/20
 * Time: 下午3:44
 */

namespace QueryCollection;

require __DIR__ . '/../Bootstrap.php';

use MiddlewareSpace\InQuery;

class QueryWeChartAddCnt extends InQuery
{
    public function getWeChartDetail()
    {
        $pointDate = new \DateTime('2016-06-18');
        $details = $this->getWeChartQuestUserItems($pointDate->getTimestamp());
        echo sprintf("Detail userCnt: %d , BackUpCnt: %d \n", $details['userCnt'], $details['cnt']);
    }
}