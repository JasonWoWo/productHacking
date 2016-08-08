<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/8/8
 * Time: 上午11:01
 */
require __DIR__. '/Bootstrap.php';

$home = '/home/oipublish';
$basePath = '/opt/octinn/artifacts/ugrowth-stat';
$mobileLogPath = '/var/log/octinn/statlogs';
$outLog = $home . '/Log';

$jobby = new Jobby\Jobby();

$jobs = [
    // 核心任务基础数据 展示路径: /hacking/baseUserPromotionDetail
    'registerCoreTask' => [
        'command' => "php {$basePath}/RegisterCoreTask.php",
        'schedule' => '05 0 * * *',
        'output' => '',
    ],
    // 每日核心用户的留存情况 展示路径: /hacking/userCoreTaskRetain
    'retainDailyCoreTask' => [
        'command' => "php {$basePath}/RetainDailyCoreTask.php",
        'schedule' => '12 0 * * *',
        'output' => '',
    ],
    // 发短信引入的注册用户数 展示路径: /hacking/smsRegisterRetain
    'smsRegisterStat' => [
        'command' => "php {$basePath}/SmsRegisterStat.php",
        'schedule' => '30 0 * * *',
        'output' => '',
    ],
    // 注册用户分渠道和平台(新插入) 展示路径: /hacking/registerPlatform
    'RegisterRetainPCStat' => [
        'command' => "php {$basePath}/RegisterRetainPCStat.php ",
        'schedule' => '18 0 * * *',
        'output' => '',
    ],
    // 注册用户分渠道和平台留存(更新留存数据) 展示路径: /hacking/registerPlatform
    'RegisterOnRetainPCStat' => [
        'command' => "php {$basePath}/RegisterOnRetainPCStat.php",
        'schedule' => '26 0 * * *',
        'output' => '',
    ],
    // 每天新增的注册用户数(新插入) 展示路径: /hacking/userRegisterDaily
    'UserDailyRegister' => [
        'command' => "php {$basePath}/UserDailyRegister.php",
        'schedule' => '27 0 * * *',
        'output' => '',
    ],
    // 每天新增的注册用户留存(更新留存数据) 展示路径: /hacking/userRegisterDaily
    'UserDailyRegisterRetain' => [
        'command' => "php {$basePath}/UserDailyRegisterRetain.php",
        'schedule' => '38 0 * * *',
        'output' => '',
    ],
    // 每月1日定时新增的注册用户数(新插入) 展示路径: /hacking/userRegisterMonthly
    'UserMonthlyRegister' => [
        'command' => "php {$basePath}/UserMonthlyRegister.php",
        'schedule' => '50 0 1 * *',
        'output' => '',
    ],
    // 每月核心任务数, 展示路径: /hacking/userMonthRetain
    'MonthCoreTask' => [
        'command' => "php {$basePath}/MonthCoreTask.php",
        'schedule' => '00 1 1 * *',
        'output' => '',
    ],
    // 新增设备新用户分SRC备份生日数据, 展示路径: /hacking/userBackUpBirthStat
    'UserBackUpBirthStat' => [
        'command' => "php {$basePath}/UserBackUpBirthStat.php",
        'schedule' => '05 1 * * *',
        'output' => '',
    ],
    // 短信唤醒注册用户的留存数据 展示路径: /hacking/smsAwakenUserRetain
    'Activation' => [
        'command' => "php {$basePath}/Activation.php",
        'schedule' => '10 1 * * *',
        'output' => '',
    ],
    // 每周用户的留存数据(更新) 展示路径: /hacking/userRegisterWeekly
    'UserWeeklyRegisterRetain' => [
        'command' => "php {$basePath}/UserWeeklyRegisterRetain.php",
        'schedule' => '20 1 * * *',
        'output' => '',
    ],
    // 注册用户的通讯录授权(insert) 展示路径: /hacking/userAuthorize
    'RegisterAuthorize' => [
        'command' => "php {$basePath}/RegisterAuthorize.php",
        'schedule' => '30 1 * * *',
        'output' => '',
    ],
    // 注册用户的通讯录授权留存数据(update) 展示路径: /hacking/userAuthorize
    'RetainAuthorizeRegister' => [
        'command' => "php {$basePath}/RetainAuthorizeRegister.php",
        'schedule' => '40 1 * * *',
        'output' => '',
    ],
    // 生日群数据 展示路径: /hacking/fetchBirthGroup  or  /hacking/userBirthGroupSummation
    'UserBirthGroup' => [
        'command' => "php {$basePath}/UserBirthGroup.php",
        'schedule' => '50 1 * * *',
        'output' => '',
    ],
    // 每天执行月注册用户的留存(update) 展示路径: /hacking/userRegisterMonthly
    'UserMonthlyRegisterRetain' => [
        'command' => "php {$basePath}/UserMonthlyRegisterRetain.php",
        'schedule' => '00 2 * * *',
        'output' => '',
    ],
    // 全部设备的通讯录授权 展示:
    'EntireDeviceAuthorize' => [
        'command' => "php {$basePath}/EntireDeviceAuthorize.php",
        'schedule' => '10 2 * * *',
        'output' => '',
    ],
    // 注册用户微信按平台和品牌分类 展示路径:  /hacking/weChatBrandRatio  or /hacking/weChatPlatformRatio
    'UserDailyBrandStat' => [
        'command' => "php {$basePath}/UserDailyBrandStat.php",
        'schedule' => '20 2 * * *',
        'output' => '',
    ],
    // 注册用户微信按平台和品牌分类(update)  展示路径:  /hacking/weChatBrandRatio  or /hacking/weChatPlatformRatio
    'WeChatAuthorize' => [
        'command' => "php {$basePath}/WeChatAuthorize.php",
        'schedule' => '27 2 * * *',
        'output' => '',
    ],
    //  每周注册用户分渠道和平台留存(更新留存数据)
    'UserWeeklyRegisterRetainOnPC' => [
        'command' => "php {$basePath}/UserWeeklyRegisterRetainOnPC.php",
        'schedule' => '35 2 * * *',
        'output' => '',
    ],
    // 周核心任务留存
    'RetainWeekCoreTask' => [
        'command' => "php {$basePath}/RetainWeekCoreTask.php",
        'schedule' => '40 2 * * *',
        'output' => '',
    ],
    // 月核心任务用户留存
    'RetainMonthCoreTask' => [
        'command' => "php {$basePath}/RetainMonthCoreTask.php",
        'schedule' => '00 3 * * *',
        'output' => '',
    ],
    // 7天和30天累计核心用户数
    'cronUpdateTask' => [
        'command' => "php {$basePath}/CronRetainCoreTask.php",
        'schedule' => '10 3 * * *',
        'output' => '',
    ],
    // 累计注册用户数
    'SummationActivities' => [
        'command' => "php {$basePath}/SummationActivities.php/",
        'schedule' => '20 3 * * *',
        'output' => '',
    ],
    // 用户添加生日总数来源分布 /hacking/userBackUpBirthStat
    'UserBirthSummation' => [
        'command' => "php {$basePath}/UserBirthSummation.php",
        'schedule' => '15 3 * * *',
        'output' => '',
    ],
    // 品牌提醒率分类统计 /hacking/platformReminderRatio or /hacking/brandsReminderRatio
    'UserBrandBirthdayStat' => [
        'command' => "php {$basePath}/UserBrandBirthdayStat.php",
        'schedule' => '30 4 * * *',
        'output' => '',
    ],
    // 平台提醒率分类统计 /hacking/platformReminderRatio
    'UserBirthdayStat' => [
        'command' => "php {$basePath}/UserBirthdayStat.php",
        'schedule' => '25 4 * * *',
        'output' => '',
    ],
    // 新用户完成个人信息数据 /hacking/registerCompleteInfoRatio
    'RegisterInformation' => [
        'command' => "php {$basePath}/RegisterInformation.php",
        'schedule' => '10 4 * * *',
        'output' => '',
    ],
    //
//    'stat_reminder_statis' => [
//        'command' => '',
//        'schedule' => '',
//        'output' => '',
//    ],
    //
//    'PandoraSource' => [
//        'command' => '',
//        'schedule' => '',
//        'output' => '',
//    ],
    //
//    'daily_forecast_stat' => [
//        'command' => '',
//        'schedule' => '',
//        'output' => '',
//    ],
    //
//    'pushClientDataStat' => [
//        'command' => '',
//        'schedule' => '',
//        'output' => '',
//    ],
    // 周核心任务
    'WeekCoreTask' => [
        'command' => "php {$basePath}/WeekCoreTask.php",
        'schedule' => '10 01 * * 01',
        'output' => '',
    ],
    // 每周用户的留存数据(insert) 展示路径: /hacking/userRegisterWeekly
    'UserWeeklyRegister' => [
        'command' => "php {$basePath}/UserWeeklyRegister.php",
        'schedule' => '00 1 * * 1',
        'output' => '',
    ],
    // 每周注册用户分渠道和平台留存(insert)
    'UserWeeklyRegisterOnPC' => [
        'command' => "php {$basePath}/UserWeeklyRegisterOnPC.php",
        'schedule' => '00 2 * * 1',
        'output' => '',
    ],
];

foreach ($jobs as $job => $config) {
    $jobby->add($job, $config);
}

$jobby->run();