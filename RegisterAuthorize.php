<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/9
 * Time: 下午4:54
 */
include __DIR__ ."/middleware/Authorize.php";
class RegisterAuthorize extends Authorize
{
    const USER_REGISTER_AUTHORIZE = 'user_device_authorize_statis';
    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }

    public function registerAuthorizeMain()
    {
        $current = new \DateTime();
        $current->modify('-1 days');
        $this->getRegisterAuthorize($current->getTimestamp());
        $this->getMongoAuthorize();
        $paramList = $this->getPlatformCntList();
        $paramList['create_on'] = "'". $current->format('Y-m-d') ."'";
        if (empty($paramList)) {
            echo "==== UnCatch Authorize On Open Please Check! \n";
            return;
        }
        $paramList['create_on'] = "'" . $current->format('Y-m-d') ."'";
        $insertSql = $this->common->insertParamsQuery(self::USER_REGISTER_AUTHORIZE, $paramList);
        $query = $this->common->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $paramList['create_on'] . " authorize Insert " . self::USER_REGISTER_AUTHORIZE . " Success !!! \n";
        }
    }
}
$authorize = new RegisterAuthorize();
$authorize->registerAuthorizeMain();