<?php 
namespace ivier;
/**
 * 短信Api
 */
class SmsApi
{
	/**
	 * 短信发送
	 * @param  [type] $phone   手机号
	 * @param  [type] $content 短信内容
	 * @param  [type] $sign    签名
	 * @return [type]          状态
	 */
	public function send($phone,$content,$sign)
	{
		$p['name']='18323555999';
		$p['pwd']='954B019AD324EC00290548A1B93B';
		$p['sign']=$sign;
		$p['mobile']=$phone;
		$p['content']=$content;
		$p['type']='pt';
		$r= $this->http('http://web.1xinxi.cn/asmx/smsservice.aspx',$p,'POST');
		$this->writeLog($phone.','.$content."\r\n".$r,$p);//记录日志
		$ra=explode(',',$r);
		return $ra;
	}

	public function sendSelfSite($phone,$content)
	{
		$this->send($phone,$content,'远大集团');
	}

	public function http($url,$data = '', $method = 'GET'){
		return $this->http2($url,array(), $data , $method);
	}

	public function http2($url, $param, $data = '', $method = 'GET'){
		$opts = array(
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			);
		$opts[CURLOPT_URL] = $url . '?' . http_build_query($param);
		if(strtoupper($method) == 'POST'){
			$opts[CURLOPT_POST] = 1;
			$opts[CURLOPT_POSTFIELDS] = $data;
			if(is_string($data)){
            	//发送JSON数据
				$opts[CURLOPT_HTTPHEADER] = array(
					'Content-Type: application/json; charset=utf-8',  
					'Content-Length: ' . strlen($data),
					);
			}
		}
		/* 初始化并执行curl请求 */
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$data  = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
        //发生错误，抛出异常
		if($error) throw new \Exception('请求发生错误：' . $error);
		return  $data;
	}
	public function writeLog($msg,$p)
	{
		$msg=date('H:i:s')."    ".$msg."\r\nparams:".http_build_query($p)."\r\n\r\n";
		error_log($msg,3,'./../runtime/log/'.date('Ym/d').'_sms.log'); 
	}
}