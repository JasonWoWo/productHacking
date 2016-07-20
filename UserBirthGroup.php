<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/7/18
 * Time: 上午11:04
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\UserBirthGroupQuery;

class UserBirthGroup extends UserBirthGroupQuery
{
    const BIRTH_GROUP_TABLE = 'daily_birth_group_statis';
    
    public function getGroupDetail($extendStamp = 0)
    {
        $timeStamp = empty($extendStamp) ? time() : $extendStamp;
        $pointDate = new \DateTime(date('Y-m-d', $timeStamp));
        $pointDate->modify('-1 day');
        $params = array();
        $this->getPointDayBirthGroupInfo($pointDate);
        $params['create_on'] = "'{$pointDate->format('Y-m-d')}'";
        $groupBuildItems = $this->getBuildBirthGroupUserCnt($this->newFreshUsers, $pointDate);
        $params['fresh_user_cnt'] = $groupBuildItems['fresh_user_cnt'];
        $params['user_build_cnt'] = $groupBuildItems['user_build_cnt'];
        $params['birth_group_cnt'] = $groupBuildItems['birth_group_cnt'];
        $groupMembers = $this->getGroupMemberCnt($this->newFreshUsers, $pointDate);
        $params['group_member_cnt'] = $groupMembers;
        $this->insertCore(self::BIRTH_GROUP_TABLE, $params);
        echo "fresh_user_cnt: {$groupBuildItems['fresh_user_cnt']} user_cnt: {$groupBuildItems['user_build_cnt']} | buildGroupCnt: {$groupBuildItems['birth_group_cnt']} | groupMemberCnt: {$groupMembers} \n";
    }
    
    public function getWeChartGroupDetail($extendStamp = 0)
    {
        $timeStamp = empty($extendStamp) ? time() : $extendStamp;
        $pointDate = new \DateTime(date('Y-m-d', $timeStamp));
        $pointDate->modify('-1 day');
        $this->getRegisterOnWeChart($pointDate);
        $params['create_on'] ="'{$pointDate->format('Y-m-d')}'";
        $weChartBuildItems = $this->getBuildBirthGroupUserCnt($this->weChartUsers, $pointDate);
        $params['fresh_user_cnt'] = $weChartBuildItems['fresh_user_cnt'];
        $params['user_build_cnt'] = $weChartBuildItems['user_build_cnt'];
        $params['birth_group_cnt'] = $weChartBuildItems['birth_group_cnt'];
        $weChartGroupMembers = $this->getGroupMemberCnt($this->weChartUsers, $pointDate);
        $params['group_member_cnt'] = $weChartGroupMembers;
        //$this->insertCore(self::BIRTH_GROUP_TABLE, $params);
        echo "fresh_user_cnt: {$weChartBuildItems['fresh_user_cnt']} user_cnt: {$weChartBuildItems['user_build_cnt']} | buildGroupCnt: {$weChartBuildItems['birth_group_cnt']} | groupMemberCnt: {$weChartGroupMembers} \n";
    }

}
$group = new UserBirthGroup();
$group->getGroupDetail();
$group->getWeChartGroupDetail();