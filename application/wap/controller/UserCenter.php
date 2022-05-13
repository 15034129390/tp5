<?php
namespace app\wap\controller;
/**
 * 用户中心
 */
class UserCenter extends Wap
{
	
	function __construct()
	{
		parent::__construct();
		$info=session('memberInfo');
		if($info){
			define('MID',session('memberInfo.member_id'));
		}else{
			$this->redirect('Passport/Login');
			exit(0);
		}
	}
	//个人信息
	public function index()
	{
		$this->mInfo=session('memberInfo');
		$this->member=model('bs.member')->getMCard(MID);
		$this->rlLis=model('bs.tradesLog')->lis(MID,2);
		$this->clLis=model('bs.tradesLog')->lis(MID,1);
		$this->notice=model('bs.notice')->pageLis();
		$this->bookLis=model('bs.booking')->memberLis(MID);
		//医院
		$this->hs=model('bs.hospital')->lis();
		$this->page_title='个人信息';
		return view();
	}
	public function ajax_bz($hid=false)
	{
		$lis=model('bs.disease')->lis($hid,0);
		return json(RD($lis));
	}

	public function guahao()
	{
		$data=input('post.');
		$data['member_id']=MID;
		$data['is_self_booking']=1;
		//渠道绑定
		$r=model('bs.booking')->data($data)->addResult();
		return json($r);
	}
	//充值记录
	public function recharge_log()
	{
		$this->lis=model('bs.tradesLog')->lis(MID,2);
		return view();
	}
	//消费日志
	public function charge_log()
	{
		$this->lis=model('bs.tradesLog')->lis(MID,1);
		$this->page_title='消费日志';
		return view();
	}
	//挂号预约
	public function appointment()
	{
		$this->page_title='挂号预约';
		return view();
	}

	//登录日志
	public function login_log()
	{
		$this->page_title='登录日志';
		return view();
	}
}
?>