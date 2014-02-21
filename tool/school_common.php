<?php
define('POST_URL', 'http://school.hulapai.com/');
define('COOKIE_FILE', './cookie.txt');
define('EVENT_FILE', './event_cfg.php');
define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
require(ROOT_PATH.'/global.php');
$argv = $_SERVER['argv'];
//登录
$username = isset($argv[1]) ? $argv[1] : '';
$passwd = isset($argv[2]) ? $argv[2] : '';
if(!$username || !$passwd) exit;
$data = array(
	'username'=>$username,
	'password'=>$passwd,
);
unlink(COOKIE_FILE);
$return  = doPost(POST_URL.'login',$data);
preg_match_all('/\/school\?id=(\d+)/',$return,$schools);
$school = $schools[1][0] ? $schools[1][0] : 0;
if(!$school){
	exit("没有机构");
}
	
$return  = doPost(POST_URL.'school?id='.$school,array());
$info = load_model('admin_user',array('table'=>'admin_user'))->getRow(array('id'=>$school));
$school = $info['school'] ? $info['school'] : 0;
if(!$school) exit("没有机构");
//选择机构
$return  = doPost(POST_URL.'School/Info/index',array());
//获取课程分类
$return  = doPost(POST_URL.'School/Course/index',array());
preg_match_all('/\/school\/course\/delete\?id=(\d+)/',$return,$courses);
$course = $courses[1][0] ? $courses[1][0] : 0;
if(!$course){
	exit("没有授课内容");
}
//获取老师
$return  = doPost(POST_URL.'School/Teacher/index',array());
preg_match_all('/\/school\/teacher\/freeze\?teacher=(\d+)/',$return,$teachers);
$teachers = $teachers[1] ? $teachers[1] : array();
if(!$teachers){
	exit("没有老师");
}
//获取学生
$return  = doPost(POST_URL.'School/Student/index',array());
preg_match_all('/\/school\/student\/freeze\?student=(\d+)/',$return,$students);
$students = $students[1] ? $students[1] : array();
if(!$students){
	exit("没有学生");
}

$teacher_keys = array_rand($teachers,(count($teachers) > 2 ? 2:1));
$student_keys = array_rand($students,(count($students) > 2 ? 2:1));
$teacher_op = array();
foreach($teacher_keys as $teacher_key){
	$teacher_op[$teachers[$teacher_key]] = 15;
}
$student_op = array();
foreach($student_keys as $student_key){
	$student_op[$students[$student_key]] = $students[$student_key];
}
