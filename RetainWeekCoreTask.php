<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/4
 * Time: 下午5:58
 */
include __DIR__ ."/middleware/RetainForWeek.php";
class RetainWeekCoreTask extends RetainForWeek
{
    const USER_WEEK_BASE_TASK_TABLE = 'user_core_task_retain_weekly_statis';

    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }

    public function updateBeforeWeek($extendStamp = 0) 
    {
        $this->updateRetainForWeek($extendStamp, 7);
        $this->updateRetainForWeek($extendStamp, 14);
        $this->updateRetainForWeek($extendStamp, 21);
        $this->updateRetainForWeek($extendStamp, 28);
        $this->updateRetainForWeek($extendStamp, 35);
        $this->updateRetainForWeek($extendStamp, 42);
        $this->updateRetainForWeek($extendStamp, 49);
        $this->updateRetainForWeek($extendStamp, 56);
        $this->updateRetainForWeek($extendStamp, 63);
    }
    
    public function updateRetainForWeek($extendStamp = 0, $isRetain = 0)
    {
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $currentDate = strtotime(date('Y-m-d', $timestamp));

        $retainWeekCT = $this->baseRetainWeekly($currentDate, 7, 7, 6, 1000);
        if (empty($retainWeekCT)) {
            echo "UnCatch isRetainWeekCT value, Please Check! \n";
            return false;
        }
        $retainWeekUnCT = $this->baseRetainWeekly($currentDate, 7, 7, -1, 5);
        if (empty($retainWeekUnCT)) {
            echo "UnCatch isRetainWeekUnCT value, Please Check! \n";
            return false;
        }
        $paramsKey = $this->getIsRetainWeekParamsKey($isRetain);
        if (empty($paramsKey)) {
            echo "UnCatch the paramsKey from isRetain, Please check ! \n";
            return;
        }
        $items = array(
            $paramsKey[1] => $retainWeekCT,
            $paramsKey[2] => $retainWeekUnCT
        );
        $loginIn = $currentDate - $isRetain * 86400;
        $loginInString = "'" . date('Y-m-d', $loginIn) . "'";
        if ($this->checkCurrentDateData(self::USER_WEEK_BASE_TASK_TABLE, $loginInString)) {
            $where = array('create_on' => $loginInString);
            $updateQuery = $this->common->updateParamsQuery(self::USER_WEEK_BASE_TASK_TABLE, $items, $where);
            $query = $this->common->fetchCakeStatQuery($updateQuery);
            if ($query) {
                echo " === UPDATE " . $loginInString . " week isRetain : " . $isRetain . " success !!! \n";
            }
        }
    }
}
$retainWeek = new RetainWeekCoreTask();
$retainWeek->updateBeforeWeek();