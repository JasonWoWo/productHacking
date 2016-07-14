<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/13
 * Time: 下午5:10
 */
namespace UtilSpace;

trait UtilTool
{
    public $default_daily_timestamp = 86400;
    
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

    public function getAppPackage()
    {
        $appPackage = array(
            'com.cleanmaster.security_cn' =>  '猎豹安全卫士',
            'com.cleanmaster.mguard_cn' => '猎豹清理大师',
            'com.qihoo360.mobilesafe' => '360卫士',
            'com.qihoo.cleandroid_cn' => '360清理大师',
            'cn.opda.a.phonoalbumshoushou' =>'百度卫士',
        );
        return $appPackage;
    }
    
    public function getSrcParamsKeyInit()
    {
        $srcParamsKey = array(
            'ab' => 0,
            'add' => 0,
            'yab' => 0,
            'of' => 0,
            'oi' => 0,
            'qq' => 0,
            'rr' => 0,
            'wx' => 0,
            'pyq' => 0,
        );
        return $srcParamsKey;
    }

    public function getSrcParamInsertKey()
    {
        $paramItems = array(
            'srcItem' => array(
                'ab' => 'local_import_cnt',
                'add' => 'add_import_cnt',
                'yab' => 'yab_import_cnt',
                'of' => 'of_import_cnt',
                'oi' => 'oi_import_cnt',
                'qq' => 'qq_import_cnt',
                'rr' => 'rr_import_cnt',
                'wx' => 'wx_import_cnt',
                'pyq' => 'pyq_import_cnt',
            ),
            'srcWithPhoneItem' => array(
                'ab' => 'local_import_phone_cnt',
                'add' => 'add_import_phone_cnt',
                'yab' => 'yab_import_phone_cnt',
                'of' => 'of_import_phone_cnt',
                'oi' => 'oi_import_phone_cnt',
                'qq' => 'qq_import_phone_cnt',
                'rr' => 'rr_import_phone_cnt',
                'wx' => 'wx_import_phone_cnt',
                'pyq' => 'pyq_import_phone_cnt',
            ),
        );
        return $paramItems;
    }

    public function summationDeviceSrcItemsCnt($currentSrc = array(), $summationSrc = array())
    {
        $currentSrc['ab'] += $summationSrc['ab'];
        $currentSrc['add'] += $summationSrc['add'];
        $currentSrc['yab'] += $summationSrc['yab'];
        $currentSrc['of'] += $summationSrc['of'];
        $currentSrc['oi'] += $summationSrc['oi'];
        $currentSrc['qq'] += $summationSrc['qq'];
        $currentSrc['rr'] += $summationSrc['rr'];
        $currentSrc['wx'] += $summationSrc['wx'];
        $currentSrc['pyq'] += $summationSrc['pyq'];
        return $currentSrc;
    }

    public function getIsRetainParamsDailyKey($isRetain)
    {
        $paramKey = array(
            2 => array(
                1 => 'second_core_task_cnt',
                2 => 'second_uncore_task_cnt',
            ),
            3 => array(
                1 => 'third_core_task_cnt',
                2 => 'third_uncore_task_cnt',
            ),
            7 => array(
                1 => 'week_core_task_cnt',
                2 => 'week_uncore_task_cnt',
            ),
            15 => array(
                1 => 'half_month_core_task_cnt',
                2 => 'half_month_uncore_task_cnt',
            ),
            30 => array(
                1 => 'month_core_task_cnt',
                2 => 'month_uncore_task_cnt',
            ),
            60 => array(
                1 => 'second_month_core_task_cnt',
                2 => 'second_month_uncore_task_cnt',
            ),
            90 => array(
                1 => 'quarter_month_core_task_cnt',
                2 => 'quarter_month_uncore_task_cnt',
            ),
        );
        return $paramKey[$isRetain];
    }
    
    public function getSmsRetainParamKey($isRetain = 3)
    {
        $paramKey = array(
            2 => 'second_retain_cnt',
            3 => 'third_retain_cnt',
            7 => 'week_retain_cnt',
            15 => 'half_month_retain_cnt',
            30 => 'month_retain_cnt',
        );
        return $paramKey[$isRetain];
    }

    public function getProductPlatform()
    {
        return $platformDetailCnt = array(
            1002 => 0,
            1001 => 0,
            1003 => 0,
        );
    }

    public function getProductChannel()
    {
        return $channelDetailCnt = array(
            0 => 0,
            2029 => 0,
            2025 => 0,
            2038 => 0,
            2032 => 0,
            2031 => 0,
            2460 => 0,
            2461 => 0,
            2013 => 0,
            2071 => 0,
            2043 => 0,
            2108 => 0,
            2039 => 0,
        );
    }

    public function getIsRetainDailyParamsKey($isRetain = 0)
    {
        $paramsKey = array(
            2 => 'second_day_cnt',
            3 => 'third_day_cnt',
            4 => 'fourth_day_cnt',
            5 => 'fifth_day_cnt',
            6 => 'sixth_day_cnt',
            7 => 'seventh_day_cnt',
            15 => 'fifteen_day_cnt',
            30 => 'thirty_day_cnt',
            60 => 'sixty_day_cnt',
            90 => 'ninety_day_cnt',
        );
        return $paramsKey[$isRetain];
    }

    public function getIsRetainWeekParamsKey($isRetain)
    {
        $paramsKeys = array(
            7 => 'first_week_user_cnt',
            14 => 'second_week_user_cnt',
            21 => 'third_week_user_cnt',
            28 => 'fourth_week_user_cnt',
            35 => 'fifth_week_user_cnt',
            42 => 'sixth_week_user_cnt',
            49 => 'seventh_week_user_cnt',
            56 => 'eighth_week_user_cnt',
            63 => 'ninth_week_user_cnt',
        );
        return $paramsKeys[$isRetain];
    }

    public function buildDeviceTableName($udid)
    {
        // $udid is lower case.
        $ascii = ord($udid);
        if ($ascii >= 48 && $ascii <= 57) {
            $idx = floor(($ascii -48)/2);
        } elseif ($ascii >= 97 && $ascii <= 102) {
            $idx = floor(($ascii - 97)/2) + 5;
        } elseif ($ascii >= 65 && $ascii <= 70) {
            $idx = floor(($ascii - 65)/2) + 5;
        } else {
            return '';
        }

        return 'oistatistics.st_devices_'.$idx;
    }

    public function get_number_birthday_number($userId)
    {
        $table_num = floor( intval( $userId ) / 50000 );
        return $table_num;
    }

    public function extract_phone_number( $number ) {
        if ( !is_string( $number ) ) {
            return FALSE;
        }
        $number = strtr( $number, array( '-' => '', ' '=> '', '(' => '', ')' => '' ) );
        $pattern = '/^(?:\+?0?86)?(?:17951)?(1\d{10})$/';
        if ( preg_match( $pattern, $number, $matches ) ) {
            return $matches[1];
        }
        return FALSE;
    }

    public function get_hashed_number( $number ) {
        $number = $this->extract_phone_number( $number );
        if ( $number === FALSE ) {
            return FALSE;
        }
        return md5( 'BR$$@'.$number.'NGFjMDA4ZWQyOTUzMzJmZmM0NTJjMjQ2' );
    }
    
    public function array_change_key_name( $array, $namepairs ) {
        foreach ( $namepairs as $oldkey => $newkey ) {
            if ( isset( $array[$oldkey] ) ) {
                $array[$newkey] = $array[$oldkey];
                unset( $array[$oldkey] );
            }
        }
        return $array;
    }

    public function _follower_key( $number )
    {
        return "F:{$number}:follower";
    }
}