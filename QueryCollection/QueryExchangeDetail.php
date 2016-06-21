<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/21
 * Time: 下午2:32
 */

namespace QueryCollection;

require __DIR__ . '/../Bootstrap.php';

use MiddlewareSpace\ExchangerQuery;

class QueryExchangeDetail extends ExchangerQuery
{
    public function exchangerDetail($argv = array())
    {
        $pointDate = new \DateTime();
        if (!empty($argv)) {
            $pointDate = new \DateTime(strtotime($argv[1]));
        }
        $this->getDailyRegisters($pointDate);
        $this->getExtraDetail();
        foreach ($this->exchangers as $registerDetail) {
            $maxBirthCnt = $registerDetail['birthcnt'] >= $registerDetail['backUpBirths'] ? $registerDetail['birthcnt'] : $registerDetail['backUpBirths'];
            echo sprintf("%s;%s;%s;%s \n", $registerDetail['id'], $registerDetail['udid'], $registerDetail['authorize'], $maxBirthCnt);
        }
    }
}
$exchanger = new QueryExchangeDetail();
$exchanger->exchangerDetail();