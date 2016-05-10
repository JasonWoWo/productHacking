<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/10
 * Time: 下午1:51
 */
include __DIR__ ."/middleware/Authorize.php";
class RetainAuthorizeRegister extends Authorize
{
    const USER_REGISTER_AUTHORIZE = 'user_device_authorize_statis';
    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }
    
    public function updateAuthorize()
    {
        $currentDate = new \DateTime();
        $currentDate->modify('-1 day');
        $currentTimestamp = $currentDate->getTimestamp();
        $this->updateBaseAuthorize($currentTimestamp, 0);
        $this->updateBaseAuthorize($currentTimestamp, 1);
        $this->updateBaseAuthorize($currentTimestamp, 2);
        $this->updateBaseAuthorize($currentTimestamp, 3);
    }

    public function updateBaseAuthorize($currentTimestamp = 0, $isRetain = 0)
    {
        $login = $this->common->calculateLoginIn($currentTimestamp, $isRetain);
        $params = array('platform_cnt');
        $where = array('create_on' => "$login");
        $selectQuery = $this->common->selectParamsQuery(self::USER_REGISTER_AUTHORIZE, $params, $where);
        $result = $this->common->fetchAssoc($selectQuery);
        if (empty($result)) {
            echo "UnCatch the result where create_on :" . $login . "In " . self::USER_REGISTER_AUTHORIZE . " \n";
        }
        $this->getRegisterAuthorize(strtotime($login));
        $this->getMongoAuthorize();
        $this->getAuthorize();
        $param = $this->getPlatformAuthorizeOn();
        $paramKey = $this->getAuthorizeKey($isRetain);
        $paramList = array(
            $paramKey[1] => $param['android'],
            $paramKey[2] => $params['iphone'],
        );
        $updateQuery = $this->common->updateParamsQuery(self::USER_REGISTER_AUTHORIZE, $paramList, $where);
//        $query = $this->common->fetchCakeStatQuery($updateQuery);
//        if ($query) {
//            echo " === " . $login . " DeviceAuthorize isRetain : " . $isRetain . " success !!! \n";
//        }
    }

    public function getAuthorizeKey($isRetain)
    {
        $paramsKey = array(
            0 => array(
                1 => 'zero_android_authorize',
                2 => 'zero_iphone_authorize',
            ),
            1 => array(
                1 => 'first_android_authorize',
                2 => 'first_iphone_authorize',
            ),
            2 => array(
                1 => 'second_android_authorize',
                2 => 'second_iphone_authorize',
            ),
            3 => array(
                1 => 'third_android_authorize',
                2 => 'third_iphone_authorize',
            ),
        );
        return $paramsKey[$isRetain];
    }
}