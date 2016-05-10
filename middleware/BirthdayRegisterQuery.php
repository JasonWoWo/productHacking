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

    public function __construct(Common $common)
    {
        $this->connectObj = $common;
    }

    public function getPointDayBirthdayUserCnt($table = 0)
    {
        $currentCount = 0;
        $queryResult = $this->currentBirthdayTable($table, 5, 10, 0);
        $queryClass = $this->getClassDevice($queryResult);
        foreach ($queryClass as $key => $value) {
            $currentCount += $this->getProductSk($key, $value);
        }
        $queryLunarResult = $this->currentBirthdayTable($table, 4, 4, 1);
        $queryLunarClass = $this->getClassDevice($queryLunarResult);
        foreach ($queryLunarClass as $key => $value) {
            $currentCount += $this->getProductSk($key, $value);
        }

        return $currentCount;
    }

    public function getClassDevice($queryResult = array())
    {
        $queryDevice = array();
        foreach ($queryResult as $queryItem) {
            $device = $queryItem['device'];
            if ($device == 0) {
                $queryDevice[0][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 1) {
                $queryDevice[1][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 2) {
                $queryDevice[2][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 3) {
                $queryDevice[3][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 4) {
                $queryDevice[4][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 5) {
                $queryDevice[5][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 6) {
                $queryDevice[6][] = "'" . $queryItem['udid'] . "'";
            } else {
                $queryDevice[7][] = "'" . $queryItem['udid'] . "'";
            }
        }
        return $queryDevice;

    }

    public function getProductSk($table = 0, $udids = array())
    {
        $udidList = implode(',', $udids);
        $currentTableName = 'oistatistics.st_devices_' . $table;
        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTableName ." AS s WHERE s.udid IN ( " . $udidList . " ) AND s.product_sk = 1002";
//        echo $query . " \n";
        $result = $this->connectObj->fetchCnt($query);
        return $result['cnt'];
    }

    public function currentBirthdayTable($table = 0, $birthM = 0, $birthD = 0, $isBirthLunar = 0)
    {
        echo "===== start table: " . $table . " \n";
        $currentTableName = "oibirthday.br_birthdays_" . $table;
        $sql = sprintf("
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