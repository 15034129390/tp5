<?php

namespace app\common\model\common;
use app\common\model\Base;
/**
 * 登陆日志
 */
class LoginRecord extends Base
{
	protected $LogMsgParams=array('编号','id','登陆日志');
	protected $autoWriteTimestamp = false;
	/**
	 * 是否开启操作日志记录
	 * @param int $type 操作类型
	 */
	public function IsOpenOperationLog($type)
	{
		if($type==1){
			return false;
		}
		return true;
	}
	/**
	 * 重写日志消息方法
	 * @param  [type] $type [description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function getLogMsg($type,$data)
	{
		$msg="了编号为【".$data['id']."】的登录日志信息";
		switch ($type) {
			case '1':
			return "添加".$msg;
			case '2':
			return "修改".$msg;
			case '3':
			return "删除".$msg;
		}
	}
    public function addResult()
	{
		$this->created=time();
		return parent::addResult();
	}
	public function pageLis($keyword='',$sdate='',$edate='')
	{
		
		$map=array();
		if(!empty($keyword)){
			$map['a.name']=array('like',"%$keyword%");
		}
		if(!empty($sdate)){
			$sdate=strtotime($sdate);
		}else{
			$sdate=0;
		}
		if(!empty($edate)){
			$edate=(strtotime($edate)+24*60*60);
		}else{
			$edate=time();
		}
		$this->whereTime('m.created', 'between', [$sdate, $edate]);
		$this->alias('m')->join('common_admin a','a.id=m.admin_id')
		->field('m.*,a.name');
		$p=$this->order('m.id desc')->where($map)->paginate();
		return $p;
	}
}
