<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/30
 * Time: 上午11:32
 */

namespace MiddlewareSpace;

use BaseSpace\baseController;

class QueryFollower extends baseController
{
    public static $defaultUdid = array('00000000000000000000000000000000');
    /**
     * @param $userId
     * @return int
     */
    public function getUserBindWeChartPublicDetail($userId)
    {
        $hasBind = 0;
        $result = $this->connectObj->fetchCnt($this->getQueryHasViewWeChartPublic($userId));
        if ($result) {
            $hasBind  = 1;
        }
        return $hasBind;
    }
    
    public function getRegisterWeChartItem()
    {
        $result = $this->connectObj->fetchAssoc($this->getQueryWeChartRegisterList(5585077));
        if (!$result) {
            echo "UnCatch user List ! \n";
        }
        foreach ($result as &$item) {
            $item['isDevice'] = 0;
            $udid = $item['udid'];
            if (!empty($udid) && !in_array($udid, self::$defaultUdid)) {
                $item['isDevice'] = 1;
            }
            $phone = $item['phone'];
            $item['followFans'] = $this->getPhoneFans($phone);
            $userid = $item['id'];
            $item['hasBind'] = $this->getUserBindWeChartPublicDetail($userid);
            $item['backBirthCnt'] = $this->getBirthCntDetail($userid);
        }
        return $result;
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