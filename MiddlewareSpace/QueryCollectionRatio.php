<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/25
 * Time: 上午10:32
 */

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;

class QueryCollectionRatio
{
    use UtilSqlTool;
    
    public $connectObj;

    /**
     * @var \DateTime
     */
    public $currentDate;

    public function __construct()
    {
        $this->connectObj = new Common();
        $this->currentDate = new \DateTime();
    }

    public function getAddUdidDetail()
    {
        $this->currentDate->modify('-1 day');
        $devicesItems = array();
        $defaultTable = 0;
        while ($defaultTable < 8) {
            $deviceResult = $this->getCurrentDeviceDetail($defaultTable, $this->currentDate->getTimestamp());
            foreach ($deviceResult as $item) {
                $devicesItems[] = $item;
            }
            $defaultTable += 1;
        }
        return $devicesItems;
    }
    
    public function getCurrentDeviceDetail($table, $pointTimeStamp = 0)
    {
        $tableName = "oistatistics.st_devices_" . $table;
        $result = $this->connectObj->fetchAssoc($this->getQueryCollectionRegisterRatio($tableName, $pointTimeStamp));
        return $result;
    }
    
    public function getCurrentDevice($table, $pointTimeStamp = 0)
    {
        $tableName = "oistatistics.st_devices_" . $table;
        $result = $this->connectObj->fetchAssoc($this->getQueryCollectionUdid($tableName, $pointTimeStamp));
        return $result;
    }
}