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
            'max_bct' => array('$gte' => $minBirthCnt, '$lte' => $maxBirthCnt)
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
    
    
}