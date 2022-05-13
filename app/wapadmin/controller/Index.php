<?php
namespace app\wapadmin\controller;

class Index extends Wap
{
	function __construct()
	{
		parent::__construct();
		$info=session('WapAdminInfo');
		if($info){
			define('ADMIN_ID',session('WapAdminInfo.id'));
		}else{
			$this->redirect('Passport/Login');
			exit(0);
		}
	}
	//个人信息
	public function index()
	{
		$this->mInfo=session('WapAdminInfo');
		$this->page_title='会员管理系统';
		$tjlis=model('bs.member')->tj_hc();
		$this->tjlis=$tjlis;
		// var_dump($tjlis);
		return view();
	}
	//会员列表
	public function member()
	{
		$this->page_title='会员列表';
		$this->pageInfo=model('bs.member')->search(input('get.'));
		return view();
	}
	public function member_detail($id)
	{
		$this->info=model('bs.member')->detail($id);
		return view();
	}
	//财务报表
	public function cwbb()
	{
		$this->page_title='财务报表';
		//财务报表
		$model=model('bs.tradesLog');
		$this->tjlis=$model->tjcwbb();
		$this->tlis=$model->tjcwbb_time_type();
		return view();
	}
	
}
