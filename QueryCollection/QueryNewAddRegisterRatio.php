<?php

namespace QueryCollection;

require __DIR__ . '/../Bootstrap.php';

use MiddlewareSpace\QueryCollectionRatio;

class QueryNewAddRegisterRatio extends QueryCollectionRatio
{
    public function getDeviceDetail()
    {
        $collection = $this->getAddUdidDetail();
        foreach ($collection as $item) {
            echo sprintf("%s;%d;%s;%d \n", $item['create_on'], $item['id'], $item['udid'], $item['product_sk']);
        }
    }
    
    public function getUdidDetail()
    {
        $collection = $this->getAddUdidDetail();
        foreach ($collection as $item) {
            echo sprintf("%s;%d \n", $item['udid'], $item['product_sk']);
        }
    }
}
$query = new QueryNewAddRegisterRatio();
$query->getDeviceDetail();
//$query->getUdidDetail();