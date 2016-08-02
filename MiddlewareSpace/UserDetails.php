<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/13
 * Time: 下午2:33
 */

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class UserDetails
{

    //指定用户的出生年份,总消费次数, 生日备份数,手动添加的生日数,云端匹配的生日数, 本地通讯录的生日数
    
    use UtilSqlTool;
    use UtilTool;

    public $userDetails = array();
    
    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }
    
    public function getUserBirthDetail($userItems = array())
    {
        $userItemsLists = implode(',', $userItems);
        $userBirthDetailQuery = $this->getQueryBirthZeroInProduct($userItemsLists);
        $result = $this->connectObj->fetchAssoc($userBirthDetailQuery);
        foreach ($result as $item) {
            $this->userDetails[] = $item;
        }
    }

    public function getUserConsumeCnt()
    {
        foreach ($this->userDetails as &$item) {
            $consumeCntQuery = $this->getQueryRegisterConsumeCnt($item['id'], $item['create_on']);
            $result = $this->connectObj->fetchCnt($consumeCntQuery);
            $item['consumeCnt'] = $result['consumeCnt'];
        }
    }
    
    public function getUserBackUpDetail()
    {
        foreach ($this->userDetails as &$item) {
            $birthTable = $this->get_number_birthday_number($item['id']);
            $src = array('ab', 'yab', 'add');
            $item['all'] = $this->getBackUpSourceCnt($birthTable, $item['id']);
            foreach ($src as $srcList) {
                $item[$srcList] = $this->getBackUpSourceCnt($birthTable, $item['id'], $srcList);
            }
            $item['onJuly'] = $this->getUsersFriendOnJuly($birthTable, $item['id']);
        }
    }
    
    public function getUsersFriendOnJuly($birthTable, $userId)
    {
        $friendOnJuly = $this->getQueryFriendOnJuly($birthTable, $userId);
        $friendCntResult = $this->connectObj->fetchCnt($friendOnJuly);
        return $friendCntResult['cnt'];
    }
    
    public function getBackUpSourceCnt($table, $userId, $src = null)
    {
        $backUpSourceQuery = $this->getQueryBackUpBirthDetail($table, $userId, $src);
        $result = $this->connectObj->fetchCnt($backUpSourceQuery);
        return $result['cnt'];
    }
    
    public function getUserDetails()
    {
        return $this->userDetails;
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
                'id' => $item['uid'],
                'max_bct' => $item['max_bct'],
                'udid' => $item['_id'],
            );
            $this->userDetails[] = $userDetail;
        }
    }

    public function getAuthorizeStatus()
    {
        $deviceAuthorizeCollection = $this->connectObj->fetchDeviceInfoCollection();
        foreach ($this->userDetails as &$item) {
            $singleUserDetailQuery = $this->getQueryBirthZeroInProduct($item['id']);
            $singleUserDetail = $this->connectObj->fetchCnt($singleUserDetailQuery);
            $channelId = -1;
            if (isset($singleUserDetail['chnid'])) {
                $channelId = $singleUserDetail['chnid'];
            }
            $item['appId'] = $singleUserDetail['appid'];
            $item['visit_on'] = $singleUserDetail['visit_on'];
            $item['create_on'] = $singleUserDetail['create_on'];
            $item['channelId'] = $channelId;
            list($item['hasBind'], $item['hasView']) = $this->getUserBindWeChartPublicDetail($item['id']);
            $query = array('_id' => $item['udid']);
            $userDeviceAuth = $deviceAuthorizeCollection->findOne($query);
            $contactAuth = -1;
            if (!empty($userDeviceAuth)) {
                $contactAuth = $userDeviceAuth['combineAuthorizeStatus'];
            }
            $item['contactAuth'] = $contactAuth;
        }
    }

    public function getUserBindWeChartPublicDetail($userId)
    {
        $weChartItem = array(
            'hasBind' => 0,
            'hasView' => 0,
        );
        $bindResult = $this->connectObj->fetchCnt($this->getQueryHasBindWeChart($userId));
        if ($bindResult) {
            $weChartItem['hasBind'] = 1;
        }
        $result = $this->connectObj->fetchCnt($this->getQueryHasViewWeChartPublic($userId));
        if ($result) {
            $weChartItem['hasView']  = 1;
        }
        return $weChartItem;
    }
}