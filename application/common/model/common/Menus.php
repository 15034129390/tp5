<?php
namespace app\common\model\common;
use app\common\model\Base;
/**
 * 菜单
 */
class Menus extends Base
{
	protected $LogMsgParams=array('名','name','菜单');
	public static $_defaultIcon=array(
		'Hui-iconfont-root'=>'管理员',
		'Hui-iconfont-manage'=>'管理',
		'Hui-iconfont-home'=>'首页',
		'Hui-iconfont-system'=>'系统'
		);
	public function listTreeFormat()
	{
		$lis=$this->order('weight desc')->select();
		$tree=new \ivier\Tree();
		return $tree->toFormatTree($lis,'name');
	}
	public function listByPid($pid=0)
	{
		$map['pid']=$pid;
		$lis=$this->order('weight desc')->where($map)->select();
		return $lis;
	}
	public function defaultIcon()
	{
		return self::$_defaultIcon;
	}
	private $_path;
	public function listPath($auth)
	{
		$this->_path=array();
		$map['action']=$auth;
		$m=$this->get($map);
		if($m){
			$this->x_path($m->pid);
			$this->_path[]=$m;
		}
		return $this->_path;
	}
	public function x_path($id)
	{
		if($id>0){
			$m=$this->get($id);
			if($m){
				$this->x_path($m->pid);
				$this->_path[]=$m;
			}
		}
	}
}
