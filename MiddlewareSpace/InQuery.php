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
        $cnt = 0;
        $userItemsQuery = $this->getQueryWeChartQuestByAddOn($pointTimeStamp);
        $result = $this->connectObj->fetchAssoc($userItemsQuery);
        foreach ($result as $item) {
            $cnt += $this->getUserAddWeChatCnt($item['userid']);
        }
        return array(
            'cnt' => $cnt,
            'userCnt' => count($result)
        );
    }

    public function getUserAddWeChatCnt($userId)
    {
        $number = $this->get_number_birthday_number($userId);
        $query = $this->getQueryBackUpBirthDetail($number, $userId, 'oi');
        $result = $this->connectObj->fetchCnt($query);
        return $result['cnt'];
    }
}