<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/20
 * Time: 下午3:14
 */

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class InQuery
{
    use UtilSqlTool;
    use UtilTool;

    public $connectObj;
    
    public function __construct()
    {
        $this->connectObj = new Common();
    }
    
    public function getWeChartQuestUserItems($pointTimeStamp = 0)
    {
        $userItemsQuery = $this->getQueryWeChartQuestByAddOn($pointTimeStamp);
        $result = $this->connectObj->fetchAssoc($userItemsQuery);
        foreach ($result as &$item) {
            $item['weChatCnt'] = $this->getUserAddWeChatCnt($item['userid']);
            $query = $this->getQueryBirthZeroInProduct($item['userid']);
            $userItem = $this->connectObj->fetchAssoc($query);
            if (empty($userItem['udid'])) {
                $item['udid'] = '';
            } else {
                $item['udid'] = $userItem['udid'];
            }
            if (empty($userItem['appid'])) {
                $item['appid'] = 0;
            } else {
                $item['appid'] = $userItem['appid'];
            }
        }
        return $result;
    }

    public function getUserAddWeChatCnt($userId)
    {
        $number = $this->get_number_birthday_number($userId);
        $query = $this->getQueryBackUpBirthDetail($number, $userId, 'wx');
        $result = $this->connectObj->fetchCnt($query);
        $queryOi = $this->getQueryBackUpBirthDetail($number, $userId, 'oi');
        $resultOi = $this->connectObj->fetchCnt($queryOi);
        $cnt = $result['cnt'] + $resultOi['cnt'];
        return $cnt;
    }
}