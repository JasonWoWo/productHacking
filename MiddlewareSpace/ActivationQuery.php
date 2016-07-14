<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/7/14
 * Time: 下午1:54
 */

namespace MiddlewareSpace;


use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class ActivationQuery
{
    use UtilSqlTool;
    use UtilTool;

    public $connectObj;
    
    /**
     * @var \MongoCollection
     */
    public $activationCollection;
    
    public $activeUsers = array();

    public function __construct()
    {
        $this->connectObj = new Common();
        $this->activationCollection = $this->connectObj->fetchUsersAwakenCollection();
    }

    public function getActivationSendUsers(\DateTime $currentDate)
    {
        $sendEndDate = clone $currentDate;
        $sendStartDate = $currentDate->modify('-1 day');
        $query = array(
            'send_on' => array('$gte' => $sendStartDate, '$lte' => $sendEndDate)
        );
        $sendedUsers = $this->activationCollection->find($query);
        if (empty($sendedUsers)) {
            return 0;
        }
        foreach ($sendedUsers as $item) {
            //查询用户的访问时间,更新数据
            $userId = $item['user_id'];
            $isActive = true;
            $userDetail = $this->getUserDetails($userId);
            if ($userDetail['visit_on']->format('Y-m-d') != $sendStartDate->format('Y-m-d')) {
                continue;
            }
            //获取用户的设备信息,是否是新设备
            $isNewDevice = true;
            $deviceDetail = $this->getActivationDevice($userId);
            if ($deviceDetail['create_on'] != $sendStartDate->format('Y-m-d')) {
                $isNewDevice = false;
            }
            //更新用户的信息
            $this->updateActivationInfo($userId, $isActive, $isNewDevice, $userDetail['visit_on']);
        }
    }
    
    public function getUserDetails($userId)
    {
        
        $userDetailQuery = $this->getQueryBirthZeroInProduct($userId);
        $userDetail = $this->connectObj->fetchCnt($userDetailQuery);
        var_dump($userDetail);
        return $userDetail;
    }

    public function getActivationDevice($udid)
    {
        $deviceTableName = $this->buildDeviceTableName($udid);
        $deviceInfoQuery = $this->getQueryDeviceDetailInfo($deviceTableName, $udid);
        $deviceDetail = $this->connectObj->fetchCnt($deviceInfoQuery);
        
        return $deviceDetail;
    }
    
    public function updateActivationInfo($uid, $isVisit = false, $isNewDevice = false, \DateTime $visitDate)
    {
        $query = array('user_id' => $uid);
        $updateParams = array(
            'active' => $isVisit,
            'new_device' => $isNewDevice,
            'visit_on' => $visitDate
        );
        try {
            $this->activationCollection->update($query, $updateParams);
        } catch (\MongoException $me) {
            echo 'Mongo Exception: '.$me->getMessage().' '.__FILE__.':'.__LINE__;
        }

    }

    public function fetchAwakenUserId(\DateTime $sendDate)
    {
        $awaken = array();
        $sendStartDate = clone $sendDate;
        $sendEndDate = $sendDate->modify('+1 day');
        $query = array(
            'active' => 1,
            'send_on' => array(
                '$gte' => $sendStartDate,
                '$lte' => $sendEndDate
            ),
        );
        $awakenUser = $this->activationCollection->find($query);
        foreach ($awakenUser as $item) {
            $awaken[] = $item['user_id'];
        }
        return $awaken;
    }

    public function getAwakenUserRetainCnt($awakenUsers = array(), $visitOnTimeStamp = 0)
    {
        $awakenItems = implode(',', $awakenUsers);
        $usersRetainCntQuery = $this->getQueryRegisterVisitCnt($awakenItems, $visitOnTimeStamp);
        $result = $this->connectObj->fetchCnt($usersRetainCntQuery);
        return $result['cnt'];
    }
}