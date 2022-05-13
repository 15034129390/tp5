<?php
namespace app\wapadmin\controller;
use think\Controller;
use think\Request;
/**
 * 用户中心
 */
class Wap extends Controller
{
	protected $request;
	//构造方法
	function __construct()
	{
		//调用Controller构造方法
		parent::__construct();
		//获取当前请求对象;
		$this->request = Request::instance();
		define('IS_POST',$this->request->isPost());
	}
	//添加设置方法
	public function __set($name,$value) {
		$this->assign($name,$value);
	}
}