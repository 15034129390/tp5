<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;
use think\helper\Time;
/**
 * 客户信息表模型
 */
class Member extends Base
{
	protected $LogMsgParams=array('名','name','会员');
	public function search($params)
	{
		$this->_buildSearch($params);
		$data=$this->paginate();
		$visitModel=model('bs.visit');
		foreach ($data as $key => $value) {
			$map['m.booking_id']=$value['id'];
			$map['m.status'] = 1;
			$data[$key]['visits']=$visitModel->alias('m')->where($map)
			->join('common_admin a','a.id=m.user_id')
			->field('m.*,a.name user_name')
			->select();
		}
		return $data;
	}
    public function getcard($params)
    {
      $this->_buildSearch2($params);
      $data=$this->find(); 
      return $data;
  }
    /**
     * 删除数据
     * @param  int $id Id
     * @return object Result object
     */
    public function delResult($id)
    {
        $ud['is_del']=0;
        $ud['id']=$id;
        $this->data($ud);
        $result=parent::updateResult();
        if ($result->status==1) {
            return RS('删除成功');
        }else{
            return $result;
        }
    }
    public function addResult()
    {
    	$data=$this->getData();
    	$card=model('bs.card');
    	$card->data($data);
        $this->creator_id=ADMIN_ID;//创建人编号
        $card->allowField(true);
        $result=$card->addResult();
        if($result->status==1){
            $this->allowField(true);
            $this->card_id=$result->data;
            $this->create_hid=HID;
            $mid=parent::addResult();
            $ad['money']=$data['balance'];
            $ad['mz_money']=$data['mz_money'];
            $ad['zy_money']=$data['zy_money'];
            $ad['cash_money']=0; 
            $ad['card_id']=$result->data;
            $ad['memo']='开卡';
            $ad['direction']=2;
            $ad['member_id']=$mid->data;
            $ad['operator_id']=ADMIN_ID;
            $ad['hospital_id']=HID;
            model('bs.tradesLog')->data($ad)->save();
            return  $mid;
        }
        return $result;
    }
    // /**
    //  * 修改并返回状态
    //  * @return object Result object
    //  */
    // public function updateResult()
    // {
    //     $data=$this->getData();
    //     //权限验证
    //     $map['m.is_del']=1;
    //     $map['m.id']=$data['id'];
    //     $info=$this->alias('m')->where($map)->find();
    //     if($info){
    //         $card=model('bs.card');
    //         $card->data($data);
    //         $card->id=$data['customer_id'];
    //         $card->allowField(true);
    //         $result=$card->updateResult();
    //         if($result->status==1){
    //             $this->allowField(true);
    //             return parent::updateResult();
    //         }
    //         return $result;
    //     }else{
    //         return RE('修改失败！，无权限');
    //     }
    // }

    public function simple($id)
    {
        $map['m.id']=$id;
        $info=$this->alias('m')->join('bs_card c','c.id=m.card_id','left')
        ->field('m.*,c.card_no')
        ->where($map)->find();
        return $info;
    }

    private function _buildSearch($params)
    {
        $map['m.is_del']=1;
        //查询条件拼接
        if(array_key_exists('kw', $params)&&!empty($params['kw'])){
            $map['m.name|m.phone|m.id|c.card_no']=array('like','%'.$params['kw'].'%');
        }
        $zxt=$this->timeWhereD('sdate','edate',$params);
        if($zxt){
            $map['m.create_time']=$zxt; 
        }
        $order='m.id desc';
        $this->alias('m')
        ->join('bs_card c','c.id=m.card_id','left')
        ->join('common_admin u','u.id=m.creator_id','left')
        ->join('bs_area ca','ca.id=m.province_id','left')
        ->join('bs_area cc','cc.id=m.city_id','left')
        ->join('bs_hospital h','h.id=m.create_hid','left')
        ->where($map)
        ->field('m.*,h.name hname,h.short_name hshort_name,u.name user_name,ca.name province_name,cc.name city_name,c.card_no,c.balance,c.zy_money,c.mz_money')
        ->order($order);
    }
    private function _buildSearch2($params)
    {
        $map['m.is_del']=1;
        //查询条件拼接
        $map['c.card_no']=$params;
        $this->alias('m')
        ->join('bs_card c','c.id=m.card_id','left')
        ->join('common_admin u','u.id=m.creator_id','left')
        ->join('bs_area ca','ca.id=m.province_id','left')
        ->join('bs_area cc','cc.id=m.city_id','left')
        ->where($map)
        ->field('m.*,u.name user_name,ca.name province_name,cc.name city_name,c.card_no,c.balance,c.zy_money,c.mz_money,c.update_time uptime');
        
    }
    public function getMCard($mid)
    {
        $map['m.is_del']=1;
        $map['m.id']=$mid;
        $info=$this->alias('m')
        ->join('bs_card c','c.id=m.card_id')
        ->where($map)
        ->field('c.*,m.name')->find();
        return $info;
    }
    public function tj_hc()
    {
        //$map['is_del']=1;
        $map=array();
        $rd['a']=$this->where($map)->count();
        //今天
        list($start, $end) = Time::today();
        $dw='create_time>='.$start." and create_time<=".$end;
        //本周
        list($start, $end) = Time::week();
        $ww='create_time>='.$start." and create_time<=".$end;
        //本月
        list($start, $end) = Time::month();
        $mw='create_time>='.$start." and create_time<=".$end;
        //上周
        list($start, $end) = Time::lastWeek();
        $lww='create_time>='.$start." and create_time<=".$end;
        //上月
        list($start, $end) = Time::lastMonth();
        $lmw='create_time>='.$start." and create_time<=".$end;
        //最近七天
        list($start, $end) = Time::dayToNow(7);
        $d7w='create_time>='.$start." and create_time<=".$end;
        //今年
        list($start, $end) = Time::year();
        $yw='create_time>='.$start." and create_time<=".$end;
        $tj=$this->where($map)->group('create_hid')
        ->field("create_hid hid,sum(case when $dw then 1 else 0 end) d,sum(case when $ww then 1 else 0 end) w,sum(case when $mw then 1 else 0 end) m,sum(case when $lww then 1 else 0 end) lw,sum(case when $lmw then 1 else 0 end) lm,sum(case when $d7w then 1 else 0 end) d7,sum(case when $yw then 1 else 0 end) y,count(1) a")
        ->buildSql();
        $lis=$this->table('bs_hospital')->alias('h')
        ->join($tj.'as tj','tj.hid=h.id','left')->select();
        return $lis;

    }
    public function welcome_tj()
    {
        $map['hospital_id']=HID;
        list($start, $end) = Time::today();
        $dw='create_time>='.$start." and create_time<=".$end;

        $subQuery =$this->table('bs_trades_log')->group('card_id')->field('card_id')
        ->where($dw)->where($map)->buildSql();
        $rd['rs']=$this->table($subQuery.' as a')->count();

        $map['direction']=2;
        $rd['cz']=$this->table('bs_trades_log')->where($map)->where($dw)->sum('money+zy_money+mz_money');
        $map['direction']=1;
        $rd['xf']=$this->table('bs_trades_log')->where($map)->where($dw)->sum('money+zy_money+mz_money');
        return $rd;
    }
    public function detail($id)
    {
        $map['m.id']=$id;
        $info=$this->alias('m')
        ->join('bs_card c','c.id=m.card_id','left')
        ->join('common_admin u','u.id=m.creator_id','left')
        ->join('bs_area ca','ca.id=m.province_id','left')
        ->join('bs_area cc','cc.id=m.city_id','left')
        ->join('bs_hospital h','h.id=m.create_hid','left')
        ->where($map)
        ->field('m.*,h.name hname,h.short_name hshort_name,u.name user_name,ca.name province_name,cc.name city_name,c.card_no,c.balance,c.zy_money,c.mz_money')
        ->find();

        if($info){
            $where=' member_id='.$info['id'].' or card_id='.$info['card_id'];
            $info['log']=$this->table('bs_trades_log')->where($where)->select();
        }

        return $info;
    }
}
