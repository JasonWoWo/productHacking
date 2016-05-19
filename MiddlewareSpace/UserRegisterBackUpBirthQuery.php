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

    public function getSummationSrcItems($isNew = true)
    {
        $srcDeviceItems = $this->getSrcParamsKeyInit();
        $srcDeviceWithPhoneItems = $this->getSrcParamsKeyInit();
        $this->currentDate->modify('-1 day');
        $defaultTable = 0;
        while ($defaultTable < 8) {
            $currentDeviceItems = $this->getCurrentDeviceSrcItems($defaultTable, $this->currentDate->getTimestamp(), $isNew);
            $srcDeviceItems = $this->summationDeviceSrcItemsCnt($srcDeviceItems, $currentDeviceItems['srcItems']);
            $srcDeviceWithPhoneItems = $this->summationDeviceSrcItemsCnt($srcDeviceWithPhoneItems, $currentDeviceItems['srcWithPhoneItems']);
            $defaultTable += 1;
        }
        return array(
            'srcSummation' => $srcDeviceItems,
            'phoneSummation' => $srcDeviceWithPhoneItems,
        );
    }

    public function getOldUserSrcDetail()
    {
        $srcDeviceItems = $this->getSrcParamsKeyInit();
        $srcDeviceWithPhoneItems = $this->getSrcParamsKeyInit();
        $this->currentDate->modify('-1 day');
        // 这里其实不关系设备表信息
        $defaultTable = 9999;
        $currentDeviceItems = $this->getCurrentDeviceSrcItems($defaultTable, $this->currentDate->getTimestamp(), false);
        $srcDeviceItems = $this->summationDeviceSrcItemsCnt($srcDeviceItems, $currentDeviceItems['srcItems']);
        $srcDeviceWithPhoneItems = $this->summationDeviceSrcItemsCnt($srcDeviceWithPhoneItems, $currentDeviceItems['srcWithPhoneItems']);
        return array(
            'srcSummation' => $srcDeviceItems,
            'phoneSummation' => $srcDeviceWithPhoneItems,
        );
    }

    public function getCurrentDeviceSrcItems($table = 0, $currentDateStamp = 0, $isNew = true)
    {
        $currentTable = 'oistatistics.st_devices_' . $table;
        $srcItems = $this->getSrcParamsKeyInit();
        $srcWithPhoneItems= $this->getSrcParamsKeyInit();
        $userItems = array();
        $userQueryItems = $this->getUserSelectQueryList($currentTable, $currentDateStamp, $currentDateStamp, $isNew);
        $userQuery = $userQueryItems['userQuery'];
        $result = $this->connectObj->fetchAssoc($userQuery);
        foreach ($result as $item) {
            $userItems[] = $item['id'];
        }
        $userRankQuery = $userQueryItems['userRankQuery'];
        $userRank = $this->connectObj->fetchCnt($userRankQuery);
        $birthMinTable = $this->get_number_birthday_number($userRank['minId']);
        $birthMaxTable = $this->get_number_birthday_number($userRank['maxId']);
        while ($birthMinTable <= $birthMaxTable) {
            $currentSrcResult = $this->getCurrentDeviceSrcCount($birthMinTable, $userItems);
            $srcItems = $this->summationDeviceSrcItemsCnt($srcItems, $currentSrcResult['srcCnt']);
            $srcWithPhoneItems = $this->summationDeviceSrcItemsCnt($srcWithPhoneItems, $currentSrcResult['srcWithPhoneCnt']);
            $this->birthSummation += $this->getCurrentDeviceSrcCount($birthMinTable, $userItems , true);
            $birthMinTable += 1;
        }
        echo "== ab: " . $srcItems['ab'] . " == add: " . $srcItems['add'] . " == yab: " . $srcItems['yab'] . " == of: " . $srcItems['of'] . " == oi: " . $srcItems['oi'] . 
            " == qq: " . $srcItems['qq'] . " == rr: " . $srcItems['rr'] . " == wx: " . $srcItems['wx'] . " == pyq: " . $srcItems['pyq'] . " \n";
        echo "phone == ab: " . $srcWithPhoneItems['ab'] . " == add: " . $srcWithPhoneItems['add'] . " == yab: " . $srcWithPhoneItems['yab'] . " == of: " . $srcWithPhoneItems['of'] . " == oi: " . $srcWithPhoneItems['oi'] .
            " == qq: " . $srcWithPhoneItems['qq'] . " == rr: " . $srcWithPhoneItems['rr'] . " == wx: " . $srcWithPhoneItems['wx'] . " == pyq: " . $srcWithPhoneItems['pyq'] . " \n";;
        return array(
            'srcItems' => $srcItems,
            'srcWithPhoneItems' => $srcWithPhoneItems,
        );
    }

    public function getUserSelectQueryList($currentTable, $loginInStamp = 0, $loginEndStamp = 0, $isNew = false)
    {
        if ($isNew) {
            return array(
                'userQuery' => $this->getQueryRankUserId($currentTable, $loginInStamp, $loginEndStamp, true),
                'userRankQuery' => $this->getQueryRankUserId($currentTable, $loginInStamp, $loginEndStamp),
            );
        }
        return array(
            'userQuery' => $this->getQueryDAUFromOld($loginInStamp, $loginEndStamp, true),
            'userRankQuery' => $this->getQueryDAUFromOld($loginInStamp, $loginEndStamp),
        );
    }

    public function getCurrentDeviceSrcCount($birthTable = 0, $userItems = array(), $isSummation = false)
    {
        $srcParamKey = $this->getSrcParamsKeyInit();
        $srcParamWithPhone = $this->getSrcParamsKeyInit();
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
            $srcWithPhoneQuery = $this->getQueryBirthListSrc($currentBirthTable, $userItemString, $key, true);
            $srcWithPhoneResult = $this->connectObj->fetchCnt($srcWithPhoneQuery);
            $srcParamWithPhone[$key] = $srcWithPhoneResult['cnt'];
        }
        return array(
            'srcCnt' => $srcParamKey,
            'srcWithPhoneCnt' => $srcParamWithPhone,
        );
    }

    public function getBirthSummation()
    {
        return $this->birthSummation;
    }

}