每天基础数据 核心人数, 注册用户人数, 新增设备
0 3 * * * php ~/productHacking/RegisterCoreTask.php > ~/log/log_RegisterCoreTask.txt 2>&1 2
每周周一执行上周完成核心人数数据
0 3 * * 1 php ~/productHacking/WeekCoreTask.php > ~/log/log_weekCoreTask.txt 2>&1 2
每月1日执行上月完成核心任务的人数数据
0 1 1 */1 * php ~/productHacking/MonthCoreTask.php > ~/log/log_MonthCoreTask.txt 2>&1 2
每天当日新增设备累积至今的注册用户数据
0 5 * * * php ~/productHacking/SummationActivities.php > ~/log/log_summation.txt 2>&1 2
每天执行当日完成核心任务的留存数据
0 4 * * * php ~/productHacking/RetainDailyCoreTask.php > ~/log/log_retainWeek.txt 2>&1 2
每天执行周完成核心任务的留存数据
0 4 * * * php ~/productHacking/RetainWeekCoreTask.php > ~/log/log_retainWeek.txt 2>&1 2
每天执行月完成核心任务的留存数据
0 4 * * * php ~/productHacking/RetainMonthCoreTask.php > ~/log/log_retainMonth.txt 2>&1 2
每天完成注册用户数据
0 2 * * * php ~/productHacking/UserDailyRegister.php > ~/log/log_UserDailyRegister.txt 2>&1 2
每周周1完成注册用户数据
0 3 * * 1 php ~/productHacking/UserWeeklyRegister.php > ~/log/log_UserWeeklyRegister.txt 2>&1 2
每月1日完成注册用户数据
0 1 1 * * php ~/productHacking/UserMonthlyRegister.php > ~/log/log_UserMonthlyRegister.txt 2>&1 2
每天执行注册用户的留存数据
0 6 * * * php ~/productHacking/UserDailyRegisterRetain.php > ~/log/log_UserDailyRegisterRetain.txt 2>&1 2
每天执行周注册用户的留存数据
0 6 * * * php ~/productHacking/UserWeeklyRegisterRetain.php > ~/log/log_UserWeeklyRegisterRetain.txt 2>&1 2
每天执行月注册用户的留存数据
0 6 * * * php ~/productHacking/UserMonthlyRegisterRetain.php > ~/log/log_UserMonthlyRegisterRetain.txt 2>&1 2