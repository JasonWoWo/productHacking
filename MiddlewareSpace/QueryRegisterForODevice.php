<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/12
 * Time: 下午2:16
 */

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class QueryRegisterForODevice
{
    use UtilSqlTool;
    use UtilTool;

    public $connectObj;

    public $userList = array();

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function getSummationODRegisters($registerTimeStamp = 0)
    {
        $defaultTable = 0;
        while ($defaultTable < 8) {
            $currentTable = "oistatistics.st_devices_" . $defaultTable;
            $userItems = $this->getCurrentTableODRegisters($currentTable, $registerTimeStamp);
            if (!empty($userItems)) {
                $this->userList = $this->userList + $userItems;
            }
            $defaultTable += 1;
        }
    }

    public function getCurrentTableODRegisters($currentTable, $registerTimeStamp = 0)
    {

        $registerQuery = $this->getQueryRegisterForODevice($currentTable, $registerTimeStamp);
        $userItems = $this->connectObj->fetchAssoc($registerQuery);
        return $userItems;
    }

    public function getODRegisterRetain($pointTimeStamp = 0)
    {
        $userItemsList = implode(',', $this->userList);
        $retainCntQuery = $this->getQueryODRegisterVisitCnt($userItemsList, $pointTimeStamp);
        $retainCnt = $this->connectObj->fetchCnt($retainCntQuery);
        return $retainCnt['cnt'];
    }
}