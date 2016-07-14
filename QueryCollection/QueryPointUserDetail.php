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

    public function getUserList()
    {
        $this->userList = array(378464,378460,135947);
        return $this->userList;
    }
}
$userDetail = new QueryPointUserDetail();
$userDetail->main();