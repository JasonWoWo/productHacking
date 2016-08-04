<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/4/29
 * Time: 下午12:49
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\CoreTaskQuery;

class RegisterCoreTask extends CoreTaskQuery
{
    /**
     * 核心任务的基础数据 - 核心任务的留存数据
     */

    const USER_PROMOTION_TABLE_NAME = 'user_promotion_statis';
    
    public function insertUserPromotionList($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify("-1 day");
        $currentDate = $dateTime->format('Y-m-d');
        $params['create_on'] = "'" . $currentDate . "'";
        // udid_uv 当日新增设备的总数
        $params['udid_uv'] = $this->getCurrentCoreAddUdid($dateTime->getTimestamp());
        // udid_activist_uv 获取当日新增的device设备中注册的用户数据
        $params['udid_activist_uv'] = $this->getCurrentCoreAddUdidActivists($dateTime->getTimestamp());
        // udid_old_user_uv 当日新增的device设备中已登陆的老用户
        $params['udid_old_user_uv'] = $this->getCurrentNewUdidOldUsers($dateTime->getTimestamp());
        // activists_uv 获取当日新增的注册用户
        $params['activists_uv'] = $this->fetchCurrentAddUsers($dateTime->getTimestamp());
        $birthRankParams = $this->fetchBirthLevelUsers($dateTime->getTimestamp());
        // 当日新增设备中完成核心任务的数量
        $params['udid_core_task_cnt'] = $this->getCurrentDevicesCoreTaskCount($dateTime->getTimestamp());;
        // 当日新增设备中新注册用户完善用户信息的数量
        $params['register_complete_info_cnt'] = $this->getRegisterCompleteCount($dateTime->getTimestamp());
        $params = $params + $birthRankParams;
        $insertSql = $this->connectObj->insertParamsQuery(self::USER_PROMOTION_TABLE_NAME, $params);
        $query = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $currentDate . " Insert " . self::USER_PROMOTION_TABLE_NAME ." Success !!! \n";
        }
    }

    // 更新7日和30日的核心用户数量
    public function updateCoreTaskPromotion()
    {
        
    }
}
$register = new RegisterCoreTask();
$register->insertUserPromotionList();