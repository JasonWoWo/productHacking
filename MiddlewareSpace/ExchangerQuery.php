<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/21
 * Time: 下午1:31
 */

namespace MiddlewareSpace;

use BaseSpace\baseController;


class ExchangerQuery extends baseController
{
    public $exchangers = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function getDailyRegisters(\DateTime $pointDate)
    {
        $defaultTable = 0;
        while ($defaultTable < 8) {
            $this->getDeviceRegisters($defaultTable, $pointDate->getTimestamp());
            $defaultTable += 1;
        }
    }

    public function getDeviceRegisters($tableNum = 0, $loginStamp = 0)
    {
        $oiDevice = 'oistatistics.st_devices_' . $tableNum;
        $userQuery = $this->getQueryRankUserId($oiDevice, $loginStamp, $loginStamp, true);
        $registers = $this->connectObj->fetchAssoc($userQuery);
        foreach ($registers as $register) {
            $userId = $register['id'];
            $consumeCnt = $this->getRegisterConsumeOnExchange($userId, $loginStamp);
            if ($consumeCnt) {
                $this->exchangers[] = $register;
            }
        }
    }

    public function getRegisterConsumeOnExchange($userId, $consumeStamp = 0)
    {
        $consumeQuery = $this->getQueryFirstExchangeRegister($userId, $consumeStamp);
        $result = $this->connectObj->fetchCnt($consumeQuery);
        return $result['consumeCnt'];
    }
    
    public function getExtraDetail()
    {
        foreach ($this->exchangers as &$item) {
            $item['authorize'] = $this->getContactAuthorize($item['udid']);
            $item['backUpBirths'] = $this->getBirthCntDetail($item['id']);
        }
    }
    
    public function getOrderUidDetail($orderItems = array())
    {
        $orderDetailInfo = array();
        $orderUsersQuery = $this->getQueryOrderUserDetail($orderItems);
        $users = $this->connectObj->fetchAssoc($orderUsersQuery);
        foreach ($users as $user) {
            $userDetails = $this->getUserDetail($user['uid']);
            if (!preg_match('/^[1][35678][0-9]{9}$/', $userDetails['phone'])) {
                continue;
            }
            $userDetails['orderId'] = $user['orderId'];
            $send = $this->getUserSendSms($userDetails['phone']);
            $userDetails['active_send'] = $send['active_send'];
            $userDetails['push_send'] = $send['push_send'];
            $orderDetailInfo[] = $userDetails;
        }
        return $orderDetailInfo;
    }

    public function getUserSendSms($phone)
    {
        $send = array(
            'active_send' => 0,
            'push_send' => 0
        );
        $activationCollection = $this->connectObj->fetchUsersAwakenCollection();
        $query = array('_id' => $phone);
        $activation = $activationCollection->findOne($query);
        if ($activation) {
            $send['active_send'] = is_null($activation['send_on']) ? 0 : 1;
        }
        $pushCollection = $this->connectObj->fetchUnRegisterInfoCollection();
        $pusher = $pushCollection->findOne($query);
        if ($pusher) {
            $send['push_send'] = $pusher['send'];
        }
        return $send;
    }

    public function getUserDetail($uid)
    {
        $userDetailQuery = $this->getQueryBirthZeroInProduct($uid);
        $users = $this->connectObj->fetchAssoc($userDetailQuery);
        return $users;
    }
    
}