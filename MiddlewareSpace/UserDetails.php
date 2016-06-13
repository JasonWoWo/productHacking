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
        $userBirthDetailQuery = $this->getQueryRegisterBirthDetail($userItemsLists);
        $result = $this->connectObj->fetchAssoc($userBirthDetailQuery);
        foreach ($result as $item) {
            $this->userDetails[] = $item;
        }
    }

    public function getUserConsumeCnt()
    {
        foreach ($this->userDetails as &$item) {
            $consumeCntQuery = $this->getQueryRegisterConsumeCnt($item['id']);
            $result = $this->connectObj->fetchCnt($consumeCntQuery);
            $item['consumeCnt'] = $result['consumeCnt'];
        }
    }
    
    public function getUserBackUpDetail()
    {
        foreach ($this->userDetails as &$item) {
            $birthTable = $this->get_number_birthday_number($item['id']);
            $src = array('ab', 'yab', 'add');
            foreach ($src as $srcList) {
                $item[$srcList] = $this->getBackUpSourceCnt($birthTable, $item['id'], $srcList);
                $item['all'] = $this->getBackUpSourceCnt($birthTable, $item['id']);
            }
            
        }
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
}