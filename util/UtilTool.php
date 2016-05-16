<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/13
 * Time: 下午5:10
 */
trait UtilTool
{
    public function brandItemInit()
    {
        $currentBrandListCnt = array(
            'iphone_cnt' => 0,
            'xiaomi_cnt' =>0,
            'meizu_cnt' => 0,
            'huawei_cnt' => 0,
            'vivo_cnt' => 0,
            'samsung_cnt' => 0,
            'oppo_cnt' => 0,
            'zte_cnt' => 0
        );
        return $currentBrandListCnt;
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

    public function summationDeviceCnt($currentBrands = array(), $summationBrands = array())
    {
        $currentBrands['iphone_cnt'] += $summationBrands['iphone_cnt'];
        $currentBrands['xiaomi_cnt'] += $summationBrands['xiaomi_cnt'];
        $currentBrands['meizu_cnt'] += $summationBrands['meizu_cnt'];
        $currentBrands['huawei_cnt'] += $summationBrands['huawei_cnt'];
        $currentBrands['vivo_cnt'] += $summationBrands['vivo_cnt'];
        $currentBrands['samsung_cnt'] += $summationBrands['samsung_cnt'];
        $currentBrands['oppo_cnt'] += $summationBrands['oppo_cnt'];
        $currentBrands['zte_cnt'] += $summationBrands['zte_cnt'];
        return $currentBrands;
    }

    public function getProductSkParamsInit($productSk = 1001)
    {
        if ($productSk == 1001) {
            return array(
                'iphone_bind_mp_cnt' => 0,
                'iphone_bind_focus_mp_cnt' => 0
            );
        }
        
        return array(
            'android_bind_mp_cnt' => 0,
            'android_bind_focus_mp_cnt' => 0
        );
    }
    
    public function getMappingParamsKey($productSk = 1001)
    {
        $paramsKey = array(
            1001 => array(
                0 => 'iphone_bind_mp_cnt',
                1 => 'iphone_bind_focus_mp_cnt',
            ),
            1002 => array(
                0 => 'android_bind_mp_cnt',
                1 => 'android_bind_focus_mp_cnt'
            )
        );
        return $paramsKey[$productSk];
    }

    public function getRegisterMappingBrandInit()
    {
        $paramsKeyList = array(
            'xiaomi_bind_mp_cnt' => 0,
            'xiaomi_bind_focus_mp_cnt' => 0,
            'meizu_bind_mp_cnt' => 0,
            'meizu_bind_focus_mp_cnt' => 0,
            'huawei_bind_mp_cnt' => 0,
            'huawei_bind_focus_mp_cnt' => 0,
            'vivo_bind_mp_cnt' => 0,
            'vivo_bind_focus_mp_cnt' => 0,
            'samsung_bind_mp_cnt' => 0,
            'samsung_bind_focus_mp_cnt' => 0,
            'oppo_bind_mp_cnt' => 0,
            'oppo_bind_focus_mp_cnt' => 0,
            'zte_bind_mp_cnt' => 0,
            'zte_bind_focus_mp_cnt' => 0
        );
        return $paramsKeyList;
    }
}