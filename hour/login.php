<?php
//分离登录日志入库
define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
require(ROOT_PATH.'/global.php');
$_Report = load_model('report',$_SERVER['argv']);
$_Report->checkLogExist();
$agents = array(
	'web'=>0,
	'wap'=>1,
	'android'=>2,
	'ios'=>3
);
$start = "{$_Report->Y}-{$_Report->M}-{$_Report->D} {$_Report->H}:00:00";
$end = "{$_Report->Y}-{$_Report->M}-{$_Report->D} {$_Report->H}:59:59";
$sql = "SELECT * FROM `{$_Report->DB}`.`log_{$_Report->Y}{$_Report->M}{$_Report->D}` WHERE method='POST' and create_time >= '$start' and create_time <= '$end' and ((app ='index' AND act='login') OR url='/login')";
$logs = $_Report->dbhandle->fetchAll($sql);
if($logs){
	$sql = "delete from `huladb`.`t_logs_login` where create_time >= '$start' and create_time <= '$end'";
	@$_Report->dbhandle->query($sql);
	$sql = "insert into `huladb`.`t_logs_login`(`account`,`agent`,`ip`,`create_time`) values";
	foreach($logs as $log){
		$ext = json_decode($log['ext'],true);
		if(!$ext) continue;
		$account = $ext['account'] ? $ext['account'] :$ext['username'];
		if(!$account) continue;
		$sql .= "('".$account."','".$agents[$log['agent']]."','".$log['ip']."','".$log['create_time']."'),";
	}
	$sql = mb_substr($sql,0,-1);
	@$_Report->dbhandle->query($sql);
}
//更新记录，去除无效用户
$sql = "update `huladb`.`t_logs_login` as a,`huladb`.`t_user` as b set a.account=b.account,a.user=b.id,a.status = 1 WHERE a.create_time >= '$start' and a.create_time <= '$end' and (b.account = a.account or b.hulaid = a.account)";
@$_Report->dbhandle->query($sql);
$sql = "delete from `huladb`.`t_logs_login` where status = 0";
@$_Report->dbhandle->query($sql);