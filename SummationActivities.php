<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/5
 * Time: 上午10:58
 */
include __DIR__ ."/middleware/CoreTaskQuery.php";
class SummationActivities extends CoreTaskQuery
{
    const USER_PROMOTION_TABLE_NAME = 'user_promotion_statis';
    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }
    
    public function getCronDailySummationActivities()
    {
        $terminationTimestamp = strtotime('2015-01-01');
        $dateTime = new \DateTime(date('Y-m-d'));
        while ($dateTime->getTimestamp() > $terminationTimestamp) {
            $dateTime->modify('-1 day');
            $loginIn = "'" . $dateTime->format('Y-m-d') . "'";
            $param['activity_summation'] = $this->getFullDeviceSummation($dateTime->getTimestamp());
            if ($this->checkCurrentDateData(self::USER_PROMOTION_TABLE_NAME, $loginIn)) {
                $where = array('create_on' => $loginIn);
                $updateQuery = $this->common->updateParamsQuery(self::USER_PROMOTION_TABLE_NAME, $param, $where);
                $query = $this->common->fetchCakeStatQuery($updateQuery);
                if ($query) {
                    echo " === Update " . $loginIn . " activity_summation " . $loginIn . "  update success !!! \n";
                }
            }
        }

    }
}
$summation = new SummationActivities();
$summation->getCronDailySummationActivities();