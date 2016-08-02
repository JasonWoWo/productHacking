<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/13
 * Time: 下午2:32
 */

namespace QueryCollection;

use MiddlewareSpace\UserDetails;

require __DIR__ . '/../Bootstrap.php';

class QueryPointUserDetail extends UserDetails
{
    public $userList = array();

    public function main()
    {
        $this->getUserBirthDetail($this->getUserList());
        $this->getUserConsumeCnt();
        $this->getUserBackUpDetail();
        $userDetails = $this->getUserDetails();
        echo "ID;Year;Month;Day;Is_Lunar;AppId;ChnId;AllCnt;AbCnt;YabCnt;AddCnt;ConsumeCnt \n";
        foreach ($userDetails as $item) {
            echo sprintf("%d;%d;%d;%d;%d;%d;%d;%d;%d;%d;%d;%d \n",
                $item['id'], $item['birth_y'], $item['birth_m'], $item['birth_d'], $item['birth_is_lunar'], 
                $item['appid'], $item['chnid'], $item['all'], $item['ab'], $item['yab'], $item['add'], $item['consumeCnt']);
        }
    }
    
    public function getRegisterCoreTask()
    {
        $currentDate = new \DateTime(date('Y-m-d'));
        $currentDate->modify('-1 day');
        $this->getUnCoreUser($currentDate->getTimestamp(), 0, -1, 5);
        // 获取用户的通讯录授权
        $this->getAuthorizeStatus();
        // 获取用户备份生日的src分布
        $this->getUserBackUpDetail();
        // 获取消费情况
        $this->getUserConsumeCnt();
        echo "uid;udid;max_bct;yab;ab;add;onJuly;appId;channelId;hasBind;hasView;contactAuth;visitOn;consumeCnt \n";
        $userDetails = $this->getUserDetails();
        foreach ($userDetails as $item) {
            echo sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s \n", $item['id'], $item['udid'], $item['max_bct'], $item['yab'],
                $item['ab'], $item['add'], $item['onJuly'], $item['appId'], $item['channelId'],
                $item['hasBind'], $item['hasView'], $item['contactAuth'], $item['visit_on'], $item['consumeCnt']);
        }
    }

    public function getUserList()
    {
        $this->userList = array(378464,378460,135947);
        return $this->userList;
    }
}
$userDetail = new QueryPointUserDetail();
$userDetail->getRegisterCoreTask();