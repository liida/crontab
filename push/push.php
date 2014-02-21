<?php
/**
 * 推送(logs->push)
 */
define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
require(ROOT_PATH.'/global.php');
$id = $_SERVER['argv'][1] ? intval($_SERVER['argv'][1]) : 0;
if(!$id) exit;
//取得log信息
$log = load_model('logs')->getLogData($id);
if(!$log) exit;
if(!$log['target']) exit;
$pushData = array();
$pushModel = array(
	'app'=>$log['app'],
	'act'=>$log['act'],
	'from'=> $log['creator'],
	'to'=> 0,
	'character'=>$log['character'],
	'student'=> 0,
	'ext'=> $log['source'],
	'type'=> $log['type'],
);
if($log['character'] == "student"){
	foreach($log['target'] as $student){
		$pushModel['student'] = $student;
		$users = load_model('user_student')->getColumn(array('student'=>$student),'user');
		if(!$users) continue;
		foreach($users as $user){
			$pushModel['to'] = $user;	
			$pushData[] = $pushModel;
		}
	}
}elseif($log['character'] == "teacher" || $log['character'] == "user"){
	foreach($log['target'] as $user){
		$pushModel['to'] = $user;	
		$pushData[] = $pushModel;
	}
}

//课程
if($log['app'] == 'event'){
	/*
	$students = array();
	$teachers = array();
	$event = $log['source']['event'];
	if(!$event) exit;
	$eventInfo = load_model('event')->getRow($notify['event'], false, 'id, pid, text,course,start_date,end_date,class_time,`type`, is_loop,`length`, rec_type, color, teacher,`status`');
	if(!$eventInfo) exit;
	$is_loop = $log['source']['is_loop'];
	if($eventInfo['is_loop'] != $is_loop) exit;
	$whole = $log['source']['whole'];
	if($log['character'] == "teacher"){
		$teachers = $log['target'];
	}elseif($log['character'] == "student"){
		$students = $log['target'];
	}
	if(!$teachers && !$students)  exit;
	if($log['act'] == 'add'){
		//生成学生,老师-课程关系
		
		if($teachers){
			foreach($teachers as $teacher){
				load_model('teacher_course')->create($eventInfo, $teacher);
			}		
		}
		if($students){
			foreach($students as $student)
			{			
				load_model('student_course')->create($eventInfo, $student);
			}
		} 
		
	}elseif($log['act'] == 'update'){
		
		$student_compares = load_model('student_course')->compare($event, $students);
		$teacher_compares = load_model('teacher_course')->compare($event, $teachers);
		$recentEventInfo = load_model('event')->recent($eventInfo); //最近课程
		if($student_compares['new']){
			if($recentEventInfo){ //修改				
				$_start_date = $whole ? array() : array('start_date' => $recentEventInfo['start_date']);
				foreach($student_compares['new'] as $item)
				{
					load_model('student_course')->create(array_merge($eventInfo, $_start_date), $item); //生成学生-课程关系
				}			
			}
		}
		if($student_compares['lost']){
			if($whole)
			{
				load_model('student_course')->delete(array('event' => $event, 'student,in' => $student_compares['lost']));
			}else{
				if($recentEventInfo){ // 修改			
					foreach($student_compares['lost'] as $item)
					{
						load_model('student_course')->update(array('end_date' => $recentEventInfo['end_date']), $item); //生成学生-课程关系
					}			
				}	
			}
			
		}
		if($teacher_compares['new']){
			if($recentEventInfo){ //修改
				$_start_date = $whole ? array() : array('start_date' => $recentEventInfo['start_date']);
				foreach($student_compares['new'] as $item)
				{
					load_model('teacher_course')->create(array_merge($eventInfo, $_start_date), $item); //生成学生-课程关系
				}			
			}
		}
		if($teacher_compares['lost']){
			if($whole)
			{
				load_model('teacher_course')->delete(array('event' => $event, 'teacher_course,in' => $teacher_compares['lost']));
			}else{
				if($recentEventInfo){ // 修改			
					foreach($teacher_compares['new'] as $item)
					{
						load_model('student_course')->update(array('start_date' => $recentEventInfo['end_date']), $item); //生成学生-课程关系
					}			
				}	
			}
		}
		
	}
	*/
	
//通知
}elseif($log['app'] == 'notify'){
	$notifyId = $log['source']['notifyId'];
	$notify = load_model('notify')->getRow($notifyId);
	if(!$notify) exit;
	$messageData = array(
		'content'=>$notify['content'],
		'event'=>$notify['event'],
		'from'=>$notify['creator'],
		'to'=>0,
		'student'=>0,
		'character'=>$log['character'],
		'type'=>0,
		'create_time'=>time(),
		'school'=>$notify['school'],
		'status'=>0,
		'source'=>json_encode(array()),
		'attachs'=>$notify['attachs'],
		'pid'=>$notifyId,
		'reply'=>0,
	);
	//新增通知分发
	if($log['act'] == 'add'){
		//问卷
		if($notify['vote']){
			$messageData['type'] = 2;
			$messageData['source'] = json_encode(array('id'=>$notify['vote']));
		//课程通知
		}elseif($notify['event']){
			$messageData['type'] = 1;
			$messageData['source'] = json_encode(load_model('event')->getRow($notify['event'], false, 'id, pid, text,course,start_date,end_date,class_time,`type`, is_loop,`length`, rec_type, color, teacher,`status`'));
		//系统消息
		}
		$_Message = load_model('message');
		foreach($pushData as &$pushModel){
			//发送消息
			$messageData['to'] = $pushModel['to'];
			$messageData['student'] = $pushModel['student'];
			if($messageData['type'] == 2){
				$messageInfo = $_Message->getRow(array('to'=>$messageData['to'],'student'=>$messageData['student'],'type'=>2,'source'=>$messageData['source']));
				if($messageInfo){
					$messageId = $messageInfo['id'];
				}else{
					$messageId = $_Message->insert($messageData);
				}
			}else{
				$messageId = $_Message->insert($messageData);
			}
			$pushModel['ext'] = array('messageId'=>$messageId,'type'=>$messageData['type']);
		}
	}
	load_model('notify')->update(array('status'=>1),array('id'=>$notifyId));
}
sendPush($pushData);