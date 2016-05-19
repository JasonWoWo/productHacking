<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/18
 * Time: 下午1:50
 */
namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class UserRegisterBackUpBirthQuery
{
    use UtilTool;
    use UtilSqlTool;

    public $connectObj;

    /**
     * @var \DateTime
     */
    public $currentDate;

    public $birthSummation = 0;

    public function __construct()
    {
        $this->connectObj = new Common();
        $this->currentDate = new \DateTime();
    }

    public function getSummationSrcItems()
    {
        $srcDeviceItems = $this->getSrcParamsKeyInit();
        $this->currentDate->modify('-1 day');
        $defaultTable = 0;
        while ($defaultTable < 8) {
            $currentDeviceItems = $this->getCurrentDeviceSrcItems($defaultTable, $this->currentDate->getTimestamp());
            $srcDeviceItems = $this->summationDeviceSrcItemsCnt($srcDeviceItems, $currentDeviceItems);
            $defaultTable += 1;
        }
        return $srcDeviceItems;
    }

    public function getCurrentDeviceSrcItems($table = 0, $currentDateStamp = 0)
    {
        $currentTable = 'oistatistics.st_devices_' . $table;
        $srcItems = $this->getSrcParamsKeyInit();
        $userItems = array();
        $userQuery = $this->getQueryRankUserId($currentTable, $currentDateStamp, $currentDateStamp, true);
        $result = $this->connectObj->fetchAssoc($userQuery);
        foreach ($result as $item) {
            $userItems[] = $item['id'];
        }
        $userRankQuery = $this->getQueryRankUserId($currentTable, $currentDateStamp, $currentDateStamp);
        $userRank = $this->connectObj->fetchCnt($userRankQuery);
        $birthMinTable = $this->get_number_birthday_number($userRank['minId']);
        $birthMaxTable = $this->get_number_birthday_number($userRank['maxId']);
        while ($birthMinTable <= $birthMaxTable) {
            $currentSrcResult = $this->getCurrentDeviceSrcCount($birthMinTable, $userItems);
            $srcItems = $this->summationDeviceSrcItemsCnt($srcItems, $currentSrcResult);
            $this->birthSummation += $this->getCurrentDeviceSrcCount($birthMinTable, $userItems , true);
            $birthMinTable += 1;
        }
        echo "== ab: " . $srcItems['ab'] . " == add: " . $srcItems['add'] . " == yab: " . $srcItems['yab'] . " == of: " . $srcItems['of'] . " == oi: " . $srcItems['oi'] . 
            " == qq: " . $srcItems['qq'] . " == rr: " . $srcItems['rr'] . " == wx: " . $srcItems['wx'] . " == pyq: " . $srcItems['pyq'] . " \n";
        return $srcItems;
    }

    public function getCurrentDeviceSrcCount($birthTable = 0, $userItems = array(), $isSummation = false)
    {
        $srcParamKey = $this->getSrcParamsKeyInit();
        $userItemString = implode(',', $userItems);
        $currentBirthTable = 'oibirthday.br_birthdays_' . $birthTable;
        if ($isSummation) {
            $summationQuery = $this->getQueryBirthSummation($currentBirthTable, $userItemString);
            $deviceSummation = $this->connectObj->fetchCnt($summationQuery);
            return $deviceSummation['cnt'];
        }
        foreach ($srcParamKey as $key => &$value) {
            $keyQuery = $this->getQueryBirthListSrc($currentBirthTable, $userItemString, $key);
            $result = $this->connectObj->fetchCnt($keyQuery);
            $value += $result['cnt'];
        }
        return $srcParamKey;
    }

    public function getBirthSummation()
    {
        return $this->birthSummation;
    }

}