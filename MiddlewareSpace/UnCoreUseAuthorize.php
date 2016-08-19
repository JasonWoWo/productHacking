<?php

namespace MiddlewareSpace;


use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class UnCoreUseAuthorize
{
    use UtilSqlTool;
    use UtilTool;

    public $connectObj;

    public $userDetailItem = array();

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function getUnCoreUser($extendStamp = 0, $isRetain = 0, $minBirthCnt = 0, $maxBirthCnt = 0)
    {
        $loginStartStamp = $extendStamp - $isRetain * $this->default_daily_timestamp;
        $loginEndStamp = $extendStamp - ($isRetain - 1) * $this->default_daily_timestamp;
        $query = array(
            'dct_lt' => array('$gte' => $loginStartStamp, '$lte' => $loginEndStamp),
            'max_bct' => array('$gte' => $minBirthCnt, '$lte' => $maxBirthCnt),
            'appid' => array('$gte' => 1001, '$lte' => 1002),
        );
        $retainCollection = $this->connectObj->fetchRetainCollection();
        $retains = $retainCollection->find($query);
        if (empty($retains)) {
            return false;
        }
        foreach ($retains as $item) {
            $userDetail = array(
                'uid' => $item['uid'],
                'max_bct' => $item['max_bct'],
                'udid' => $item['_id'],
            );
            $this->userDetailItem[] = $userDetail;
        }
    }

    public function getAuthorizeStatus()
    {
        $deviceAuthorizeCollection = $this->connectObj->fetchDeviceInfoCollection();
        foreach ($this->userDetailItem as &$item) {
            $singleUserDetailQuery = $this->getQueryBirthZeroInProduct($item['uid']);
            $singleUserDetail = $this->connectObj->fetchCnt($singleUserDetailQuery);
            $appId = -1;
            if (isset($singleUserDetail['appid'])) {
                $appId = $singleUserDetail['appid'];
            }
            $item['channelId'] = $appId;
            $query = array('_id' => $item['udid']);
            $userDeviceAuth = $deviceAuthorizeCollection->findOne($query);
            $contactAuth = -1;
            if (!empty($userDeviceAuth)) {
                $contactAuth = $userDeviceAuth['combineAuthorizeStatus'];
            }
            $item['contactAuth'] = $contactAuth;
        }
    }

    public function geTmpUserId($phoneItems = array())
    {
        foreach ($phoneItems as $item) {
            $userQuery = $this->getQueryUserWithPhone($item);
            $user = $this->connectObj->fetchCnt($userQuery);
            echo "{$user['id']}\n";
        }
    }
    
    public function mongoTest()
    {
        $appList = array(1001, 1002, 1003);
        $retainCollection = $this->connectObj->fetchRetainCollection();
        $startDate = new \DateTime(date('Y-m-d', strtotime('-1 day')));
        $endDate = new \DateTime('2016-08-16'); // 核心用户的数据分平台从8月16日创建
        $dataItems = array();
        while ($startDate > $endDate) {
            $dayItems = array();
            $dayItems[] = $startDate->format('Y-m-d');
            foreach ($appList as $item) {
                $query = array(
                    'appid' => $item,
                    'dct_lt' => array('$gte' => $startDate->getTimestamp(), '$lte' => $startDate->getTimestamp() + 86400),
                );
                $query['max_bct'] = array('$gte' => -1, '$lte' => 1);
                $itemCnt = $retainCollection->count($query);
                $dayItems[] = $itemCnt;
                $query['max_bct'] = array('$gte' => 2, '$lte' => 5);
                $itemCnt = $retainCollection->count($query);
                $dayItems[] = $itemCnt;
                $query['max_bct'] = array('$gte' => 6, '$lte' => 1000);
                $itemCnt = $retainCollection->count($query);
                $dayItems[] = $itemCnt;
            }
            $dataItems['aaData'][] = $dayItems;
            $startDate->modify('-1 day');
        }
        var_dump($dataItems['aaData']);
    }
    
    
}