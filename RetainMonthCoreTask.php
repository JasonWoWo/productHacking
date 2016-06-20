<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/5
 * Time: 下午5:44
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\RetainForMonth;

class RetainMonthCoreTask extends RetainForMonth
{
    const USER_MONTH_BASE_TASK_TABLE = 'user_core_task_retain_monthly_statis';

    //每天执行月完成核心任务的留存数据
    public function updateRetainMonthForMonth($isRetainMonth = 0)
    {
        $current = new \DateTime();
        $nextMonth = $current->modify("+1 month");
        $date = new \DateTime(date('Y-m-01', $nextMonth->getTimestamp()));
        $pointDate = clone $date;
        $visitDate = clone $date;
        $pointDate->modify("-$isRetainMonth months");
        $visitDate->modify("-1 month"); 
        $retainMonthUnCT = $this->baseRetainMonthly($pointDate, $visitDate, -1, 5);
        if (empty($retainMonthUnCT)) {
            echo "UnCatch isRetainMonthUnCT value, Please Check! \n";
            return false;
        }
        $retainMonthCT = $this->baseRetainMonthly($pointDate, $visitDate, 6, 1000);
        if (empty($retainMonthCT)) {
            echo "UnCatch isRetainMonthCT value, Please Check! \n";
            return false;
        }
        $paramsKey = $this->getIsRetainMonthParamsKey($isRetainMonth);
        $paramsList = array(
            $paramsKey[1] => $retainMonthCT,
            $paramsKey[2] => $retainMonthUnCT
        );
        $monthLabelString ="'" . $pointDate->format('Y-m-d') . "'";
        echo $monthLabelString . "\n";
        if ($this->checkCurrentDateData(self::USER_MONTH_BASE_TASK_TABLE, $monthLabelString)) {
            $where = array('create_on' => $monthLabelString);
            $updateQuery = $this->connectObj->updateParamsQuery(self::USER_MONTH_BASE_TASK_TABLE, $paramsList, $where);
            $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
            if ($query) {
                echo " === UPDATE " . $monthLabelString . " month isRetain : " . $isRetainMonth . " success !!! \n";
            }
        }
    }

    public function updateBeforeMonth()
    {
        // 栗子: 2016.5.1 , 次月留存 - 2016.3.1 , 2016.2.1 , 2016.1.1 , 2015.12.1 , 2015.11.1 依次类推
        $this->updateRetainMonthForMonth(2);
        $this->updateRetainMonthForMonth(3);
        $this->updateRetainMonthForMonth(4);
        $this->updateRetainMonthForMonth(5);
        $this->updateRetainMonthForMonth(6);
        $this->updateRetainMonthForMonth(7);
        $this->updateRetainMonthForMonth(8);
        $this->updateRetainMonthForMonth(9);
        $this->updateRetainMonthForMonth(10);
    }
}
$retainMonth = new RetainMonthCoreTask();
$retainMonth->updateBeforeMonth();