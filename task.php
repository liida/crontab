<?php
define('ROOT_PATH', realpath(dirname(__FILE__)));
require(ROOT_PATH.'/global.php');
$_Cron = load_model('cron');
$bin = "php";
$handles =  array();
$pipes = array();
//debug('run task:' . date('Y-m-d H:i:s')); 
while (true){
	// 读取所有任务
	$result = $_Cron->getAll('status !=2');
	// 写入任务日志
	$logFilePath = LOG_PATH .'/'. date("Y").'/'. date("m").'/'. date("d");
	if (! is_dir($logFilePath)) {
        $old = umask(0);
        mkdir($logFilePath, 0777, true);
        umask($old);
    }
    // 遍历所有任务
	if($result){
		foreach ($result as $row){
	        $handleString = $row['exec_script'];
	        // 取出参数
	        if (preg_match('/(.+\.php)\s*(.*)/', $row['exec_script'], $regs)) {
	            if ($regs[2]) {
	                $args = explode(' ', $regs[2]);
	                $handleString = $regs[1] . '_' . implode('_', $args);
	            }
	        }
			
			$exec_script = ROOT_PATH."/{$row['exec_type']}/{$row['exec_script']}";
			// 得到这条脚本的现执行进程数
			$countProcess = unixCountProcess($bin, $exec_script);
	
	        // 如果在task执行中，脚本处于暂停状态，停止该进程
	        if ($row['status'] == 2) {
	            if ($countProcess <= 0) continue;
			    //debug('stop service:' . $handleString);
				for ($j = 0; $j < $countProcess; $j++) {
					proc_terminate($handles[$handleString][$j]);
					array_splice($handles[$handleString], $j, 1);
				}
				continue;
	        }
	
	
	        // 下次启动时间
	        $nextStartTime = 0;
	
			if ($row['exec_type'] == 'daemon')
				$nextStartTime = 0;
	
			// 下次启动时间为上次启动时间 + 1小时
			if ($row['exec_type'] == 'hour')
				$nextStartTime = $row['last_exec_time'] + 60 * 60;
	
			// 下次启动时间为上次启动时间 + 1分钟
			if ($row['exec_type'] == 'minute')
				$nextStartTime = $row['last_exec_time'] + 60;
	
			// 下次启动时间为上次启动时间 + 1天
			if ($row['exec_type'] == 'day')
				$nextStartTime = $row['last_exec_time'] + 60 * 60 * 24;
	
			if ($row['exec_type'] == 'week')
				$nextStartTime = $row['last_exec_time'] + 60 * 60 * 24 * 7;
	
			$stime = time();
			// 如果当前时间还不到下次启动时间
			if ($stime < $nextStartTime) {
				//debug($row['exec_script'] . '脚本还不到时间执行');
				continue;
			}
	
	        // 执行条件变量
			$assignVars = array(
				'month' => date('m'),
				'day' => date('d'),
				'weekday' => date('w'),
				'hour' => date('H'),
				'minute' => date('i'),
				'second' => date('s')
			);

			// 如果不满足执行条件，就继续等待
			if (! formula($row['exec_condition'], $assignVars)) {
				//debug($row['exec_script'] . '执行条件不满足');
				continue;
			}
	        // 如果在脚本执行中，需重启该服务
	        if ($row['status'] == 4) {
			    //debug('restart service:' . $handleString);
				for ($j = 0; $j < $countProcess; $j++) {
					proc_terminate($handles[$handleString][$j]);
					array_splice($handles[$handleString], $j, 1);
				}
	            $countProcess = 0;
	        }
			
			// 如果已经达到最大进程数了
			if ($countProcess >= $row['count_process']) {
				//如果不是常驻进程且到达下次执行时间
				if($row['exec_type'] != "daemon" && $stime > $nextStartTime){
					// 强制关闭
					$dieLimit = $countProcess;
					if($dieLimit){
						//debug('kill service:' . $handleString);
					}
				}else{
					// 关掉多余进程
					$dieLimit = $countProcess - $row['count_process'];
					if($dieLimit){
						//debug('kill over service:' . $handleString);
					}
				}
				for ($j = 0; $j < $dieLimit; $j++) {
					proc_terminate($handles[$handleString][$j]);
					array_splice($handles[$handleString], $j, 1);
				}
				$countProcess = $countProcess - $dieLimit;
				continue;
			}
			
			// 需要执行的命令
	        $command = "$bin $exec_script";
	        
			debug('start to run service:' . $handleString);
			for ($i = $countProcess; $i < $row['count_process']; $i++) {
			    $logFile = $logFilePath . '/' . $handleString . '_' . $i . '.log';
	            $descriptors = array(
	    			//0 => array("pipe", "r")
	                1 => array("file", $logFile, "a") // stdout is a file to append to
	            );
	            $handles[$handleString][] = proc_open($command, $descriptors, $pipes);
			}

			$d_time = date("Y-m-d H:i");
			$time	= strtotime($d_time);
			//debug('start to update service:' . $handleString);
			// 更新最后的执行时间，状态
			$_Cron->update(array('last_exec_time'=>$time,'status'=>1),array('cron_id'=>$row['cron_id']));
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