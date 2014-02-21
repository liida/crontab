<?php
require './school_common.php';
$pushTo = array();
$data = array(
	'course'=>$course,
	'start_hour'=>'07',
	'start_minute'=>'00',
	'end_hour'=>'08',
	'end_minute'=>'00',
	'class_time'=>'1.0',
	'teacher_op'=>$teacher_op,
	'student_op'=>$student_op,
	'color'=>1,
);
//开单节课
$oneData = array_merge(array(
	'text'=>'测试课程单节-'.$nowTime,
	'rec_type'=>'',
	'num'=>1,
	'start_date'=>$nowDate2,
),$data);

//开每日循环课
$dayData = array_merge(array(
	'text'=>'测试课程每日-'.$nowTime,
	'rec_type'=>'day_1',
	'num'=>$rec_rate,
	'start_date'=>$nowDate,
),$data);

$dayData2 = array_merge(array(
	'text'=>'测试课程每日2-'.$nowTime,
	'rec_type'=>'day_1',
	'num'=>$rec_rate,
	'start_date'=>$nowDate2,
),$data);

$dayData3 = array_merge(array(
	'text'=>'测试课程每日3-'.$nowTime,
	'rec_type'=>'day_1',
	'num'=>$rec_rate,
	'start_date'=>$nowDate3,
),$data);
//开每周循环课
$weekData = array_merge(array(
	'text'=>'测试课程每周-'.$nowTime,
	'rec_type'=>'week_1',
	'week'=>array(1,3,5,0),
	'num'=>$rec_rate,
	'start_date'=>$nowDate,
),$data);

$weekData2 = array_merge(array(
	'text'=>'测试课程每周2-'.$nowTime,
	'rec_type'=>'week_1',
	'week'=>array(1,3,5,0),
	'num'=>$rec_rate,
	'start_date'=>$nowDate2,
),$data);

$weekData3 = array_merge(array(
	'text'=>'测试课程每周3-'.$nowTime,
	'rec_type'=>'week_1',
	'week'=>array(1,3,5,0),
	'num'=>$rec_rate,
	'start_date'=>$nowDate3,
),$data);



//开每两周循环课
$week2Data = array_merge(array(
	'text'=>'测试课程每两周-'.$nowTime,
	'rec_type'=>'week_2',
	'week'=>array(1,3,5,0),
	'num'=>$rec_rate,
	'start_date'=>$nowDate,
),$data);


$week2Data2 = array_merge(array(
	'text'=>'测试课程每两周2-'.$nowTime,
	'rec_type'=>'week_2',
	'week'=>array(1,3,5,0),
	'num'=>$rec_rate,
	'start_date'=>$nowDate2,
),$data);

$week2Data3 = array_merge(array(
	'text'=>'测试课程每两周3-'.$nowTime,
	'rec_type'=>'week_2',
	'week'=>array(1,3,5,0),
	'num'=>$rec_rate,
	'start_date'=>$nowDate3,
),$data);

function checkEvent($i=1){
	global $oneData;
	global $dayData;
	global $dayData2;
	global $dayData3;
	global $weekData;
	global $weekData2;
	global $weekData3;
	global $week2Data;
	global $week2Data2;
	global $week2Data3;
	
	$_Event = load_model('event');
	$lock = 0;
	$lock += ($oneDataArr = $_Event->getRow(array('text'=>$oneData['text']))) ? $oneDataArr['lock']:exit('单节课程未生成');
	$lock += ($dayDataArr = $_Event->getRow(array('text'=>$dayData['text']))) ? $dayDataArr['lock']:exit('每日课程未生成');
	$lock += ($dayDataArr2 = $_Event->getRow(array('text'=>$dayData2['text']))) ? $dayDataArr2['lock']:exit('每日课程2未生成');
	$lock += ($dayDataArr3 = $_Event->getRow(array('text'=>$dayData3['text']))) ? $dayDataArr3['lock']:exit('每日课程3未生成');
	$lock += ($weekDataArr = $_Event->getRow(array('text'=>$weekData['text']))) ? $weekDataArr['lock']:exit('每周课程未生成');
	$lock += ($weekDataArr2 = $_Event->getRow(array('text'=>$weekData2['text']))) ? $weekDataArr2['lock']:exit('每周课程2未生成');
	$lock += ($weekDataArr3 = $_Event->getRow(array('text'=>$weekData3['text']))) ? $weekDataArr3['lock']:exit('每周课程3未生成');
	$lock += ($week2DataArr = $_Event->getRow(array('text'=>$week2Data['text']))) ? $week2DataArr['lock']:exit('每两周课程未生成');
	$lock += ($week2DataArr2 = $_Event->getRow(array('text'=>$week2Data2['text']))) ? $week2DataArr2['lock']:exit('每两周课程2未生成');
	$lock += ($week2DataArr3 = $_Event->getRow(array('text'=>$week2Data3['text']))) ? $week2DataArr3['lock']:exit('每两周课程3未生成');
	if($lock){
		if($i > 3) exit('课程异常');
		sleep(10);
		$i++;
		checkEvent($i);
	}else{
		checkEventData($oneData,$oneDataArr);
		$oneData['id'] = $oneDataArr['id'];
		
		checkEventData($dayData,$dayDataArr);
		$dayData['id'] = $dayDataArr['id'];
		checkEventData($dayData2,$dayDataArr2);
		$dayData2['id'] = $dayDataArr2['id'];
		checkEventData($dayData3,$dayDataArr3);
		$dayData3['id'] = $dayDataArr3['id'];
		
		checkEventData($weekData,$weekDataArr);
		$weekData['id'] = $weekDataArr['id'];
		checkEventData($weekData2,$weekDataArr2);
		$weekData2['id'] = $weekDataArr2['id'];
		checkEventData($weekData3,$weekDataArr3);
		$weekData3['id'] = $weekDataArr3['id'];
		
		checkEventData($week2Data,$week2DataArr);
		$week2Data['id'] = $week2DataArr['id'];
		checkEventData($week2Data2,$week2DataArr2);
		$week2Data2['id'] = $week2DataArr2['id'];
		checkEventData($week2Data3,$week2DataArr3);
		$week2Data3['id'] = $week2DataArr3['id'];
		return true;
	}
}

function checkEventData($data,$eventData){
	global $pushTo;
	if($data['course'] != $eventData['course']) exit('课程数据异常course');
	if($data['text'] != $eventData['text']) exit('课程数据异常text');
	if($data['rec_type']){
		$data['rec_type'] .= '___';
		if($data['week']) $data['rec_type'] .= implode(',',$data['week']);
		$data['rec_type'] .= '#';
		if($data['num'] > 1 && $data['week']) $data['rec_type'] .= $data['num'];
	}
	if($data['rec_type'] != $eventData['rec_type']) exit('课程数据异常rec_type');
	if($data['start_hour'].':'.$data['start_minute'] != date('H:i',strtotime($eventData['start_date']))) exit('课程数据异常start_hour_minute');	
	if($data['end_hour'].':'.$data['end_minute'] != date('H:i',strtotime($eventData['end_date']))) exit('课程数据异常end_hour_minute');
	if($data['class_time'] != $eventData['class_time']) exit('课程数据异常class_time');
	if($data['color'] != $eventData['color']) exit('课程数据异常color');
	$pushTo[$data['id']]['teacher'] = array_keys($data['teacher_op']);
	$pushTo[$data['id']]['student'] = array_keys($data['student_op']);
}

function checkStudentCourse(){
	global $oneData;
	global $dayData;
	global $dayData2;
	global $dayData3;
	global $weekData;
	global $weekData2;
	global $weekData3;
	global $week2Data;
	global $week2Data2;
	global $week2Data3;
	$_Student_Course = load_model('student_course');
	$students = $_Student_Course->getColumn(array('event'=>$oneData['id']),'student');
	array_diff(array_values($students),array_keys($oneData['student_op'])) ? exit($oneData['text'].'学生数据异常') : 1;
	
	$students = $_Student_Course->getColumn(array('event'=>$dayData['id']),'student');
	array_diff(array_values($students),array_keys($dayData['student_op'])) ? exit($dayData['text'].'学生数据异常') : 1;
	$students = $_Student_Course->getColumn(array('event'=>$dayData2['id']),'student');
	array_diff(array_values($students),array_keys($dayData2['student_op'])) ? exit($dayData2['text'].'学生数据异常') : 1;
	$students = $_Student_Course->getColumn(array('event'=>$dayData3['id']),'student');
	array_diff(array_values($students),array_keys($dayData3['student_op'])) ? exit($dayData3['text'].'学生数据异常') : 1;
	
	
	$students = $_Student_Course->getColumn(array('event'=>$weekData['id']),'student');
	array_diff(array_values($students),array_keys($weekData['student_op'])) ? exit($weekData['text'].'学生数据异常') : 1;
	$students = $_Student_Course->getColumn(array('event'=>$weekData2['id']),'student');
	array_diff(array_values($students),array_keys($weekData2['student_op'])) ? exit($weekData2['text'].'学生数据异常') : 1;
	$students = $_Student_Course->getColumn(array('event'=>$weekData3['id']),'student');
	array_diff(array_values($students),array_keys($weekData3['student_op'])) ? exit($weekData3['text'].'学生数据异常') : 1;
	
	
	$students = $_Student_Course->getColumn(array('event'=>$week2Data['id']),'student');
	array_diff(array_values($students),array_keys($week2Data['student_op'])) ? exit($week2Data['text'].'学生数据异常') : 1;
	$students = $_Student_Course->getColumn(array('event'=>$week2Data2['id']),'student');
	array_diff(array_values($students),array_keys($week2Data2['student_op'])) ? exit($week2Data2['text'].'学生数据异常') : 1;
	$students = $_Student_Course->getColumn(array('event'=>$week2Data3['id']),'student');
	array_diff(array_values($students),array_keys($week2Data3['student_op'])) ? exit($week2Data3['text'].'学生数据异常') : 1;
}

function checkTeacherCourse(){
	global $oneData;
	global $dayData;
	global $dayData2;
	global $dayData3;
	global $weekData;
	global $weekData2;
	global $weekData3;
	global $week2Data;
	global $week2Data2;
	global $week2Data3;
	$_Teacher_Course = load_model('teacher_course');
	$teachers = $_Teacher_Course->getColumn(array('event'=>$oneData['id']),'teacher');
	array_diff(array_values($teachers),array_keys($oneData['teacher_op'])) ? exit($oneData['text'].'老师数据异常') : 1;
	
	$teachers = $_Teacher_Course->getColumn(array('event'=>$dayData['id']),'teacher');
	array_diff(array_values($teachers),array_keys($dayData['teacher_op'])) ? exit($dayData['text'].'老师数据异常') : 1;
	$teachers = $_Teacher_Course->getColumn(array('event'=>$dayData2['id']),'teacher');
	array_diff(array_values($teachers),array_keys($dayData2['teacher_op'])) ? exit($dayData2['text'].'老师数据异常') : 1;
	$teachers = $_Teacher_Course->getColumn(array('event'=>$dayData3['id']),'teacher');
	array_diff(array_values($teachers),array_keys($dayData3['teacher_op'])) ? exit($dayData3['text'].'老师数据异常') : 1;
	
	
	$teachers = $_Teacher_Course->getColumn(array('event'=>$weekData['id']),'teacher');
	array_diff(array_values($teachers),array_keys($weekData['teacher_op'])) ? exit($weekData['text'].'老师数据异常') : 1;
	$teachers = $_Teacher_Course->getColumn(array('event'=>$weekData2['id']),'teacher');
	array_diff(array_values($teachers),array_keys($weekData2['teacher_op'])) ? exit($weekData2['text'].'老师数据异常') : 1;
	$teachers = $_Teacher_Course->getColumn(array('event'=>$weekData3['id']),'teacher');
	array_diff(array_values($teachers),array_keys($weekData3['teacher_op'])) ? exit($weekData3['text'].'老师数据异常') : 1;
	
	
	$teachers = $_Teacher_Course->getColumn(array('event'=>$week2Data['id']),'teacher');
	array_diff(array_values($teachers),array_keys($week2Data['teacher_op'])) ? exit($week2Data['text'].'老师数据异常') : 1;
	$teachers = $_Teacher_Course->getColumn(array('event'=>$week2Data2['id']),'teacher');
	array_diff(array_values($teachers),array_keys($week2Data2['teacher_op'])) ? exit($week2Data2['text'].'老师数据异常') : 1;
	$teachers = $_Teacher_Course->getColumn(array('event'=>$week2Data3['id']),'teacher');
	array_diff(array_values($teachers),array_keys($week2Data3['teacher_op'])) ? exit($week2Data3['text'].'老师数据异常') : 1;
}

function checkLogs($act="add"){
	global $nowTime;
	global $pushTo;
	$_Logs = load_model('logs');
	$studentLogs = $_Logs->getAll(array('app'=>'event','act'=>$act,'character'=>'student','create_time,>'=>$nowTime));
	$teacherLogs = $_Logs->getAll(array('app'=>'event','act'=>$act,'character'=>'teacher','create_time,>'=>$nowTime));
	$pushTo2 = array();
	if($studentLogs){
		foreach($studentLogs as $studentLog){
			$students = json_decode($studentLog['target'],true);
			$event = json_decode($studentLog['source'],true);
			$pushTo2[$event['event']]['student'] = $students;
		}
	}
	if($teacherLogs){
		foreach($teacherLogs as $teacherLog){
			$teachers = json_decode($teacherLog['target'],true);
			$event = json_decode($teacherLog['source'],true);
			$pushTo2[$event['event']]['teacher'] = $teachers;
		}
	}
	foreach($pushTo2 as $event=>$_pushTo2){
		if($pushTo[$event]){
			array_diff($pushTo[$event]['students'],$pushTo2[$event]['students']) ? exit('课程id'.$event.'推送学生数据异常') : 1;
			array_diff($pushTo[$event]['teachers'],$pushTo2[$event]['teachers']) ? exit('课程id'.$event.'推送老师数据异常') : 1;
		}
	}
}


function writeEventLog($type="add"){
	global $oneData;
	global $dayData;
	global $dayData2;
	global $dayData3;
	global $weekData;
	global $weekData2;
	global $weekData3;
	global $week2Data;
	global $week2Data2;
	global $week2Data3;
	$str = "";
	$str .= '$oneData_'.$type.' = '.var_export($oneData,true).';'."\n";
	$str .= '$dayData_'.$type.' = '.var_export($dayData,true).';'."\n";
	$str .= '$dayData2_'.$type.' = '.var_export($dayData2,true).';'."\n";
	$str .= '$dayData3_'.$type.' = '.var_export($dayData3,true).';'."\n";
	$str .= '$weekData_'.$type.' = '.var_export($weekData,true).';'."\n";
	$str .= '$weekData2_'.$type.' = '.var_export($weekData2,true).';'."\n";
	$str .= '$weekData3_'.$type.' = '.var_export($weekData3,true).';'."\n";
	$str .= '$week2Data_'.$type.' = '.var_export($week2Data,true).';'."\n";
	$str .= '$week2Data2_'.$type.' = '.var_export($week2Data2,true).';'."\n";
	$str .= '$week2Data3_'.$type.' = '.var_export($week2Data3,true).';'."\n";
	if($type == "add"){
		unlink(EVENT_FILE);
		file_put_contents(EVENT_FILE,'<?php'."\n".$str.''."\n");
	}else{
		file_put_contents(EVENT_FILE,$str,FILE_APPEND);
	}
	
}
