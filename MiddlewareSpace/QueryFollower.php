<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/30
 * Time: 上午11:32
 */

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilTool;
use UtilSpace\UtilSqlTool;

class QueryFollower
{
    use UtilTool;
    use UtilSqlTool;

    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function getUserList()
    {
        $maxUserIdQuery = $this->getQueryMaxUserId();
        $maxUserId = $this->connectObj->fetchCnt($maxUserIdQuery);
        $maxTable = $this->get_number_birthday_number($maxUserId['id']);
        $defaultRank = 0;
        $userItems = array();
        while ($defaultRank < $maxTable) {
            $currentRankUserId = ($defaultRank + 1) * 50000 - 15000;
//            $currentRankUserId = mt_rand($defaultRank, ($defaultRank + 1) * 50000);
            $userLists = $this->getRankUserLists($currentRankUserId);
            foreach ($userLists as $item) {
                if(!$item['phone']) {
                    continue;
                }
                $userItems[] = array(
                    'uid' => $item['id'],
                    'phone' => $item['phone'],
                    'fc' => $this->getPhoneFans($item['phone']),
                );
            }
            $defaultRank += 1;
        }
        return $userItems;
    }

    public function getRankUserLists($randomUserId)
    {
        $userListsQuery = $this->getQueryRandomRegisterStatic($randomUserId);
        $userLists = $this->connectObj->fetchAssoc($userListsQuery);
        return $userLists;

    }
    
    public function getPhoneFans($phone) 
    {
        $followerRedis = $this->connectObj->redisConnect();
        $fans = $followerRedis->sMembers($this->_follower_key($phone));
        return count($fans);
    }
}