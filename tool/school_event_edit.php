<?php
$nowDate = date('Y-m-d',strtotime('50 days ago'));
$nowDate2 = date('Y-m-d');
$nowDate3 = date('Y-m-d',strtotime('+50 days'));
$nowTime = time();
$rec_rate = 5;
require './school_event_config.php';
if(!file_exists(EVENT_FILE))  exit('请先生成课程');
require EVENT_FILE;
debug("开始修改课程");
//修改单节课
doPost(POST_URL.'School/Event/edit',array_merge($oneData,array('id'=>$oneData_add['id'],'whole'=>1)));
//修改每日循环课
doPost(POST_URL.'School/Event/edit',array_merge($dayData,array('id'=>$dayData_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/edit',array_merge($dayData2,array('id'=>$dayData2_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/edit',array_merge($dayData3,array('id'=>$dayData3_add['id'],'whole'=>1)));
//修改每周循环课
doPost(POST_URL.'School/Event/edit',array_merge($weekData,array('id'=>$weekData_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/edit',array_merge($weekData2,array('id'=>$weekData2_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/edit',array_merge($weekData3,array('id'=>$weekData3_add['id'],'whole'=>1)));
//修改每两周循环课
doPost(POST_URL.'School/Event/edit',array_merge($week2Data,array('id'=>$week2Data_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/edit',array_merge($week2Data2,array('id'=>$week2Data2_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/edit',array_merge($week2Data3,array('id'=>$week2Data3_add['id'],'whole'=>1)));
debug("修改课程完毕");
debug("检测课程数据是否异常");
sleep(10);
checkEvent();
debug("课程数据检测正常");
debug("检测课程学生数据是否异常");
checkStudentCourse();
debug("课程学生数据检测正常");
debug("检测课程老师数据是否异常");
checkTeacherCourse();
debug("课程老师数据检测正常");
debug("检测课程通知数据是否异常");
checkLogs('update');
debug("课程通知数据检测正常");
writeEventLog('update');