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

    public function getPointDayFreshUserGroupData(\DateTime $pointDate)
    {
        $pointStamp = $pointDate->getTimestamp();
        $userQuery = $this->getQueryRegisterByCreateOn($pointStamp);
        $userItems = $this->connectObj->fetchAssoc($userQuery);
        foreach ($userItems as $item) {
            $userId = $item['id'];
            $this->newFreshUsers[] = $userId;
        }
    }

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
     * 新用户创建生日群的群数
     * @param $userItems
     * @param \Datetime
     * @return array
     */
    public function getBuildBirthGroupUserCnt($userItems = array(), \DateTime $pointDate)
    {
        $birthGroupCnt = 0;
        $birthDefaultGroupCnt = 0;
        // 用户创建自定义群
        $userBuildQuery = $this->getQueryUserBuildBirthGroup($userItems, $pointDate->getTimestamp());
        $userBuilders = $this->connectObj->fetchAssoc($userBuildQuery);
        foreach ($userBuilders as $builder) {
            $birthGroupCnt += $builder['buildCnt'];
        }
        // 用户创建默认群
        $userBuildDefaultQuery = $this->getQueryUserBuildBirthGroup($userItems, $pointDate->getTimestamp(), true);
        $userDefaultBuilder = $this->connectObj->fetchAssoc($userBuildDefaultQuery);
        foreach ($userDefaultBuilder as $defaultBuilder) {
            $birthDefaultGroupCnt += $defaultBuilder['buildCnt'];
        }
        // 新用户中有多少参加生日群创建(groupBy)
        $userBuildCntQuery = $this->getQueryPointDayUserCnt($pointDate->getTimestamp(), $userItems);
        $userBuildCntResult = $this->connectObj->fetchAssoc($userBuildCntQuery);
        return array(
            'fresh_user_cnt' => count($userItems),
            'birth_custom_group_cnt' => $birthGroupCnt,
            'birth_default_group_cnt' => $birthDefaultGroupCnt,
            'user_build_cnt' => count($userBuildCntResult),
        );
    }

    public function getGroupMemberCnt($userItems = array(), \DateTime $pointDate)
    {
        $memberCnt = 0;
        $defaultMemberCnt = 0;
        // 用户当天创建自定义群的总成员数
        $groupMemberQuery = $this->getQueryGroupMembers($userItems, $pointDate->getTimestamp());
        $groupMembers = $this->connectObj->fetchAssoc($groupMemberQuery);
        foreach ($groupMembers as $member) {
            $memberCnt += $member['member_cnt'];
        }
        // 用户当天创建默认群的总成员数
        $defaultGroupMemberQuery = $this->getQueryGroupMembers($userItems, $pointDate->getTimestamp(), true);
        $defaultGroupMembers = $this->connectObj->fetchAssoc($defaultGroupMemberQuery);
        foreach ($defaultGroupMembers as $defaultMember) {
            $defaultMemberCnt += $defaultMember['member_cnt'];
        }
        return array(
            'default_member' => $defaultMemberCnt,
            'custom_member' => $memberCnt
        );
    }

    /**
     * 获取当天参加生日群创建的总用户数
     * @param \DateTime $pointDate
     * @return mixed
     */
    public function getSummationDayUsers(\DateTime $pointDate)
    {
        $summationUsersQuery = $this->getQueryPointDayUserCnt($pointDate->getTimestamp());
        $summationUsersCnt = $this->connectObj->fetchAssoc($summationUsersQuery);
        return count($summationUsersCnt);
    }

    /**
     * 获取当天总的生日群数(默认群和自定义群)
     * @param \DateTime $pointDate
     * @return array
     */
    public function getSummationDayGroupCnt(\DateTime $pointDate)
    {
        $summationGroupCnt = 0;
        $summationDefaultGroupCnt = 0;
        $summationGroupQuery = $this->getQueryPointUserBirthGroup($pointDate->getTimestamp());
        $summations = $this->connectObj->fetchAssoc($summationGroupQuery);
        foreach ($summations as $item) {
            $summationGroupCnt += $item['buildCnt'];
        }
        $summationGroupDefaultQuery = $this->getQueryPointUserBirthGroup($pointDate->getTimestamp(), true);
        $groupDefaulters = $this->connectObj->fetchAssoc($summationGroupDefaultQuery);
        foreach ($groupDefaulters as $defaulter) {
            $summationDefaultGroupCnt += $defaulter['buildCnt'];
        }
        return array(
            'default_group_cnt' => $summationDefaultGroupCnt,
            'custom_group_cnt' => $summationGroupCnt
        );
    }

    /**
     * 获取当天总的生日群成员数(自定义和默认)
     * @param \DateTime $pointDate
     * @return array
     */
    public function getSummationDayGroupMemberCnt(\DateTime $pointDate)
    {
        $summationGroupMemberCnt = 0;
        $summationDefaultGroupMemberCnt = 0;
        $summationGroupMemberQuery = $this->getQueryPointUserGroupMember($pointDate->getTimestamp());
        $summationGroupMemberResult = $this->connectObj->fetchAssoc($summationGroupMemberQuery);
        foreach ($summationGroupMemberResult as $item) {
            $summationGroupMemberCnt += $item['member_cnt'];
        }
        $summationDefaultGroupMemberQuery = $this->getQueryPointUserGroupMember($pointDate->getTimestamp(), true);
        $summationDefaultGroupMemberResult = $this->connectObj->fetchAssoc($summationDefaultGroupMemberQuery);
        foreach ($summationDefaultGroupMemberResult as $item) {
            $summationDefaultGroupMemberCnt += $item['member_cnt'];
        }
        return array(
            'default_group_members' => $summationDefaultGroupMemberCnt,
            'custom_group_members' => $summationGroupMemberCnt
        );
    }
}