<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/18
 * Time: 下午1:50
 */
include __DIR__."/../util/UtilTool.php";
include __DIR__."/../util/UtilSqlTool.php";
include __DIR__.'/../common/Common.php';
class UserRegisterBackUpBirthQuery
{
    use UtilTool;
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

    public function getSummationSrcItems()
    {
        $srcDeviceItems = $this->getSrcParamsKeyInit();
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
        while ($userRank['minId'] <= $userRank['maxId']) {
            $currentSrcResult = $this->getCurrentDeviceSrcCount($userRank['minId'], $userItems);
            $srcItems = $this->summationDeviceSrcItemsCnt($srcItems, $currentSrcResult);
            $userRank['minId'] += 1;
        }
        return $srcItems;
    }

    public function getCurrentDeviceSrcCount($birthTable = 0, $userItems = array())
    {
        $srcParamKey = $this->getSrcParamsKeyInit();
        $userItemString = implode(',', $userItems);
        $currentBirthTable = 'oibirthday.br_birthdays_' . $birthTable;
        foreach ($srcParamKey as $key => &$value) {
            $keyQuery = $this->getQueryBirthListSrc($currentBirthTable, $userItemString, $key);
            $result = $this->connectObj->fetchCnt($keyQuery);
            $value += $result['cnt'];
        }
        return $srcParamKey;
    }
}