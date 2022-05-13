<?php

/**
 * 返回失败信息
 * @param  int $state 失败状态（非1的数字）
 * @param  string $msg   失败消息
 * @return obj        
 */
function RE($msg,$state=-1)
{
	$result=new \stdClass();
	$result->status=$state;
	$result->info=$msg;
	return $result;
}

/**
 * 成功并返回数据
 * @param Object  $data  返回的数据对象或数组
 * @param string  $info  成功的信息
 * @param integer $state 成功的状态
 */
function RD($data,$info="成功",$state=1)
{
	$result=new \stdClass();
	$result->status=$state;
	if(is_null($data)){
		$result->data=array();
	}else{
		$result->data=$data;
	}
	$result->info=$info;
	return $result;
}

/**
 * 返回处理成功
 * @param string  $msg   成功的消息提示
 * @param integer $state 成功的状态值（默认为1）
 */
function RS($msg='',$state=1)
{
	$result=new \stdClass();
	$result->status=$state;
	$result->info=$msg;
	return $result;
}

/**
 * 生成随机字符串
 * @param  integer  $length 字符串长度
 * @param  integer  $z      字符串种子
 * @return String           随机字符串
 */
function randomkeys($length,$z=0)
{
	$pattern[0]='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$pattern[1]='23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
	$pattern[2]='0123456789';
	$key='';
	for($i=0;$i<$length;$i++)
	{
		$t=mt_rand(0,strlen($pattern[$z])-1);
		$key .= $pattern[$z]{$t};
	}
	return $key;
}

/**
 * 获取IP的地址
 * @param  String $ip IP地址
 * @return String IP所在区域
 */
function get_ip_address($ip)
{
	$Ip = new \ivier\IpLocation(); 
	$area = $Ip->getlocation($ip); 
	return   $area['country'] . $area['area'];
}
function hidtel($phone)
{
    $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i',$phone); //固定电话
    if($IsWhat == 1)
    {
        return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i','$1****$2',$phone);
 
    }
    else
    {
        return  preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$phone);
    }
}

function def($value,$def='')
{
	if(is_null($value)){
		return $def;
	}
	if(empty($value)){
		return $def;
	}
	return $value;
}