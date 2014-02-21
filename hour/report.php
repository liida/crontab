<?php
//日志入库
define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
require(ROOT_PATH.'/global.php');
$_Report = load_model('report',$_SERVER['argv']);
$_Report->checkLogExist();
//获取日志
$logFile = CLIENT_LOG_PATH."/client_{$_Report->Y}{$_Report->M}{$_Report->D}{$_Report->H}.log";
if(file_exists($logFile)){
	$logContent = file_get_contents($logFile);
	if($logContent){
		$start = "{$_Report->Y}-{$_Report->M}-{$_Report->D} {$_Report->H}:00:00";
		$end = "{$_Report->Y}-{$_Report->M}-{$_Report->D} {$_Report->H}:59:59";
		$sql = "delete from `{$_Report->DB}`.`log_{$_Report->Y}{$_Report->M}{$_Report->D}` where create_time >= '$start' and create_time <= '$end'";
		@$_Report->dbhandle->query($sql);
		$logArr = explode("\n",trim($logContent));
		$logArr = parseLog($logArr);
		$logsArr = array_chunk($logArr,2);
		foreach($logsArr as $__logsArr){
			$sql = "insert into `{$_Report->DB}`.`log_{$_Report->Y}{$_Report->M}{$_Report->D}`(`url`,`method`,`app`,`act`,`ext`,`agent`,`ip`,`create_time`) values";
			foreach($__logsArr as $data){
				$sql .= "('".$data['url']."','".$data['method']."','".$data['app']."','".$data['act']."','".$data['ext']."','".$data['agent']."','".$data['ip']."','".$data['create_time']."'),";
			}
			$sql = mb_substr($sql,0,-1);
			@$_Report->dbhandle->query($sql);
		}
	}
}else{
	exit($logFile."文件不存在\n");
}


function parseLog($logArr=array()){
	if($logArr){
		foreach($logArr as $key=>&$_logArr){
			$ol = $_logArr;
			$_logArr = str_replace("\\", '\\\\', $_logArr);
			$_logArr = str_replace("\r\n", '\n', $_logArr);
			$_logArr = json_decode($_logArr,true);
			if($_logArr['status'] == 200 || $_logArr['status'] == 302){
				$_logArr['url'] = $_logArr['uri'];
				$agent = strtolower($_logArr['agent']);
				if(strpos($agent,'android') !== false){
					$_logArr['agent'] = 'android';
				}elseif(strpos($agent,'iphone') !== false){
					$_logArr['agent'] = 'ios';
				}elseif(strpos($agent,'wap') !== false){
					$_logArr['agent'] = 'wap';
				}else{
					$_logArr['agent'] = 'web';
				}
				$body = array();
				if($_logArr['body'] != '-'){
					parse_str($_logArr['body'],$body);
				}
				$_logArr['app'] = $_logArr['method'] == 'POST' && $body['app']?$body['app']:'index';	
				$_logArr['act'] = $_logArr['method'] == 'POST' && $body['act']?$body['act']:'index';	
				$_logArr['create_time'] = date('Y-m-d H:i:s',strtotime($_logArr['time']));
				unset($body['app']);
				unset($body['act']);
				unset($_logArr['body']);
				unset($_logArr['uri']);
				unset($_logArr['time']);
				$_logArr['ext'] = json_encode($body);	
			}else{
				unset($logArr[$key]);
			}
			
		}
	}
	
	return $logArr;
}