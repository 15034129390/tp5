<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

use think\helper\Time;

/**
 * 交易记录
 */
class TradesLog extends Base
{
	public function pageLis($mid=false,$kw='',$sdate=false,$edate=false,$type=0)
	{
		$map=array();
		if($mid){
			$map['bm.id']=$mid;
		}
		if(!empty($kw)){
			$map['c.card_no|bm.name']=array('like',"%$kw%");
		}
        if($type>0){
            $map['m.direction']=$type;
        }
        $tims=$this->date_to_time($sdate,$edate);
        $map['m.create_time']=array('BETWEEN',$tims);
		$lis=$this->alias('m')
		->join('bs_member bm','m.card_id=bm.card_id')
		->join('bs_card c','c.id=m.card_id')
		->join('bs_hospital h','m.hospital_id=h.id')
		->join('common_admin ca','m.operator_id=ca.id','left')
		->field('m.*,bm.name,ca.name user_name,c.card_no,h.name hospital_name')
		->where($map)
		->paginate();
		return $lis;
	}

    public function pageLisTj($mid=false,$kw='',$sdate=false,$edate=false,$type=0)
    {
        $map=array();
        if($mid){
            $map['bm.id']=$mid;
        }
        if(!empty($kw)){
            $map['c.card_no|bm.name']=array('like',"%$kw%");
        }
        if($type>0){
            $map['m.direction']=$type;
        }
        $tims=$this->date_to_time($sdate,$edate);
        $map['m.create_time']=array('BETWEEN',$tims);
        $lis=$this->alias('m')
        ->join('bs_member bm','m.card_id=bm.card_id')
        ->join('bs_card c','c.id=m.card_id')
        ->join('bs_hospital h','m.hospital_id=h.id')
        ->join('common_admin ca','m.operator_id=ca.id','left')
        ->field('sum(m.money) money,sum(m.mz_money) mz_money,sum(m.zy_money) zy_money,sum(m.cash_money) cash_money')
        ->where($map)->select();
        return $lis[0];
    }

    public function exLis($mid=false,$kw='',$sdate=false,$edate=false,$type=0)
    {
        $map=array();
        if($mid){
            $map['bm.id']=$mid;
        }
        if(!empty($kw)){
            $map['c.card_no|bm.name']=array('like',"%$kw%");
        }
        if($type>0){
            $map['m.direction']=$type;
        }
        $tims=$this->date_to_time($sdate,$edate);
        $map['m.create_time']=array('BETWEEN',$tims);
        $lis=$this->alias('m')
        ->join('bs_member bm','m.card_id=bm.card_id')
        ->join('bs_card c','c.id=m.card_id')
        ->join('bs_hospital h','m.hospital_id=h.id')
        ->join('common_admin ca','m.operator_id=ca.id','left')
        ->field('m.*,bm.name,ca.name user_name,c.card_no,h.name hospital_name')
        ->where($map)
        ->select();
        return $lis;
    }

	public function lis($mid=0,$direction=0)
    {
        $map=array();
		if($mid){
			$map['bm.id']=$mid;
		}
		if($direction>0){
			$map['m.direction']=$direction;
		}
        
		$lis=$this->alias('m')
		->join('bs_member bm','m.card_id=bm.card_id')
		->join('bs_card c','c.id=m.card_id')
		->join('common_admin ca','m.operator_id=ca.id','left')
		->join('bs_hospital h','h.id=m.hospital_id')
		->field('m.*,bm.name,ca.name user_name,c.card_no,h.name hospital_name')
		->where($map)
		->select();
		return $lis;
    }


    public function tjcwbb($tims=false,$hid=0)
    {
    	$map=array();
    	if($tims && count($tims)==2){
    		$map['create_time']=array('BETWEEN',$tims);
    	}

    	$tj=$this->group('hospital_id')
        ->field("hospital_id hid,sum(case when direction=1 then money else 0 end) xmoney,sum(case when direction=1 then mz_money else 0 end) xmz,sum(case when direction=1 then zy_money else 0 end) xzy,sum(case when direction=2 then money else 0 end) cmoney,sum(case when direction=2 then mz_money else 0 end) cmz,sum(case when direction=2 then zy_money else 0 end) czy")
        ->where($map)->buildSql();
        $wh=array();
        if($hid>0){
        	$wh['h.id']=$hid;
        }
        $lis=$this->table('bs_hospital')->alias('h')
        ->join($tj.'as tj','tj.hid=h.id','left')->where($wh)->select();
        return $lis;
    }
    public function tjcwbb_time_type()
    {
    	$rd['d']=$this->tjcwbb(Time::today());//今天
    	$rd['w']=$this->tjcwbb(Time::week());//本周
    	$rd['m']=$this->tjcwbb(Time::month());//本月
    	$rd['lm']=$this->tjcwbb(Time::lastMonth());//上月
    	return $rd;
    }
    //日期转时间戳数组
    public  function date_to_time($sdate,$edate)
    {
    	$stime=0;
    	$etime=time()*10;
    	if(!empty($sdate)){
    		$stime=strtotime($sdate);
    	}
    	if(!empty($edate)){
    		$etime=strtotime($edate.' +1 day')-1;
    	}
    	return [$stime,$etime];
    }
    public function tj_member($tims,$hid=0,$kw=''){
    	$map=array();
    	if($tims && count($tims)==2){
    		$map['create_time']=array('BETWEEN',$tims);
    	}
    	if($hid>0){
    		$map['hospital_id']=$hid;
    	}
    	$wh=array();
    	if(!empty($kw)){
    		$wh['m.name|m.mobile|m.phone|c.card_no']=array('like',"%$kw%");
    	}
    	$tj=$this->group('member_id')
        ->field("member_id mid,sum(case when direction=1 then money else 0 end) xmoney,sum(case when direction=1 then mz_money else 0 end) xmz,sum(case when direction=1 then zy_money else 0 end) xzy,sum(case when direction=2 then money else 0 end) cmoney,sum(case when direction=2 then mz_money else 0 end) cmz,sum(case when direction=2 then zy_money else 0 end) czy")
        ->where($map)->buildSql();
        $lis=$this->table('bs_member')->alias('m')
        ->join($tj.'as tj','tj.mid=m.id','left')
        ->join('bs_card c','c.id=m.card_id')
        ->field('m.name,c.card_no,tj.*,m.id')
        ->where($wh)
        ->paginate();
        return $lis;

    }
}