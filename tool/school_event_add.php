<?php
$nowDate = date('Y-m-d',strtotime('100 days ago'));
$nowDate2 = date('Y-m-d');
$nowDate3 = date('Y-m-d',strtotime('+100 days'));
$nowTime = time();
$rec_rate = 2;
require './school_event_config.php';
if(file_exists(EVENT_FILE)){
	require EVENT_FILE;
	$event_ids = array(
		$oneData_add['id'],$dayData_add['id'],$dayData2_add['id'],$dayData3_add['id'],$weekData_add['id'],$weekData2_add['id'],$weekData3_add['id'],$week2Data_add['id'],$week2Data2_add['id'],$week2Data3_add['id'],
	);
	$event_ids = load_model('event')->getColumn("school = $school and (id in (".implode(',',$event_ids).") or pid in (".implode(',',$event_ids)."))",'id');
	if($event_ids){
		load_model('event')->delete("id in (".implode(',',$event_ids).")",true);
		load_model('student_course')->delete("event in (".implode(',',$event_ids).")",true);
		load_model('teacher_course')->delete("event in (".implode(',',$event_ids).")",true);
	}
}
debug("开始生成课程");
//开单节课
doPost(POST_URL.'School/Event/add',$oneData);
//开每日循环课
doPost(POST_URL.'School/Event/add',$dayData);
doPost(POST_URL.'School/Event/add',$dayData2);
doPost(POST_URL.'School/Event/add',$dayData3);
//开每周循环课
doPost(POST_URL.'School/Event/add',$weekData);
doPost(POST_URL.'School/Event/add',$weekData2);
doPost(POST_URL.'School/Event/add',$weekData3);
//开每两周循环课
doPost(POST_URL.'School/Event/add',$week2Data);
doPost(POST_URL.'School/Event/add',$week2Data2);
doPost(POST_URL.'School/Event/add',$week2Data3);
debug("生成课程完毕");
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
checkLogs();
debug("课程通知数据检测正常");
writeEventLog();