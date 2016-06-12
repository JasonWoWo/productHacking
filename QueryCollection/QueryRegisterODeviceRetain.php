<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/12
 * Time: 下午2:15
 */

namespace QueryCollection;

use MiddlewareSpace\QueryRegisterForODevice;

require __DIR__ . '/../Bootstrap.php';

class QueryRegisterODeviceRetain extends QueryRegisterForODevice
{
    public function getRegisters($extendStamp = 0, $isRetain = 7)
    {
        $timeStamp = empty($extendStamp) ? time() : $extendStamp;
        $loginTimeStamp = $timeStamp - $isRetain * 86400;
        $this->getSummationODRegisters($loginTimeStamp);
        $retainCnt = $this->getODRegisterRetain($timeStamp);
        echo "Date: " . date('Y-m-d', $loginTimeStamp) . "On " . $isRetain ." Retain Cnt: " . $retainCnt . " \n";
    }

    public function main()
    {
        $currentDate = new \DateTime();
        $this->getRegisters($currentDate->modify('-1')->getTimestamp());
    }
}
$odRegister = new QueryRegisterODeviceRetain();
$odRegister->main();