<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;
/**
 * 同步数据
 * 
 * 所有需要登录和验证权限的控制器都需要继承
 */
class Sync extends Controller
{
	private $oldCon=[
	'type'           => 'mysql',
	'hostname'       => '127.0.0.1',
	'database'       => 'guahao010zrmbwxcom',
	'username'       => 'root',
	'password'       => 'root',
	'hostport'       => '3306',
	'dsn'            => '',
	'params'         => [],
	'charset'        => 'utf8',
	'prefix'         => '',
	'debug'          => true,
	'deploy'         => 0,
	'rw_separate'    => false,
	'master_num'     => 1,
	'slave_no'       => '',
	'fields_strict'  => true,
	'resultset_type' => 'array',
	'auto_timestamp' => false,
	'sql_explain'    => false,
	];

	private $familyMap=array();
	private $diseaseMap=array();
	private $sourceMap=array();
	private $wayMap=array();
	private $userMap=array();

	private $hid=array();
    public function run($id)
    {
        $this->hid=$id;
		$this->syncDic();//同步字典
		$this->syncUser();//同步用户
		$this->syncCus();//同步客户
	}
	
	private function user_map($key)
	{
		return $this->map_get($key,$this->userMap);
	}

	private function map_get($key,$map)
	{
		if($key&&array_key_exists($key, $map)){
        	return $map[$key];//咨询员
        }else{
        	return 0;
        }
    }
    public function syncCus()
    {
    	$lis= Db::connect($this->oldCon)->table('customer')
    	->field("id,name,age,mobile,created create_time,created update_time,mobile,remark,qq,appointment_time yy_time,visit_time jz_time,gender,disease_id,consult_id custom_service_id,guider_id,doctor_id yy_doctor_id,source_id source_id,status+1 status,family_id department_id,yy_mode_id consultation_way_id,case  when is_del=1 then 0 else 1 end is_del,del_time,del_uid,real_name,{$this->hid} hospital_id,consult_id creator_id,4 province_id,99 city_id,id old_id")->select();
    	$cusTable=model('bs.customer');
    	$bbTable=model('bs.booking');
    	$bbMap=array();
    	foreach ($lis as $key => $value) {
    		$old=$value['id'];
    		unset($value['id']);
    		$value['creator_id']=$this->user_map($value['creator_id']);
    		$value['custom_service_id']=$this->user_map($value['custom_service_id']);
    		$value['jz_guide_doctor_id']=$this->user_map($value['guider_id']);
    		$value['yy_doctor_id']=$this->user_map($value['yy_doctor_id']);
    		$value['consultation_way_id']=$this->map_get($value['consultation_way_id'],$this->wayMap);
    		$value['department_id']=$this->map_get($value['department_id'],$this->familyMap);
    		$value['source_id']=$this->map_get($value['source_id'],$this->sourceMap);
    		$value['disease_id']=$this->map_get($value['disease_id'],$this->diseaseMap);
    		$cusTable->data($value)->allowField(true)->isUpdate(false)->save();
    		$value['customer_id']=$cusTable->id;
    		$bbTable->data($value)->allowField(true)->isUpdate(false)->save();
    		$bbMap[$old]=$bbTable->id;
    	}
    	echo "bbMap:".count($bbMap)."<br/>";
        //统计Log
    	$loLis=Db::connect($this->oldCon)->table('visit_log')
    	->field('user_id,created create_time,created update_time,customer_id booking_id,content')
    	->select();
    	foreach ($loLis as $key => $visit) {
    		$visit['user_id']=$this->map_get($visit['user_id'],$this->userMap);
    		$visit['booking_id']=$this->map_get($visit['booking_id'],$bbMap);
    		Db::table('bs_visit')->insert($visit);
    	}
    	echo "loLis:".count($loLis)."<br/>";
    }

    public function syncDic()
    {
        #同步科室病种
    	$this->familyMap=array();
    	$lis=Db::connect($this->oldCon)->query("select id,title name,pid,sort weight,{$this->hid} hospital_id from dict where `key`='Customer_Family'");
    	foreach ($lis as $key => $dict) {
    		$oid=$dict['id'];
    		unset($dict['id']);
    		$id=Db::table('bs_disease')->insertGetId($dict);
    		$this->familyMap[$oid]=$id;
    	}
    	echo "familyMap:".count($this->familyMap)."<br/>";
        //同步病种
    	$this->diseaseMap=array();
    	$lis=Db::connect($this->oldCon)->query("select id,title name,pid,sort weight,{$this->hid} hospital_id from dict where `key`='Customer_Disease' ");
    	foreach ($lis as $key => $dict) {
    		$oid=$dict['id'];
    		unset($dict['id']);
    		if(array_key_exists($dict['pid'], $this->familyMap)){
    			$dict['pid']=$this->familyMap[$dict['pid']];
    			$id=Db::table('bs_disease')->insertGetId($dict);
    			$this->diseaseMap[$oid]=$id;
    		}
    	}
    	echo "diseaseMap:".count($this->diseaseMap)."<br/>";
        #同步来源
    	$rli=Db::connect($this->oldCon)->table('dict')->where('key','eq','Customer_Source')->field("id,title name,sort weight,{$this->hid} hospital_id")->select();
    	$this->sourceMap=array();
    	foreach ($rli as $key => $dict) {
    		$oid=$dict['id'];
    		unset($dict['id']);
    		$id=Db::table('bs_source')->insertGetId($dict);
    		$this->sourceMap[$oid]=$id;
    	}
    	echo "sourceMap:".count($this->sourceMap)."<br/>";
        #同步预约方式
    	$rli=Db::connect($this->oldCon)->table('dict')->where('key','eq','Customer_YY_mode')->field("id,title name,sort weight,{$this->hid} hospital_id,0 pid")->select();
    	$this->wayMap=array();
    	foreach ($rli as $key => $dict) {
    		$oid=$dict['id'];
    		unset($dict['id']);
    		$id=Db::table('bs_consultation_way')->insertGetId($dict);
    		$this->wayMap[$oid]=$id;
    	}
    	echo "wayMap:".count($this->wayMap)."<br/>";
    }

    public function syncUser()
    {
    	$this->userMap=array();
    	$lis=Db::connect($this->oldCon)->table('user')->field('id,user_name login_name,password,salt,status+1 status,mobile,name,email,rank,family_id')->select();
        $buh['hospital_id']=$this->hid;//医院编号
        foreach ($lis as $key => $user) {
        	$id=Db::table('common_admin')->where('login_name','eq',$user['login_name'])->value('id');
        	$oid=$user['id'];
        	$rank=$user['rank'];
        	if(!$id){
        		$user['last_login_time']=time();
        		$user['create_time']=time();
        		$user['update_time']=time();
        		$family_id=$user['family_id'];
        		unset($user['rank']);
        		unset($user['family_id']);
        		unset($user['id']);
        		$id=Db::table('common_admin')->insertGetId($user);
        		$bu['id']=$id;
        		Db::table('bs_user')->insert($bu);
        		$cag['admin_id']=$id;
        		if($rank==1){
        			$cag['group_id']=11;
        		}
        		else if($rank==2){
        			$cag['group_id']=8;
        		}
        		else if($rank==3){
        			$cag['group_id']=3;
        		}
        		else if($rank==4){
        			$cag['group_id']=2;
        		}
        		else if($rank==5){
        			$cag['group_id']=12;
        		}
        		Db::table('common_admin_group')->insert($cag);
                $buh['user_id']=$id;
                Db::table('bs_user_hospital')->insert($buh);
            }
            $this->userMap[$oid]=$id;
        }
        echo "userMap:".count($this->userMap)."<br/>";
    }
}