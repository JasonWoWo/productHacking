<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/7/18
 * Time: 上午11:05
 */

namespace MiddlewareSpace;


use BaseSpace\baseController;

class UserBirthGroupQuery extends baseController
{
    const WE_CHART_NUMBER = 1007; 
    

    public $newFreshUsers = array();
    
    public $weChartUsers = array();

    public function getPointDayBirthGroupInfo(\DateTime $pointDate)
    {
        $defaultTableIndex = 0;
        $pointStamp = $pointDate->getTimestamp();
        while ($defaultTableIndex < 8) {
            $this->getCurrentTableUsers($defaultTableIndex, $pointStamp);
            $defaultTableIndex ++;
        }
    }
    
    public function getCurrentTableUsers($currentTable = 0, $pointStamp = 0)
    {
        $table = 'oistatistics.st_devices_' . $currentTable;
        $newFreshQuery = $this->getQueryRankUserId($table, $pointStamp, $pointStamp, true);
        $newFreshers = $this->connectObj->fetchAssoc($newFreshQuery);
        foreach ($newFreshers as $item) {
            $this->newFreshUsers[] = $item['id'];
        }
    }
    
    public function getRegisterOnWeChart(\DateTime $pointDate)
    {
        $registerQuery = $this->getQueryRegisterByCreateOn($pointDate->getTimestamp());
        $registers = $this->connectObj->fetchAssoc($registerQuery);
        foreach ($registers as $register) {
            if ($register['appid'] != self::WE_CHART_NUMBER) {
                continue;
            }
            $this->weChartUsers[] = $register['id'];
        }
    }

    /**
     * 新增设备的新注册创建生日群的用户数
     * @param $userItems
     * @param \Datetime
     * @return array
     */
    public function getBuildBirthGroupUserCnt($userItems = array(), \DateTime $pointDate)
    {
        $birthGroupCnt = 0;
        $userBuildQuery = $this->getQueryUserBuildBirthGroup($userItems, $pointDate->getTimestamp());
        $userBuilders = $this->connectObj->fetchAssoc($userBuildQuery);
        $userCnt = count($userBuilders);
        foreach ($userBuilders as $builder) {
            $birthGroupCnt += $builder['buildCnt'];
        }
        return array(
            'birth_group_cnt' => $birthGroupCnt,
            'user_build_cnt' => $userCnt,
        );
    }

    public function getGroupMemberCnt($userItems = array(), \DateTime $pointDate)
    {
        $memberCnt = 0;
        $groupMemberQuery = $this->getQueryGroupMembers($userItems, $pointDate->getTimestamp());
        $groupMembers = $this->connectObj->fetchAssoc($groupMemberQuery);
        foreach ($groupMembers as $member) {
            $memberCnt += $member['member_cnt'];
        }
        return $memberCnt;
    }
}