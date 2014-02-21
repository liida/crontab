<?php
/**
 * 获取UNIX/LINUX 下正在运行的进程数量
 */
function unixCountProcess($bin, $script){
	$countCmd = popen("ps -ef | grep \"$bin $script\" | grep -v grep | wc -l", "r");
	$countProc = fread($countCmd, 512);
    pclose($countCmd);
	return intval($countProc);
}

/**
 * 获取UNIX/LINUX 下正在运行的进程id
 */
function unixProcess($bin, $script){
	$idCmd = popen("ps -ef | grep \"$bin $script\" | grep -v grep | awk '{print $2,$3}'", "r");
    $idProc = fread($idCmd, 512);
    pclose($idCmd);
    $ids = array();
    if($idProc){
        $idArr = explode("\n",trim($idProc));
        foreach($idArr as $id){
                 $_idArr = explode(' ',trim($id));
                $ids[$_idArr[1]] = $_idArr[0];
        }
    }
    return $ids;
}

/**
 * 公式代入
 */
function formula($formula, $params) {
	if (!trim($formula))
		return null;
	extract($params);
	$str = "\$formulaResult = ($formula);";
	eval ($str);
	return $formulaResult;
}

function debug($str) {
	echo '[' . date('Y-m-d H:i') . ':00] ' . $str . "\n";
}



function writeLog($fileName,$data) {

	$year	= date('Y');
	$month	= date('m');
	$day	= date('d');
	$path	= $year . '/' . $month . '/';

	$filename = $fileName.'-'.$year . '-' . $month . '-' . $day . '.log';
	
	if (!is_dir(LOG_PATH.'/'. $year)) {
		mkdir(LOG_PATH.'/'. $year);
	}

	$sub_dir = LOG_PATH .'/'. $path;
	if (!is_dir($sub_dir)) {
		mkdir($sub_dir);
	}

	if(is_array($data))$data = implode("\t",$data);

	$data = date('Y-m-d H:i:s')."\t" .$data."\t\n";
	file_put_contents($sub_dir . $filename, $data, FILE_APPEND);
}

/*
function doPost($url, $post = null){    
    $context = array();    
          
    if (is_array($post))    
    {    
        ksort($post);    
          
        $context['http'] = array    
        (    
            'method' => 'POST',    
            'content' => http_build_query($post, '', '&'),    
        );    
    }    
          
    return file_get_contents($url, false, stream_context_create($context));    
}
*/

function doPost( $url, $params, $cookie='./cookie.txt' ){  
    $curl = curl_init($url);  
    curl_setopt($curl, CURLOPT_HEADER, 0);  
    // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。  
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
    // 从证书中检查SSL加密算法是否存在  
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);  
    //模拟用户使用的浏览器，在HTTP请求中包含一个”user-agent”头的字符串。  
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  
    //发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。  
    curl_setopt($curl, CURLOPT_POST, 1);  
    // 将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
    // 使用自动跳转  
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);   
    // 自动设置Referer  
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);  
    // Cookie地址  
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie); 
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);  
    // 全部数据使用HTTP协议中的"POST"操作来发送。要发送文件，  
    // 在文件名前面加上@前缀并使用完整路径。这个参数可以通过urlencoded后的字符串  
    // 类似'para1=val1¶2=val2&...'或使用一个以字段名为键值，字段数据为值的数组  
    // 如果value是一个数组，Content-Type头将会被设置成multipart/form-data。  
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));  
    $result = curl_exec($curl);  
    curl_close($curl);  
    return $result;  
}  


function sendPush(array $pushData){
	foreach($pushData as $pushModel){
		push('db')->add('H_PUSH',$pushModel);
	}
}