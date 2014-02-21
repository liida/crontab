<?php
define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
require(ROOT_PATH.'/global.php');
//debug('run push:' . date('Y-m-d H:i:s')); 
$_Log = load_model('logs');
$bin = "php";
$handles =  array();
$pipes = array();
while (true){
	// 读取前10条任务
	$result = $_Log->getAll(array('status'=>0),10);
	// 写入任务日志
	$logFilePath = LOG_PATH .'/'. date("Y").'/'. date("m").'/'. date("d").'/push';
	if (! is_dir($logFilePath)) {
        $old = umask(0);
        mkdir($logFilePath, 0777, true);
        umask($old);
    }
    // 遍历所有需要处理的数据
	if($result){
		foreach ($result as $row){
	        $handleString = $row['app'].'_'.$row['act'].'_'.$row['id'];
			$fileName = ROOT_PATH."/push/push.php";
			if(!file_exists($fileName)) continue;
	        //debug('start to update push:' . $handleString);
			// 更新
			if(!$_Log->update(array('status'=>1),array('id'=>$row['id']))) continue;
			
			
			//debug('start to push:' . $handleString);
			// 需要执行的命令
	        $command = "$bin $fileName {$row['id']}";
	        $descriptors = array(
				//0 => array("pipe", "r"),
	            1 => array("file", $logFilePath . '/' . $handleString . '.log', "a") // stdout is a file to append to
	        );
	        $handles[$handleString][] = proc_open($command, $descriptors, $pipes);
			
		}
	}
	// 清理已经关闭的任务
    if (is_array($handles) && count($handles) > 0) {
        foreach ($handles as $key1 => $handleMulti) {
            foreach ($handleMulti as $key2 =>  $handle) {
                $pstatus = proc_get_status($handle);
                if (! $pstatus['running']) {
                    proc_close($handle);
                    unset($handles[$key1][$key2]);
                    if (count($handles[$key1]) <= 0)
                        unset($handles[$key1]);
                }
            }
        }
        //debug('-----current process-----');
        //print_r($handles);
        //debug('-----current process end-----');
    }
	sleep(5);
}