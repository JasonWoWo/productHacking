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
}