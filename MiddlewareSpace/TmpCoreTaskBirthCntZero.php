<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/24
 * Time: 上午10:17
 */

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;

class TmpCoreTaskBirthCntZero
{
    use UtilSqlTool;
    
    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function getCurrentDayBirthCntZeroDetail($startStamp = 0, $endStamp = 0)
    {
        $retainCollection = $this->connectObj->fetchRetainCollection();

        $query = array(
            'dct_lt' => array('$gte' => $startStamp, '$lte' => $endStamp),
            'max_bct' => array('$gte' => 6, '$lte' => 2000)
        );
        $resultItems = $retainCollection->find($query);
        $userItemsDetail = array();
        foreach ($resultItems as $item) {
            $userItemsDetail[] = $item['uid'];
        }
        return $this->getBirthCntInProduct($userItemsDetail);
    }
    
    public function getBirthCntInProduct($userItems = array())
    {
        $userIdList = implode(',', $userItems);
        $resultItems = $this->connectObj->fetchAssoc($this->getQueryBirthZeroInProduct($userIdList));
        return $resultItems;
    }
}