<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/10
 * Time: 下午5:51
 */
include __DIR__.'/../common/Common.php';
class BirthdayRegisterQuery extends Common
{
    public $connectObj;
    
    public $birthCnt = 0;

    public function __construct(Common $common)
    {
        $this->connectObj = $common;
    }

    public function getPointDayBirthdayUserCnt($table = 0)
    {
        $queryResult = $this->currentBirthdayTable($table, 5, 10, 0);
        $queryLunarResult = $this->currentBirthdayTable($table, 4, 4, 1);
        $tableCnt = $queryResult['cnt'] + $queryLunarResult['cnt'];
        $this->birthCnt += $tableCnt;
    }

    public function getSummation()
    {
        return $this->birthCnt;
    }

    public function currentBirthdayTable($table = 0, $birthM = 0, $birthD = 0, $isBirthLunar = 0)
    {
        echo "===== start table: " . $table . " \n";
        $currentTableName = "oibirthday.br_birthdays_" . $table;
        $sql = sprintf("
        SELECT COUNT(*) AS cnt FROM (
		 SELECT userid, birth_m, birth_d FROM %s 
		 WHERE 
		 	`birth_is_lunar` = %s AND birth_m = %d AND birth_d = %d
		 GROUP BY 
		 	birth_m, birth_d, userid
	     ) AS bcn LEFT JOIN oibirthday.users AS u ON bcn.userid = u.id 
        WHERE u.appid = 1002
	",
            $currentTableName,
            $isBirthLunar,
            $birthM, $birthD
        );
        $query = $this->connectObj->fetchAssoc($sql);
        return $query;
    }

    public function getMaxUserId()
    {
        $sql = sprintf("SELECT id FROM oibirthday.users ORDER BY id DESC LIMIT 1");
        $query = $this->connectObj->fetchCnt($sql);
        echo " MAXUsersId: " . $query['id'] . " \n";
        return $query['id'];
    }
}