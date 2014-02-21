<?php
$nowDate = date('Y-m-d',strtotime('50 days ago'));
$nowDate2 = date('Y-m-d');
$nowDate3 = date('Y-m-d',strtotime('+50 days'));
$nowTime = time();
$rec_rate = 5;
require './school_event_config.php';
if(!file_exists(EVENT_FILE))  exit('请先生成课程');
require EVENT_FILE;
debug("开始删除课程");
//删除单节课
doPost(POST_URL.'School/Event/delete?'.http_build_query(array('id'=>$oneData_add['id'],'whole'=>1)));
//删除每日循环课
doPost(POST_URL.'School/Event/delete?'.http_build_query(array('id'=>$dayData_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/delete?'.http_build_query(array('id'=>$dayData2_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/delete?'.http_build_query(array('id'=>$dayData3_add['id'],'whole'=>1)));
//删除每周循环课
doPost(POST_URL.'School/Event/delete?'.http_build_query(array('id'=>$weekData_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/delete?'.http_build_query(array('id'=>$weekData2_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/delete?'.http_build_query(array('id'=>$weekData3_add['id'],'whole'=>1)));
//删除每两周循环课
doPost(POST_URL.'School/Event/delete?'.http_build_query(array('id'=>$week2Data_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/delete?'.http_build_query(array('id'=>$week2Data2_add['id'],'whole'=>1)));
doPost(POST_URL.'School/Event/delete?'.http_build_query(array('id'=>$week2Data3_add['id'],'whole'=>1)));
sleep(10);
debug("删除课程完毕");