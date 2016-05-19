<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/18
 * Time: 下午3:51
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\UserRegisterBackUpBirthQuery;

class UserBackUpBirthStat extends UserRegisterBackUpBirthQuery
{
    const USER_BACKUP_BIRTH_STAT = 'user_backup_birth_statis';

    public function showUserBackUpSrcItems()
    {
        $srcSummationItem = $this->getSummationSrcItems();
        $insertParamKey = $this->getSrcParamInsertKey();
        $currentDate = new \DateTime();
        $currentDate->modify('-1 day');
        $params['create_on'] = "'" . $currentDate->format('Y-m-d') . "'";
        foreach ($srcSummationItem['srcSummation'] as $key => $value) {
            $srcInsertParam = $insertParamKey['srcItem'];
            $params[$srcInsertParam[$key]] = $value;
            echo "==== src: " . $key . " ==== value: " . $value . " ==== \n";
        }
        
        foreach ($srcSummationItem['phoneSummation'] as $key => $value) {
            $srcPhoneInsertParam = $insertParamKey['srcWithPhoneItem'];
            $params[$srcPhoneInsertParam[$key]] = $value;
            echo " phone ==== src: " . $key . " ==== value: " . $value . " ==== \n";
        }
        $summation = $this->getBirthSummation();
        $params['daily_backup_birth_cnt'] = $summation;
        echo " ==== summation: " . $summation . " ==== \n";
        $insertSql = $this->connectObj->insertParamsQuery(self::USER_BACKUP_BIRTH_STAT, $params);
        $query = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $params['create_on'] . " Insert " . self::USER_BACKUP_BIRTH_STAT ." Success !!! \n";
        }
    }
}
$backUp = new UserBackUpBirthStat();
$backUp->showUserBackUpSrcItems();