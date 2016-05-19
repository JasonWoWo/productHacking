<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/9
 * Time: 下午4:54
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\Authorize;

class RegisterAuthorize extends Authorize
{
    const USER_REGISTER_AUTHORIZE = 'user_device_authorize_statis';

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
        $insertSql = $this->connectObj->insertParamsQuery(self::USER_REGISTER_AUTHORIZE, $paramList);
        $query = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $paramList['create_on'] . " authorize Insert " . self::USER_REGISTER_AUTHORIZE . " Success !!! \n";
        }
    }
}
$authorize = new RegisterAuthorize();
$authorize->registerAuthorizeMain();