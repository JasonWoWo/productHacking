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
}
$query = new QueryRegisterFollower();
$query->showRandomUserItems();