<?php
define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
require(ROOT_PATH.'/global.php');
load('ustring');
			
$_User = load_model('user');	
$users = $_User->getAll("(firstname != '') OR  (lastname != '') OR (nickname != '')",'', '', false, false, 'id,firstname,lastname,nickname');
if($users){
	foreach($users as $user){
		$data = array();
		$user['firstname'] && $data['firstname_en'] = Ustring::topinyin($user['firstname']);
		$user['lastname'] && $data['lastname_en'] = Ustring::topinyin($user['lastname']);
		$user['nickname'] && $data['nickname_en'] = Ustring::topinyin($user['nickname']);
		if($data){
			$_User->update($data,array('id'=>$user['id']));
		}
	}
}

$_Student = load_model('student');	
$students = $_Student->getAll("(name != '') OR (nickname != '')",'', '', false, false, 'id,name,nickname');
if($students){
	foreach($students as $student){
		$data = array();
		$student['name'] && $data['name_en'] = Ustring::topinyin($student['name']);
		$student['nickname'] && $data['nickname_en'] = Ustring::topinyin($student['nickname']);
		if($data){
			$_Student->update($data,array('id'=>$student['id']));
		}
	}
}