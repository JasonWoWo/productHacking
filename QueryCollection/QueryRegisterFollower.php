<?php

namespace QueryCollection;

use MiddlewareSpace\QueryFollower;

require __DIR__ . '/../Bootstrap.php';

// 岳云飞需求:随机统计注册用户中 谁记录我的生日的人数是多少, 0条的比例是多少
class QueryRegisterFollower extends QueryFollower
{
    public function showRandomUserItems()
    {
        $userItems = $this->getUserList();
        echo "uid;phone;fans \n";
        foreach ($userItems as $item) {
            echo sprintf("%d;%s;%s \n", $item['uid'], $item['phone'], $item['fc']);
        }
    }
    
    // 获取40万用户来源是微信注册,且关注微信公众账号的详细情况 : 记了多少条生日, 被记了多少条, 是否激活,
    public function showWeChartRegisterDetail()
    {
        $weChartDetail = $this->getRegisterWeChartItem();
        foreach ($weChartDetail as $item) {
            echo sprintf("%s;%s;%s;%s;%s;%s \n", $item['id'], $item['isDevice'], $item['udid'], $item['hasBind'], $item['backBirthCnt'], $item['followFans']);
        }
    }
}
$query = new QueryRegisterFollower();
//$query->showRandomUserItems();
$query->showWeChartRegisterDetail();