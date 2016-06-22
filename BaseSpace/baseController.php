<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/22
 * Time: 下午2:41
 */

namespace BaseSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class baseController
{
    use UtilSqlTool;
    use UtilTool;
    
    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function getContactAuthorize($udid)
    {
        $collection = $this->connectObj->fetchDeviceInfoCollection();
        $query = array('_id' => $udid);
        $authorizeDetail = $collection->findOne($query);
        $authorize = -1;
        if ($authorizeDetail) {
            $authorize = $authorizeDetail['combineAuthorizeStatus'];
        }
        return $authorize;
    }

    public function getBirthCntDetail($userId)
    {
        $number = $this->get_number_birthday_number($userId);
        $userBackUpBirthQuery = $this->getQueryBackUpBirthDetail($number, $userId);
        $result = $this->connectObj->fetchCnt($userBackUpBirthQuery);
        return $result['cnt'];
    }

}