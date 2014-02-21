<?php
//统计
define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
require(ROOT_PATH.'/global.php');
$db = db();
/**学生考勤统计*/
$sql = "UPDATE t_student AS t,(";
$sql .= "SELECT b.student,COUNT(1) AS classes,SUM(b.`attend`*b.`attended`) AS `attend`,SUM(b.`leave`*b.`attended`) AS `leave`,SUM(b.`absence`*b.`attended`) AS `absence` FROM (`t_event` AS a) JOIN (`t_course_student` AS b) ON `b`.`event` = `a`.`id` WHERE a.is_loop = 0 AND a.`status` = 0 AND a.rec_type = '' AND a.source = 0 AND a.end_date < CURDATE() GROUP BY b.student";
$sql .= ") as s SET t.classes = s.classes,t.`leave` = s.`leave`,t.absence = s.absence WHERE t.id = s.student";
$db->query($sql);
/**老师课程统计*/
$sql = "UPDATE t_teacher as t,(";
$sql .= "SELECT b.teacher,COUNT(1) AS classes FROM (`t_event` AS a) JOIN (`t_course_teacher` AS b) ON `b`.`event` = `a`.`id` WHERE a.is_loop = 0 AND a.`status` = 0 AND a.rec_type = '' AND a.source = 0 AND a.end_date < CURDATE() GROUP BY b.teacher";
$sql .= ") as s SET t.classes = s.classes WHERE t.user = s.teacher";
$db->query($sql);
/**课程考勤统计*/
$sql = "UPDATE t_event as t,(";
$sql .= "SELECT a.id AS event,SUM(b.`attend`*b.`attended`) AS `attend`,SUM(b.`leave`*b.`attended`) AS `leave`,SUM(b.`absence`*b.`attended`) AS `absence` FROM (`t_event` AS a) JOIN (`t_course_student` AS b) ON `b`.`event` = `a`.`id` WHERE a.is_loop = 0 AND a.`status` = 0 AND a.rec_type = '' AND a.source = 0 AND a.end_date < CURDATE() GROUP BY b.event";
$sql .= ") as s SET t.attend = s.attend,t.`leave` = s.`leave`,t.absence = s.absence WHERE t.id = s.event";
$db->query($sql);

/**老师-学生课程统计*/
/*
$sql = "UPDATE t_teacher_student as t,(";
$sql .= "SELECT d.teacher,d.student,COUNT(1) AS classes,SUM(d.attend*d.attended) AS attend,SUM(d.`leave`*d.attended) AS `leave`,SUM(d.absence*d.attended) AS absence FROM (SELECT b.event,b.student,b.attended,b.attend,b.leave,b.absence,c.teacher   FROM (`t_event` AS a) JOIN (`t_course_student` AS b) ON `b`.`event` = `a`.`id` JOIN (`t_course_teacher` AS c) ON b.event = c.event WHERE a.is_loop = 0 AND a.`status` = 0 AND a.rec_type = '' AND a.source = 0 AND a.end_date < CURDATE()) AS d GROUP BY d.student";
$sql .= ") as s SET t.classes = s.classes,t.attend = s.attend,t.`leave` = s.`leave`,t.absence = s.absence WHERE t.teacher = s.teacher and t.student = s.student";
$db->query($sql);
*/
