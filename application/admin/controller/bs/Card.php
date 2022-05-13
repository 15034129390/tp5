<?php
namespace app\admin\controller\bs;
//引入base控制器
use app\admin\controller\Base;

/**
 * 会员卡
 */
class Card extends  Base
{
	/**
	 * 消费
	 * @return [type] [description]
	 */
	public function charge()
	{
		if(IS_POST){
			$mz_money=input('post.mz_money','0');
			$zy_money=input('post.zy_money','0');
			$money=input('post.money','0');
			$cash_money=input('post.cash_money','0');
			if ($mz_money<0||$zy_money<0||$money<0||$cash_money<0) {
				return RE('金额错误');
			}else{  
				return $this->model->charge2($mz_money,$zy_money,$money,$cash_money,input('post.card_no'),input('post.memo'),input('post.update_time'),input('post.pass'));
			}
		} 
		$id=input('param.id');
		if($id){
			$this->info=model('bs.member')->getMCard($id);
			return view('charge1'); 
		}
		
		return view();
	}
	/**
	 * 充值记录
	 * @return [type] [description]
	 */
	public function recharge()
	{
		if(IS_POST){
			$money=input('post.money','0');
			$mz_money=input('post.mz_money','0');
			$zy_money=input('post.zy_money','0');
			if ($mz_money<0||$zy_money<0||$money<0) {
				return RE('金额错误');
			}else{ 
				return $this->model->recharge($money,$mz_money,$zy_money,input('post.card_no'),
					input('post.memo'),input('post.update_time'));
			}
		}
		$id=input('param.id');
		if($id){
			$this->info=model('bs.member')->getMCard($id);

			return view('recharge1');
		}
		return view();
	}
	public function getcode()
	{
		if (IS_POST) { 
			$info=model('bs.member')->getcard(input('post.card'));
			if ($info) {
				$info['balance']=$info['balance']*100;
				$info['mz_money']=$info['mz_money']*100;
				$info['zy_money']=$info['zy_money']*100;
				return json(RD($info));
			}else{
				return json(RE('没有此卡号信息'));
			}
		}

	}
}