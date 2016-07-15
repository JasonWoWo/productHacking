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
            'send_on' => array('$gte' => $sendStartDate->format('Y-m-d H:i:s'), '$lte' => $sendEndDate->format('Y-m-d H:i:s'))
        );
        $sendedUsers = $this->activationCollection->find($query);
        if (empty($sendedUsers)) {
            return 0;
        }
        foreach ($sendedUsers as $item) {
            //查询用户的访问时间,更新数据
            if (empty($item['user_id'])) {
                echo "SendedUsers cant find index user_id \n";
            }
            $userId = $item['user_id'];
            $isActive = true;
            $userDetail = $this->getUserDetails($userId);
            $userVisitOn = new \DateTime($userDetail['visit_on']);
            if ($userVisitOn->format('Y-m-d') != $sendStartDate->format('Y-m-d')) {
                continue;
            }
            //获取用户的设备信息,是否是新设备
            $isNewDevice = 0;
            $udid = $userDetail['udid'];
            if (!empty($udid)) {
                $isNewDevice = 1;
                $deviceDetail = $this->getActivationDevice($udid);
                if ($deviceDetail['create_on'] != $sendStartDate->format('Y-m-d')) {
                    $isNewDevice = 0;
                }
            }
            echo "userId: {$userId}, visit_on: {$userDetail['visit_on']}, isNewDevice:{$isNewDevice} \n";
            //更新用户的信息
            $this->updateActivationInfo($userId, $isActive, $isNewDevice, $userVisitOn);
        }
    }
    
    public function getUserDetails($userId)
    {
        
        $userDetailQuery = $this->getQueryBirthZeroInProduct($userId);
        $userDetail = $this->connectObj->fetchCnt($userDetailQuery);
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
        $item = $this->activationCollection->findOne($query);
        if (!$item) {
            echo "Uncatch the user_id, Dont update \n";
        }
        $item['active'] = $isVisit ? 1 : 0;
        $item['new_device'] = $isNewDevice ? 1 : 0;
        $item['visit_on'] = $visitDate;
        try {
            $this->activationCollection->update($query, $item);
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