<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/5
 * Time: 下午5:44
 */
include __DIR__ ."/middleware/RetainForMonth.php";
class RetainMonthCoreTask extends RetainForMonth
{
    const USER_MONTH_BASE_TASK_TABLE = 'user_core_task_retain_monthly_statis';
    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }

    public function updateRetainMonthForMonth($isRetainMonth = 0)
    {
        $timestamp = time();
        $date = new \DateTime(date('Y-m-01'));
        $pointDate = clone $date;
        $visitDate = clone $date;
        $pointDate->modify("-$isRetainMonth months");
        $visitDate->modify("-1 month"); 
        $retainMonthUnCT = $this->baseRetainMonthly($pointDate, $visitDate, -1, 5);
        $retainMonthCT = $this->baseRetainMonthly($pointDate, $visitDate, 6, 10);
        $paramsKey = $this->getIsRetainMonthParamsKey($isRetainMonth);
        $paramsList = array(
            $paramsKey[1] => $retainMonthCT,
            $paramsKey[2] => $retainMonthUnCT
        );
        $monthLabelString = $pointDate->format('Y-m-d');
        if ($this->checkCurrentDateData(self::USER_MONTH_BASE_TASK_TABLE, $monthLabelString)) {
            $where = array('create_on' => $monthLabelString);
            $updateQuery = $this->common->updateParamsQuery(self::USER_MONTH_BASE_TASK_TABLE, $paramsList, $where);
            $query = $this->common->fetchCakeStatQuery($updateQuery);
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