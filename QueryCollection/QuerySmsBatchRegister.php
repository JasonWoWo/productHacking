<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/22
 * Time: 下午3:23
 */

namespace QueryCollection;

require __DIR__ . '/../Bootstrap.php';

use MiddlewareSpace\SmsRegisterCoreQuery;

class QuerySmsBatchRegister extends SmsRegisterCoreQuery
{
    public function getSmsBatchDetail($batch, $task)
    {
        $batchRegisters = $this->getSmsRegisterOnBatch($batch, $task);
        $detailItems = $this->getRegisterDetailStatus($batchRegisters);
        foreach ($detailItems as $item) {
            echo sprintf("%s;%s;%s;%s \n", $item['id'], $item['udid'], $item['backUpBirths'], $item['authorize']);
        }
    }
}
$query = new QuerySmsBatchRegister();
$query->getSmsBatchDetail(14, 10);
$query->getSmsBatchDetail(16, 12);