<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 会员卡
 */
class Card extends Base
{
	/**
	 * 消费
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function charge($money,$card,$pass,$mome='')
	{
		if(!defined("ADMIN_ID")){
			return RE("登录超时！");
		}
		$ad['operator_id']=ADMIN_ID;
		$ad['hospital_id']=HID;
		$map['m.card_no']=$card;
		$map['m.status']=1;
		$info=$this->alias('m')->join('bs_member bm','bm.card_id=m.id')->field('m.*,bm.name,bm.phone')->where($map)->find();
		if($info){
			if($info['pass']==$pass){
				if($info['balance']>=$money){
					$this->where('id',$info['id'])->setDec('balance', $money);
					$ad['money']=$money;
					$ad['card_id']=$info['id'];
					$ad['memo']=$mome;
					$ad['direction']=1;
					model('bs.tradesLog')->data($ad)->save();
					//$sms=new \ivier\SmsApi();
					//$sms->sendSelfSite($info['phone'],'你成功，消费'.$money.'元');
					return RS('消费成功');
					//消费成功
				}else{
					//余额不足
					return RE('余额不足');
				}
			}else{
				//密码错误
				return RE('密码错误');
			}
		}else{
			//卡号错误
			return RE('卡号错误');
		}
	}
	/**
	 * 刷卡消费
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function charge2($mz_money=0,$zy_money=0,$money=0,$cash_money=0,$card,$mome='',$update_time,$pass)
	{ 
		if(!defined("ADMIN_ID")){
			return RE("登录超时！");
		}
		$ad['operator_id']=ADMIN_ID;
		$ad['hospital_id']=HID;
		$map['m.card_no']=$card;
		$map['m.status']=1;
		$info=$this->alias('m')->join('bs_member bm','bm.card_id=m.id')->field('m.*,bm.name,bm.phone,bm.id mid')->where($map)->find();
		if($info){
			if ($info['update_time']==$update_time) {
				if($info['pass']==$pass){
					if($info['balance']>=$money){ 
						if ($info['balance']>0&&$money>0) {
							$this->where('id',$info['id'])->setDec('balance', $money);
						}
						if ($info['mz_money']>0&&$mz_money>0) {
							$this->where('id',$info['id'])->setDec('mz_money', $mz_money);
						}
						if ($info['zy_money']>0&&$zy_money>0) {
							$this->where('id',$info['id'])->setDec('zy_money', $zy_money);
						}
						$this->where('id',$info['id'])->setField('update_time', time());
						$ad['mz_money']=$mz_money;
						$ad['zy_money']=$zy_money;
						$ad['cash_money']=$cash_money;
						$ad['money']=$money;
						$ad['card_id']=$info['id'];
						$ad['member_id']=$info['mid'];
						$ad['memo']=$mome;
						$ad['direction']=1;
						model('bs.tradesLog')->data($ad)->save();
						//$sms=new \ivier\SmsApi();
						//$sms->sendSelfSite($info['phone'],'你成功，消费'.$money.'元');
						return RS('消费成功');

					}else{
					//余额不足
						return RE('余额不足');
					}
				}else{
				//密码错误
					return RE('密码错误');
				} 
			}else{
				return RE('卡内信息有变动，请重新获取信息');
			}
		}else{
			//卡号错误
			return RE('卡号错误');
		}
	}
	/**
	 * 充值
	 * @param  [type] $money [description]
	 * @param  [type] $card  [description]
	 * @param  string $mome  [description]
	 * @return [type]        [description]
	 */
	public function recharge($money=0,$mz_money=0,$zy_money=0,$card,$mome='',$update_time)
	{
		if(!defined("ADMIN_ID")){
			return RE("登录超时！");
		}
		$ad['operator_id']=ADMIN_ID;
		$ad['hospital_id']=HID;
		$map['m.card_no']=$card;
		$map['m.status']=1;
		$info=$this->alias('m')->join('bs_member bm','bm.card_id=m.id')->field('m.*,bm.name,bm.phone,bm.id mid')->where($map)->find();
		if($info){
			if($info['update_time']==$update_time){
				if ($money>0) {
					$this->where('id',$info['id'])->setInc('balance', $money); 
				}
				if ($mz_money>0) {
					$this->where('id',$info['id'])->setInc('mz_money', $mz_money);
				}
				if ($zy_money>0) {
					$this->where('id',$info['id'])->setInc('zy_money', $zy_money);
				}
				$this->where('id',$info['id'])->setField('update_time', time());
				$ad['money']=$money;
				$ad['mz_money']=$mz_money;
				$ad['zy_money']=$zy_money;
				$ad['card_id']=$info['id'];
				$ad['memo']=$mome;
				$ad['direction']=2;
				$ad['member_id']=$info['mid'];
				model('bs.tradesLog')->data($ad)->save();
				//$sms=new \ivier\SmsApi();
				//$sms->sendSelfSite($info['phone'],'恭喜你成功充值'.$money.'元');
				return RS('充值成功');
			}else{
				return RE('卡内信息有变动，请重新获取信息');
			}
		}else{
			//卡号错误
			return RE('卡号错误');
		}
	}

	public function login($card,$pass)
	{
		$map['m.card_no']=$card;
		$map['m.status']=1;
		$map['bm.status']=1;
		$map['bm.is_del']=1;
		$info=$this->alias('m')->join('bs_member bm','bm.card_id=m.id')
		->field('m.*,bm.name,bm.phone,bm.id member_id')->where($map)->find();
		if($info){
			if($info['pass']==$pass)
				return RD($info);
			return RE('密码错误！');
		}
		return RE('会员不存在！');
	}
	public function addResult()
	{
		$data=$this->getData();
		$this->data([]);
		$map['card_no']=$data['card_no'];
		$num=$this->where($map)->count();
		if ($num>0) {
			return RE('该卡已使用');
		}else{
			$this->save($data);
			$id=$this->id;
			if ($id>0) {
				model('common.OperationLog')->addLog($id,$this);
				return RD($id);
			}else{
				return RE('失败');
			}
		}
		
	}
}