<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/13
 * Time: 下午5:31
 */
namespace UtilSpace;

trait UtilSqlTool
{

    public function getQueryUdidLinkBrand($currentTableName, $udidsList, $brandsList)
    {
        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTableName ." AS s 
        LEFT JOIN oistatistics.st_dim_brand AS b ON s.brand_sk = b.brand_sk 
        WHERE s.udid IN ( ". $udidsList . " ) AND b.brand_sk IN ( " . $brandsList . ")";
        return $query;
    }
    
    public function getQueryVivoFuck($model = 'vivo')
    {
        $query = "SELECT model_sk  FROM `oistatistics`.`st_dim_model` WHERE `model_name` LIKE '%" . $model . "%'";
        return $query;
    }
    
    public function getQueryUdidLinkModel($currentTableName, $udidsList, $modelsList)
    {
        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTableName ." AS s 
        LEFT JOIN oistatistics.st_dim_model AS m ON s.model_sk = m.model_sk 
        WHERE s.udid IN ( ". $udidsList . " ) AND m.model_sk IN ( " . $modelsList . ")";
        return $query;
    }
    
    public function getQueryUdidsLinkProductSk($currentTableName, $udidsList, $productSk = 1002)
    {
        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTableName ." AS s WHERE s.udid IN ( " . $udidsList . " ) AND s.product_sk = " . $productSk;
        return $query;
    }
    
    public function getQueryMaxUserId($defaultTable = 'oibirthday.users')
    {
        $query = "SELECT id FROM " . $defaultTable ." ORDER BY id DESC LIMIT 1";
        return $query;
    }
    
    //  备份生日表在指定日期的用户详情(已去重),获取用户所在的设备表信息和用户设备
    public function getQueryUserItemsOnLunarAndMonthAndDay($currentTableName, $isBirthLunar, $birthM, $birthD)
    {
        $query = sprintf("
        SELECT bcn.userid,u.udid,(CONV(LEFT(u.udid, 1), 16, 10) DIV 2) AS device FROM %s AS bcn LEFT JOIN oibirthday.users AS u ON bcn.userid = u.id 
        WHERE
		 	bcn.`birth_is_lunar` = %d AND bcn.birth_m = %d AND bcn.birth_d = %d AND u.udid != ''
		 GROUP BY
		 	bcn.birth_m, bcn.birth_d, bcn.userid
	",
            $currentTableName,
            $isBirthLunar,
            $birthM, $birthD
        );
        return $query;
    }

    public function getQueryRegisterBindWeChat($timeStamp = 0)
    {
        $loginIn = "TO_DAYS('" . date('Y-m-d', $timeStamp) ."')";
        $query = sprintf("
        SELECT u.udid,(CONV(LEFT(u.udid, 1), 16, 10) DIV 2) AS device 
        FROM oibirthday.users AS u 
        LEFT JOIN oibirthday.sns_auth_info AS sai ON u.id = sai.userid 
        WHERE TO_DAYS(u.`create_on`) = %s AND TO_DAYS(sai.`auth_on`) = %s AND sai.sns_type = 5
        ", $loginIn, $loginIn);
        return $query;
    }

    public function getQueryRegisterBindAndFocusOnWeChat($timeStamp = 0)
    {
        $loginIn = "TO_DAYS('" . date('Y-m-d', $timeStamp) ."')";
        $query = sprintf("
        SELECT u.udid,(CONV(LEFT(u.udid, 1), 16, 10) DIV 2) AS device 
        FROM oibirthday.users AS u 
        LEFT JOIN oibirthday.sns_auth_info AS sai ON u.id = sai.userid LEFT JOIN oibirthday.mp_auth_info AS mai ON sai.sns_id = mai.unionid
        WHERE TO_DAYS(u.`create_on`) = %s AND TO_DAYS(sai.`auth_on`) = %s AND TO_DAYS(mai.`auth_on`) = %s AND sai.sns_type = 5
        ", $loginIn, $loginIn, $loginIn);
        return $query;
    }

    public function getQueryLoginUdidsLinkProductSk($currentTableName, $udidsList, $productSk, $timeStamp = 0)
    {
        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTableName ." AS s LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
        WHERE s.udid IN ( " . $udidsList . " ) AND TO_DAYS(d.datevalue) = " . $this->fetchDateString($timeStamp) . " AND s.product_sk = " . $productSk;
        return $query;
    }

    public function getQueryLoginUdidLinkModel($currentTableName, $udidsList, $modelsList, $timeStamp = 0)
    {
        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTableName ." AS s LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk
        LEFT JOIN oistatistics.st_dim_model AS m ON s.model_sk = m.model_sk 
        WHERE s.udid IN ( ". $udidsList . " ) AND m.model_sk IN ( " . $modelsList . ") AND TO_DAYS(d.datevalue) = " . $this->fetchDateString($timeStamp);
        return $query;
    }
    
    public function getQueryLoginUdidLinkBrand($currentTableName, $udidsList, $brandsList, $timeStamp = 0)
    {
        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTableName ." AS s LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk
        LEFT JOIN oistatistics.st_dim_brand AS b ON s.brand_sk = b.brand_sk 
        WHERE s.udid IN ( ". $udidsList . " ) AND b.brand_sk IN ( " . $brandsList . ") AND TO_DAYS(d.datevalue) = " . $this->fetchDateString($timeStamp);
        return $query;
    }
    
    public function getQueryRankUserId($currentTable, $loginStartStamp = 0, $loginEndStamp = 0, $isUserList = false)
    {
        $loginStartString = $this->fetchDateString($loginStartStamp);
        $loginEndString = $this->fetchDateString($loginEndStamp);
        $queryParams = " MAX(u.id) AS maxId, MIN(u.id) AS minId  ";
        if ($isUserList) {
            $queryParams = " u.id, u.udid, s.birthcnt ";
        }
        $query = sprintf("
        SELECT %s
        FROM oibirthday.users AS u 
        LEFT JOIN %s AS s ON u.udid = s.udid
        LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
        WHERE TO_DAYS(d.datevalue) >= %s AND TO_DAYS(d.datevalue) <= %s 
        AND TO_DAYS(u.create_on) >= %s 
        AND TO_DAYS(u.create_on) <= %s", $queryParams, $currentTable, $loginStartString, $loginEndString, $loginStartString, $loginEndString);
        echo $query . "\n";
        return $query;
    }

    /**
     * 活跃用户是老用户的生日数据
     * @param int $loginStartStamp
     * @param int $loginEndStamp
     * @param bool $isUserList
     */
    public function getQueryDAUFromOld($loginStartStamp = 0, $loginEndStamp = 0, $isUserList = false)
    {
        $loginStartString = $this->fetchDateString($loginStartStamp);
        $loginEndString = $this->fetchDateString($loginEndStamp);
        $queryParams = " MAX(u.id) AS maxId, MIN(u.id) AS minId  ";
        if ($isUserList) {
            $queryParams = " u.id ";
        }
        $query = sprintf("
        SELECT %s
        FROM oibirthday.users AS u 
        WHERE TO_DAYS(u.create_on) < %s
        AND TO_DAYS(u.visit_on) >= %s 
        AND TO_DAYS(u.visit_on) <= %s", $queryParams, $loginStartString, $loginStartString, $loginEndString);
        echo $query . "\n";
        return $query;
    }

    public function getQueryBirthListSrc($currentBirthdayTable, $userIdList, $src, $withPhone = false)
    {
        $query = "SELECT COUNT(*) AS cnt FROM ". $currentBirthdayTable. " AS b WHERE b.userid IN ( ". $userIdList ." ) AND b.src LIKE '" . $src ."%'";
        if ($withPhone) {
            $query .= " AND b.phone != ''";
        }
        return $query;
    }
    
    public function getQueryBirthSummation($currentBirthdayTable, $userIdList)
    {
        $query = "SELECT COUNT(*) AS cnt FROM ". $currentBirthdayTable. " AS b WHERE b.userid IN ( ". $userIdList ." )";
        return $query;
    }

    public function getQueryBirthZeroInProduct($userIdList)
    {
        $query = "SELECT u.appid, u.phone, u.udid, u.id, DATE_FORMAT(u.create_on, '%Y-%m-%d') AS create_on, u.visit_on FROM oibirthday.users AS u WHERE u.id IN (" . $userIdList .") ";
        return $query;
    }

    public function getQueryCollectionRegisterRatio($currentTable, $pointTimeStamp = 0)
    {
        $dayString = $this->fetchDateString($pointTimeStamp);
        $query = "SELECT s.product_sk, s.udid, DATE_FORMAT(u.create_on, '%Y-%m-%d') AS create_on, u.id FROM " . $currentTable ." AS s
        LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
        LEFT JOIN oibirthday.users AS u ON s.udid = u.udid
        WHERE TO_DAYS(d.datevalue) = " . $dayString . " AND TO_DAYS(u.visit_on) = " . $dayString;
        return $query;
    }

    public function getQueryCollectionUdid($currentTable, $pointTimeStamp = 0)
    {
        $dayString = $this->fetchDateString($pointTimeStamp);
        $query = "SELECT s.product_sk, s.udid FROM " . $currentTable ." AS s
        LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
        WHERE TO_DAYS(d.datevalue) = " . $dayString;
        return $query;
    }
    
    public function getQueryDeviceDetailInfo($currentTable, $udid) 
    {
        $query = "SELECT s.product_sk, s.udid, DATE_FORMAT(d.datevalue, '%Y-%m-%d') AS create_on 
                  FROM {$currentTable} AS s LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk WHERE s.udid = '{$udid}'";
        return $query;
    }

    public function getQueryUnRegisterCollection($currentTable, $maxId = 0)
    {
        $query = "SELECT substring(b.phone, 1, 11) AS phone, 2016 - b.birth_y AS age, b.id, b.birth_y, b.birth_m, b.birth_d, b.birth_is_lunar, b.gender FROM " . $currentTable . " AS b 
        WHERE NOT EXISTS (SELECT 1 FROM oibirthday.users AS u WHERE u.phone = b.phone) AND b.birth_y <= 1998 AND b.birth_y >= 1981 AND b.id > ". $maxId ." AND b.phone REGEXP '^[1][35678][0-9]{9}$' ORDER BY b.id ASC LIMIT 5000";
        return $query;
    }

    public function getQueryUnRegisterMaxId($currentTable)
    {
        $query = "SELECT MAX(b.id) AS maxId FROM ". $currentTable ." AS b 
        WHERE NOT EXISTS ( SELECT 1 FROM oibirthday.users AS u WHERE u.phone = b.phone ) AND b.birth_y <= 1998 AND b.birth_y >= 1981 AND b.phone REGEXP '^[1][35678][0-9]{9}$'";
        return $query;
    }
    
    public function getQueryRandomRegisterStatic($useId = 0)
    {
        $query = "SELECT u.id, u.phone FROM oibirthday.users AS u WHERE u.id >= " . $useId . " ORDER BY u.id ASC LIMIT 50";
        return $query;
    }

    // 当日新增设备中完成核心任务的设备数据
    public function getQueryAddDevicesCoreTaskCount($currentTable, $udidLists, $pointTimeStamp = 0)
    {
        $dayString = $this->fetchDateString($pointTimeStamp);
        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTable . " AS s LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
        WHERE s.birthcnt >= 6 AND TO_DAYS(d.datevalue) = " . $dayString . " AND s.udid NOT IN ( " . $udidLists . " )";
        return $query;
    }

    // 注册用户完成生日信息的数量
    public function getQueryRegistersCompleteInfo($pointTimeStamp = 0)
    {
        $dayString = $this->fetchDateString($pointTimeStamp);
        $query = "SELECT COUNT(*) AS cnt 
        FROM oibirthday.`users` AS u WHERE u.id > 4900000 AND u.name != '' AND u.gender != -1 AND u.birth_y != 0 
        AND u.birth_m != 0 AND u.birth_d != 0 AND u.appid IN (1001, 1002) AND TO_DAYS(u.create_on) = " . $dayString;
        return $query;
    }

    // 新注册用户的中老设备
    public function getQueryRegisterForODevice($currentTable, $pointTimeStamp = 0)
    {
        $dayString = $this->fetchDateString($pointTimeStamp);
        $query = "SELECT u.id FROM oibirthday.users AS u 
        LEFT JOIN " . $currentTable . " AS s ON s.udid = u.udid 
        LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
        WHERE TO_DAYS(d.datevalue) < " . $dayString . " AND TO_DAYS(u.create_on) = " . $dayString;
        echo $query . " \n";
        return $query;
    }
    
    public function getQueryRegisterVisitCnt($userList, $pointTimeStamp = 0, $isDetail = false)
    {
        $dayString = $this->fetchDateString($pointTimeStamp);
        $list = ' COUNT(*) AS cnt ';
        if ($isDetail) {
            $list = ' u.id ';
        }
        $query = "SELECT {$list} FROM oibirthday.users AS u WHERE u.id IN ( {$userList} ) AND TO_DAYS(u.visit_on) = {$dayString}";
        echo $query . " \n";
        return $query;
    }

    public function getQueryRegisterBirthDetail($userList)
    {
        $query = "SELECT u.id, u.birth_y, u.birth_m, u.birth_d, u.birth_is_lunar, u.appid, u.chnid, u.udid FROM oibirthday.users AS u WHERE u.id IN ( " . $userList . " ) ";
        return $query;
    }

    public function getQueryRegisterConsumeCnt($userId)
    {
        $query = "SELECT COUNT(*) AS consumeCnt FROM oiplatform.order_details AS o WHERE o.uid = " . $userId . " AND o.pay_time >= o.order_time ";
        return $query;
    }

    public function getQueryBackUpBirthDetail($birthNum = 0, $userId, $src = null)
    {
        $oibirthdayTable = "oibirthday.br_birthdays_" . $birthNum;
        $query = "SELECT COUNT(*) cnt FROM ". $oibirthdayTable . " AS b WHERE b.userid = ". $userId;
        if ($src) {
            $query .= " AND b.src LIKE '" . $src . "%'";
        }
        return $query;
    }

    public function getQueryRegisterByCreateOn($createOnStartTimeStamp = 0, $createOnEndTimeStamp = 0)
    {
        $createOn = $this->fetchDateString($createOnStartTimeStamp);
        if (empty($createOnEndTimeStamp)) {
            $createOnEnd = $createOn;
        } else {
            $createOnEnd = $this->fetchDateString($createOnEndTimeStamp);
        }
        $query = "SELECT u.id, u.udid, DATE_FORMAT(u.create_on, '%Y-%m-%d') AS create_on, DATE_FORMAT(u.visit_on, '%Y-%m-%d') AS visit_on, u.appid, u.chnid FROM oibirthday.users AS u WHERE u.id > 5000000 ";
        $query .= " AND TO_DAYS(u.create_on) >= {$createOnEnd} AND TO_DAYS(u.create_on) <= {$createOn}";
        return $query;
    }
    
    public function getQueryRegisterByUserAndCreateOn($userIdString, $createOnTimeStamp)
    {
        $createOn = $this->fetchDateString($createOnTimeStamp);
        $query = "SELECT u.id, u.appid, u.chnid FROM oibirthday.users AS u WHERE u.id IN ( {$userIdString} ) AND TO_DAYS(u.create_on) = {$createOn} ";
        return $query;
    }

    public function getQueryAddBirthDaySrcCnt($birthTable, $addOnStamp = 0, $src)
    {
        $addOn = $this->fetchDateString($addOnStamp);
        $query = "SELECT COUNT(*) AS cnt FROM {$birthTable} AS b WHERE TO_DAYS(b.add_on) = {$addOn} AND b.src LIKE '{$src}%'";
        echo $query . "\n";
        return $query;
    }

    public function getQueryAddBirthUniqueUserCnt($birthTable, $addOnStamp = 0)
    {
        $addOn = $this->fetchDateString($addOnStamp);
        $query = "SELECT b.userid, COUNT(*) AS cnt FROM {$birthTable} AS b WHERE TO_DAYS(b.add_on) = {$addOn} GROUP BY b.userid ";
        echo $query . "\n";
        return $query;
    }
    
    public function getQueryRegisterRetainByVisitOn($loginStartOnStamp = 0, $loginEndOnStamp = 0, $visitStartOnStamp = 0, $visitEndOnStamp = 0, $chnid = array(), $appid = array())
    {
        $loginStartOn = $this->fetchDateString($loginStartOnStamp);
        $loginEndOn = $this->fetchDateString($loginEndOnStamp);
        $visitStartOn = $this->fetchDateString($visitStartOnStamp);
        $visitEndOn = $this->fetchDateString($visitEndOnStamp);
        $query = "SELECT COUNT(*) AS cnt FROM oibirthday.users AS u 
                  WHERE u.id > 5160000 AND TO_DAYS(u.create_on) >= {$loginStartOn} 
                  AND TO_DAYS(u.create_on) <= {$loginEndOn} AND TO_DAYS(u.visit_on) >= {$visitStartOn} AND TO_DAYS(u.visit_on) <= {$visitEndOn}";
        if ($chnid) {
            $channel = implode(',', $chnid);
            $query .= " AND u.chnid IN ({$channel}) ";
        }
        if ($appid) {
            $app = implode(',', $appid);
            $query .= " AND u.appid IN ({$app}) ";
        }
        echo $query . "\n";
        return $query;
    }

    public function getQueryWeChartQuestByAddOn($addOnTimeStamp = 0)
    {
        $addOn = $this->fetchDateString($addOnTimeStamp);
        $query = "SELECT m.userid, count(*) AS cnt FROM oibirthday.msg_newbirth AS m WHERE m.src = 3 AND m.id > 12803900 AND TO_DAYS(m.add_on) = {$addOn} GROUP BY m.userid";
        return $query;
    }

    public function getQueryFirstExchangeRegister($userId, $consumeStamp = 0)
    {
        $consume = $this->fetchDateString($consumeStamp);
        $query = "SELECT COUNT(*) AS consumeCnt
        FROM oiplatform.order_details AS o 
        LEFT JOIN oiplatform.order_goods_list AS g ON o.id = g.order_id 
        LEFT JOIN oiplatform.product AS p ON g.goods_id = p.id 
        WHERE o.uid = {$userId} AND o.pay_time >= o.order_time AND TO_DAYS(o.pay_time) = {$consume} AND p.special_type = 1";
        return $query;
    }

    public function getQueryUserBuildBirthGroup($users = array(), $pointStamp = 0)
    {
        $userItems = implode(',', $users);
        $query = "SELECT g.masterid, COUNT(*) AS buildCnt FROM oibirthday.br_group AS g WHERE g.masterid IN ({$userItems}) AND g.delete_at IS NULL GROUP BY g.masterid";
        return $query;
    }

    public function getQueryGroupMembers($users = array(), $pointStamp = 0)
    {
        $userItems = implode(',', $users);
        $query = "SELECT g.masterid, count(*) AS member_cnt FROM oibirthday.br_group AS g LEFT JOIN oibirthday.br_group_member AS m ON g.id = m.group_id 
                  WHERE g.masterid IN ({$userItems}) AND m.id IS NOT NULL AND m.delete_at IS NULL AND g.delete_at IS NULL GROUP BY g.masterid";
        return $query;
    }

    public function getQueryOrderUserDetail($orderItems = array())
    {
        $orders = implode(',', $orderItems);
        $query = "SELECT o.uid, o.id AS orderId FROM oiplatform.order_details AS o WHERE o.id IN ({$orders})";
        return $query;
    }
    
    public function fetchDateString ($timeStamp = 0)
    {
        return "TO_DAYS('" . date('Y-m-d', $timeStamp) ."')";
    }
}