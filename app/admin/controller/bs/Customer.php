<?php
namespace app\admin\controller\bs;
//引入base控制器
use app\admin\controller\Base;
/**
 * 会员控制器
 * 
 */
class Customer extends  Base
{
	protected $isCRUD=false;
	public function index()
	{  
		return view();
	}

	public function create()
	{
		return view();
	}
	//检查该卡是否已使用
	public function check_code()
	{
		if(IS_POST){
             $r = array('status'=>1);
            return json($r);
        }
	}
}