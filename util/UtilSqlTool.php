<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/13
 * Time: 下午5:31
 */
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
    
    public function getQueryMaxUserId()
    {
        $query = "SELECT id FROM oibirthday.users ORDER BY id DESC LIMIT 1";
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
}