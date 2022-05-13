<?php
namespace app\admin\controller\bs;
//引入base控制器
use app\admin\controller\Base;
/**
 * 统计控制器
 * 
 */
class Statistics extends  Base
{
	protected $isCRUD=false;
	//统计概况
	public function index()
	{ 
		$model=model('bs.booking');
		$this->tj=$model->tj_time();
		return view();
	}
	//分类统计
	public function classify()
	{ 
		$m=model('bs.booking');
		$this->lis=$m->tj_fl();
		return view();
	}
	//按月统计
	public function month()
	{ 
		$m=model('bs.booking');
		$this->lis=$m->tj_am(input('year',date('Y')));
		return view();
	}
	//客服统计
	public function support()
	{ 
		$m=model('bs.booking');
		$this->lis=$m->tj_kf(input('year',date('Y')),input('mouth',date('m')));
		return view();
	}
	//关键词统计
	public function keyword()
	{ 
		$m=model('bs.booking');
		$this->lis=$m->tj_kw(input('year',date('Y')),input('mouth',date('m')));
		return view();
	}
	//咨询页面统计
	public function consulting()
	{ 
		$m=model('bs.booking');
		$this->lis=$m->tj_ym(input('year',date('Y')),input('mouth',date('m')));
		return view();
	}
	//地区统计
	public function region()
	{ 
		$m=model('bs.booking');
		$this->lis=$m->tj_dq();
		return view();
	}
	//科室统计
	public function department()
	{ 
		$m=model('bs.booking');
		$this->lis=$m->tj_ks();
		return view();
	}

}