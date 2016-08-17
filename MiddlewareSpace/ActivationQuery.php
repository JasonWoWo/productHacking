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

    /**
     * @var \MongoCollection
     */
    public $inquiryCollection;
    
    public $activeUsers = array();
    
    public $inquiryActivityUser = array();

    public function __construct()
    {
        $this->connectObj = new Common();
        $this->activationCollection = $this->connectObj->fetchUsersAwakenCollection();
        $this->inquiryCollection = $this->connectObj->fetchInquiryAwakeCollection();
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
                echo "phone : {$item['_id']} - SendedUsers cant find index user_id \n";
                continue;
            }
            $userId = $item['user_id'];
            $isActive = true;
            $userDetail = $this->getUserDetails($userId);
            $userVisitOn = new \DateTime($userDetail['visit_on']);
            if ($userVisitOn->format('Y-m-d') != $sendStartDate->format('Y-m-d')) {
                continue;
            }
            $this->activeUsers[] = $userId;
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

    public function getInquiryAwakenUsers(\DateTime $currentData)
    {
        $sendEndDate = clone $currentData;
        $sendStartDate = $currentData->modify('-1 day');
        $query = array(
            'send_on' => array(
                '$gte' => intval($sendStartDate->format('Ymd')), '$lte' => intval($sendEndDate->format('Ymd'))
            )
        );
        $sendSmsItems = $this->inquiryCollection->find($query);
        if (empty($sendSmsItems)) {
            return false;
        }
        foreach ($sendSmsItems as $sender) {
            $userId = $sender['user_id'];
            if (!$userId) {
                echo "phone: {$sender['_id']} InquirySms cant find index user_id \n";
                continue;
            }
            $isActive = 1;
            $userDetail = $this->getUserDetails($userId);
            $userVisitOn = new \DateTime($userDetail['visit_on']);
            if ($userVisitOn->format('Ymd') != $sendStartDate->format('Ymd')) {
                continue;
            }
            $this->inquiryActivityUser[] = $userId;
            $isNewDevice = 0;
            $productSk = 0;
            $udid = $userDetail['udid'];
            if (!empty($udid)) {
                $isNewDevice = 1;
                $deviceDetail = $this->getActivationDevice($udid);
                $productSk = $deviceDetail['product_sk'];
                if ($deviceDetail['create_on'] != $sendStartDate->format('Y-m-d')) {
                    $isNewDevice = 0;
                }
            }
            echo "userId: {$userId}, visit_on: {$userDetail['visit_on']}, isNewDevice:{$isNewDevice} \n";
            $this->updateInquiryActivationInfo($sender['_id'], $isActive, $isNewDevice, $productSk, $userVisitOn->getTimestamp());
        }
    }

    public function updateInquiryActivationInfo($phone, $isVisit = 0, $isNewDevice = 0, $productSk, $visitTimestamp)
    {
        $query = array('_id' => $phone);
        $item = $this->inquiryCollection->findOne($query);
        if (!$item) {
            echo "Uncatch the _id on {$phone}, check please! \n";
        }
        $item['active'] = $isVisit;
        $item['new_device'] = $isNewDevice;
        $item['visit_on'] = intval(date('Ymd', $visitTimestamp));
        $item['appid'] = $productSk;
        try {
            $this->inquiryCollection->update($query, $item);
        } catch (\MongoException $e) {
            echo 'Mongo Exception: '.$e->getMessage().' '.__FILE__.':'.__LINE__;
        }
    }

    public function setInquiryIndex()
    {
        $this->inquiryCollection->createIndex(array('send_on' => 1));
        $this->inquiryCollection->createIndex(array('click' => 1));
        $this->inquiryCollection->createIndex(array('appid' => 1));
        $this->inquiryCollection->createIndex(array('active' => 1));
    }

    public function updateSendOnValue()
    {
        $currentDate = new \DateTime(date('Y-m-d'));
        $currentDate->modify('-1 day');
        $query = array(
            'send_on' => array(
                '$gt' => $currentDate->getTimestamp()
            )
        );
        $senders = $this->inquiryCollection->findOne($query);
        foreach ($senders as $item) {
            $sendOnTimeStamp = $item['send_on'];
            if ($sendOnTimeStamp > 201608117) {
                $this->singleUpdate($item['_id'], intval(date('Ymd', $sendOnTimeStamp)));
            }
        }
    }

    public function singleUpdate($phone, $value)
    {
        $query = array(
            '_id' => $phone
        );
        $update = array(
            '$set' => array(
                'send_on' => $value
            )
        );
        try {
            $this->inquiryCollection->update($query, $update);
        } catch (\MongoException $e) {
            echo 'Mongo Exception: '.$e->getMessage().' '.__FILE__.':'.__LINE__;
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

    public function fetchInquiryUserId(\DateTime $sendDate)
    {
        $inquiryUsers = array();
        $sendStartDate = clone $sendDate;
        $sendEndDate = $sendDate->modify('+1 day');
        $query = array(
            'active' => 1,
            'send_on' => array(
                '$gte' => intval($sendStartDate->format('Ymd')),
                '$lte' => intval($sendEndDate->format('Ymd')),
            ),
        );
        $inquiryItems = $this->inquiryCollection->find($query);
        foreach ($inquiryItems as $item) {
            $inquiryUsers[] = $item['user_id'];
        }
        return $inquiryUsers;
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
                '$gte' => $sendStartDate->format('Y-m-d H:i:s'),
                '$lte' => $sendEndDate->format('Y-m-d H:i:s')
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