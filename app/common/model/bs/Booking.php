<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;
use think\helper\Time;

/**
 * 预约信息表模型
 */
class Booking extends Base
{
   //protected $autoWriteTimestamp = false;
   protected $LogMsgParams=array('名','name','预约信息表');
    /**
     * 获取日志消息
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function getLogMsg($type,$data)
    {
        $info=model('bs.customer')->get($data['customer_id']);
        $msg="了客户名为【".$info['name']."】的预约信息";
        switch ($type) {
            case '1':
            return "添加".$msg;
            case '2':
            return "修改".$msg;
            case '3':
            return "删除".$msg;
        }
    }
    static public $__status=array(
       '1'=>'咨询',
       '2'=>'已预约',
       '3'=>'已到诊',
       '4'=>'未到诊'
       );

    public function getStatus()
    {
        return self::$__status;
    }
    /**
     * 会员lis
     * @param  string $mid [description]
     * @return [type]      [description]
     */
    public function memberLis($mid='')
    {
        $map['m.member_id']=$mid;
        //验证权限
        $map['m.is_del']=1;
        return $this->alias('m')
        ->join('bs_customer c','c.id=m.customer_id','left')
        ->join('bs_hospital bh','bh.id=m.hospital_id')
        ->join('common_admin kf','kf.id=m.custom_service_id','left')
        ->join('bs_disease d','d.id=m.department_id','left')
        ->field('m.*,bh.name hospital_name,c.name,c.mobile,kf.name custom_service_name,c.gender,c.address,c.age,c.idcard,c.profession,d.name department_name')->where($map)->select();
    }
    public function addResult()
    {
        if(HID>0){
        $data=$this->getData();
        //$dm['name']=$data['name'];
        $customer=model('bs.customer');
        $cun=0;
        $ph=array();
        $mo=$data['mobile'];
        $mo2=$data['phone'];
        if(!empty($mo)){
            $ph[]=$mo;
        }
        if(!empty($mo2)){
            $ph[]=$mo2;
        }
        $dm['mobile|phone']=array('in',$ph); 
        // $cun=$customer->where($dm)->count();
        if(count($ph)>0){
        $dm['mobile|phone']=array('in',$ph); 
        $cun=$customer->where($dm)->count();
        }else{
        return RE('mobile or phone is not null');
        }
        $dm['create_time']=array('gt',time()-60*20);//20分钟
        if($cun>0){
            return RE('添加失败，手机号不能重复添加');
        }
        if(!array_key_exists('member_id', $data)){
            $member_id=false;
            if(!empty($data['idcard'])){
                $map['idcard']=$data['idcard'];
                $map['is_del']=1;
                $map['status']=1;
                $member_id=model('bs.member')->where($map)->value('id');
            }
            if($member_id){
                $data['member_id']=$member_id;
                $this->member_id=$member_id;
            }else{
                $map['mobile|phone']=$data['mobile'];
                $map['is_del']=1;
                $map['status']=1;
                $member_id=model('bs.member')->where($map)->value('id');
                if($member_id){
                    $data['member_id']=$member_id;
                    $this->member_id=$member_id;
                }
            }
        }
        if(defined('HID')){
            $data['hospital_id']=HID;
        }
        $customer->data($data);
        if ($data['yy_time']!='') {
         $this->status=2;
     }
     $data['input_ip']=request()->ip();
     $customer->allowField(true);
     $result=$customer->addResult();
     if($result->status==1){
        $this->allowField(true);
        $this->customer_id=$result->data;
        if(defined('HID')){
            $this->hospital_id=HID;
        }
        return parent::addResult();
    }
    return $result;
    }else{
        return RE('请重新登录');
    }
}

    /**
     * 到诊
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function arriving($id)
    {
        $data['id']=$id;
        $data['status']=3;
        $data['jz_time']=time();
        $data['jz_guide_doctor_id']=ADMIN_ID;
        $this->data($data);
        return parent::updateResult();
    }
    public function receive($id)
    {
        $map['id']=$id;
        $map['allot_status']=1;
        $ud['allot_status']=2;
        $ud['custom_service_id']=ADMIN_ID;
        $c=$this->save($ud,$map);
        if($c>0){
            return RS("领取成功！");
        }else{
            return RE("领取失败！有可能被其他人领取了！");
        }
    }

    /**
     * 修改并返回状态
     * @return object Result object
     */
    public function updateResult()
    {
        $data=$this->getData();
        //权限验证
        $map['m.is_del']=1;
        $map['m.id']=$data['id'];

        //基础权限判定
        if(!checkAuth('view_all_reserved_data')){
            if(checkAuth('view_data_in_this_department')){
                //查看部门
                $ids=model('bs.user')->authDeptUids(ADMIN_ID);
                if($ids){
                    $map['m.custom_service_id']=array('in',$ids);
                }else{
                    $map['m.custom_service_id']=array('LT',0);
                }
            }else{
                //查看我的数据
                $map['m.custom_service_id']=ADMIN_ID;
            }
        }
        $info=$this->alias('m')->where($map)->find();
        if($info){
            $customer=model('bs.customer');
            $customer->data($data);
            $customer->id=$info['customer_id'];
            $customer->allowField(true);
            $result=$customer->updateResult();
            if($result->status==1){
                $this->allowField(true);
                return parent::updateResult();
            }
            return $result;
        }else{
            return RE('修改失败！，无权限');
        }
    }
    /*全部咨询查询*/
    public function search($params)
    {
        $this ->_buildSearch($params);
        if(array_key_exists('op',$params)&&$params['op']==1){
            return $this->select();
        }
        $data=$this->whereTime('m.create_time', 'd')->paginate();
        // var_dump($data);
        foreach ($data as $key => $value) {
            $map['m.booking_id']=$value['id'];
            $map['m.status'] = 1;
            $data[$key]['visits']=model('bs.visit')->alias('m')->where($map)
            ->join('common_admin a','a.id=m.user_id') 
            ->field('m.*,a.name user_name')
            ->select();
        }
        //var_dump($this->getLastSql());
        
        return $data;
    }
    public function selfBookingSearch($params)
    {
        $this->_buildSelfBookingSearch($params);
        return $this->paginate();
    }
    /**
     * 首页统计
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function indexTdata()
    {
        $map['is_del']=1;
        $map['hospital_id']=HID;
        $rd['a']=$this->where($map)->count();
        $map['create_time']=array('egt',strtotime(date('Y-m-d')));
        $rd['d']=$this->where($map)->count();
        $map['create_time']=array('BETWEEN',Time::week());
        $rd['w']=$this->where($map)->count();
        $map['create_time']=array('BETWEEN',Time::month());
        $rd['m']=$this->where($map)->count();
        return $rd;
    }
    /**
     * 删除数据
     * @param  int $id Id
     * @return object Result object
     */
    public function delResult($id)
    {
        $data['id']=$id;
        $map['m.is_del']=1;
        $map['m.id']=$id;
        //基础权限判定
        if(!checkAuth('view_all_reserved_data')){
            if(checkAuth('view_data_in_this_department')){
                //查看部门
                $ids=model('bs.user')->authDeptUids(ADMIN_ID);
                if($ids){
                    $map['m.custom_service_id']=array('in',$ids);
                }else{
                    $map['m.custom_service_id']=array('LT',0);
                }
            }else{
                //查看我的数据
                $map['m.custom_service_id']=ADMIN_ID;
            }
        }
        $info=$this->alias('m')->where($map)->find();
        if($info){
            $data['is_del']=0;
            $this->data($data);
            return $this->updateResult();
        }
        return RE('删除失败,无权限！');
    }
    public function simple($id)
    {
        //验证权限
        $map['m.is_del']=1;
        $map['m.id']=$id;
        //基础权限判定
        if(!checkAuth('view_all_reserved_data')){
            if(checkAuth('view_data_in_this_department')){
                //查看部门
                $ids=model('bs.user')->authDeptUids(ADMIN_ID);
                if($ids){
                    $map['m.custom_service_id']=array('in',$ids);
                }else{
                    $map['m.custom_service_id']=array('LT',0);
                }
            }else{
                //查看我的数据
                $map['m.custom_service_id']=ADMIN_ID;
            }
        }
        return $this->alias('m')->join('bs_customer c','c.id=m.customer_id','left')
        ->join('common_admin kf','kf.id=m.custom_service_id','left')
        ->field('m.*,c.name,c.zname zname,c.mobile,kf.name custom_service_name,c.gender,c.phone,c.address,c.age,c.idcard,c.profession')->where($map)->find();

    }
    private function _buildSelfBookingSearch($params)
    {
        $map['m.is_del']=1;
        $map['m.hospital_id']=HID;
        $map['m.is_self_booking']=1;
        $map['m.allot_status']=1;
        //查询条件拼接
        if(array_key_exists('kw', $params)&&!empty($params['kw'])){
            $map['c.name|c.mobile|m.id|m.old_id']=array('like','%'.$params['kw'].'%');
        }
        $this->alias('m')
        ->join('bs_customer c','c.id=m.customer_id','left')
        ->join('bs_consultation_way cw','cw.id=m.consultation_way_id','left')
        ->join('bs_consultation_way cwd','cwd.id=m.appointment_mode_id','left')
        ->join('bs_disease dep','dep.id=m.department_id','left')
        ->join('bs_disease d','d.id=m.disease_id','left')
        ->join('bs_site site','site.id=m.source_site_id','left')
        ->join('bs_source s','s.id=m.source_id','left')
        ->join('bs_come_way cww','cww.id=m.come_way_id','left')
        ->join('bs_area ca','ca.id=m.province_id','left')
        ->join('bs_area cc','cc.id=m.city_id','left')
        ->join('common_admin dct','dct.id=m.yy_doctor_id','left')
        ->join('bs_member bm','bm.id=m.member_id','left')
        ->join('bs_card bc','bc.id=bm.card_id','left')
        ->where($map)
        ->field('m.*,c.name,c.mobile,cw.name way_name,cwd.name way_namea,d.name disease_name,s.name source_name,c.gender,dep.name department_name,cww.name come_way_name,ca.name province_name,cc.name city_name,c.address,site.name source_site_name,site.url site_url,dct.name yy_doctor_name,c.age,bm.name member_name,bc.card_no');
    }
    private function _buildSearch($params)
    {
       // $time1 = strtotime(date('Y-m-d'));
       // $time2 = $time1 + 3600*24;
       // $map['cerate_time'] = between $time1 and $time2 ;
       $map['m.is_del']=1;
       $map['m.hospital_id']=HID;
        //基础权限判定
       if(!checkAuth('view_all_reserved_data')){
        if(checkAuth('view_data_in_this_department')){
            //查看部门
            $ids=model('bs.user')->authDeptUids(ADMIN_ID);
            if($ids){
                if(array_key_exists('kf_id', $params)&&is_numeric($params['kf_id'])&&$params['kf_id']>0){
                    if(in_array($params['kf_id'], $ids)){
                        $map['m.custom_service_id']=$params['kf_id'];
                    }else{
                        $map['m.custom_service_id']=array('in',$ids);
                    }
                }else{
                    $map['m.custom_service_id']=array('in',$ids);
                }
            }else{
                $map['m.custom_service_id']=array('LT',0);
            }

        }else{
            //查看我的数据
            $map['m.custom_service_id']=ADMIN_ID;
        }
    }else{
        if(array_key_exists('kf_id', $params)&&is_numeric($params['kf_id'])&&$params['kf_id']>0){
            $map['m.custom_service_id']=$params['kf_id'];
        }
    }
    if(!checkAuth('view_all_reservation_status_data')){

        if(array_key_exists('dz_status', $params)&&is_numeric($params['dz_status'])&&$params['dz_status']>1){
            $map['m.status']=$params['dz_status'];
        }else{
            $map['m.status']=array('gt',1);
        }
    }else{
        if(array_key_exists('dz_status', $params)&&is_numeric($params['dz_status'])&&$params['dz_status']>0){
            $map['m.status']=$params['dz_status'];
        }
    }
    //查询条件拼接
    if(array_key_exists('kw', $params)&&!empty($params['kw'])){
        $map['c.name|c.mobile|m.id|m.old_id']=array('like','%'.$params['kw'].'%');
    }

    if(array_key_exists('department_id', $params)&&is_numeric($params['department_id'])&&$params['department_id']>0){
        $map['m.department_id']=$params['department_id'];
    }
    if(array_key_exists('bz_id', $params)&&is_numeric($params['bz_id'])&&$params['bz_id']>0){
        $map['m.disease_id']=$params['bz_id'];
    }
    if(array_key_exists('way_id', $params)&&is_numeric($params['way_id'])&&$params['way_id']>0){
        $map['m.consultation_way_id']=$params['way_id'];
    }
    if(array_key_exists('mode_id', $params)&&is_numeric($params['mode_id'])&&$params['mode_id']>0){
        $map['m.appointment_mode_id']=$params['mode_id'];
    }
    if(array_key_exists('sf_id', $params)&&is_numeric($params['sf_id'])&&$params['sf_id']>0){
        $map['m.province_id']=$params['sf_id'];
    }
    if(array_key_exists('sq_id', $params)&&is_numeric($params['sq_id'])&&$params['sq_id']>0){
        $map['m.city_id']=$params['sq_id'];
    }
    if(array_key_exists('site_id', $params)&&is_numeric($params['site_id'])&&$params['site_id']>0){
        $map['m.source_site_id']=$params['site_id'];
    }
    if(array_key_exists('ys_id', $params)&&is_numeric($params['ys_id'])&&$params['ys_id']>0){
        $map['m.yy_doctor_id']=$params['ys_id'];
    }
    if(array_key_exists('ly_id', $params)&&is_numeric($params['ly_id'])&&$params['ly_id']>0){
        $map['m.source_id']=$params['ly_id'];
    }
    if(array_key_exists('source_kw', $params)&&!empty($params['source_kw'])){
        $map['m.source_search_keywords']=array('like','%'.$params['source_kw'].'%');
    }
    $zxt=$this->timeWhereD('zx_dateStart','zx_dateEnd',$params);
    if($zxt){
        $map['m.create_time']=$zxt; 
    }
    $yyt=$this->dateWhereD('yy_dateStart','yy_dateEnd',$params);
    if($yyt){
        $map['m.yy_time']=$yyt; 
    }

    $jzt=$this->timeWhereD('dz_dateStart','dz_dateEnd',$params);
    if($jzt){
        $map['m.jz_time']=$jzt; 
    }
    $zxt=$this->timeWhereD('lvt_dateStart','lvt_dateEnd',$params);
    if($zxt){
        $map['tvc.last_visit_time']=$zxt; 
    }
    
    $order='m.id desc';
    if(array_key_exists('sort_type', $params)){
        switch ($params['sort_type']) {
            case 'zx_time':
            $order='m.create_time desc';
            break;
            case 'yuyueTime':
            $order='m.yy_time desc';
            break;
            case 'userID':
            $order='m.custom_service_id desc';
            break;
            case 'huifangcishu':
            $order='tvc.count desc';
            break;
            case 'guanjianci':
            $order='m.source_search_keywords desc';
            break;
            case 'bz_ID':
            $order='m.disease_id desc';
            break;
            case 'zxfs_ID':
            $order='m.consultation_way_id desc';
            break;
            case 'mode_ID':
            $order='m.appointment_mode_id desc';
            break;
            case 'xx_ID':
            $order='m.source_id desc';
            break;
            case 'xiaofei':
            $order='m.source_id desc';
            break;
            case 'daozhen_time':
            $order='m.yy_time desc';
            break;
            case 'last_visit_time':
            $order='tvc.last_visit_time desc';
             break;
            default:
            $order='m.id desc';
            break;
        }
    }
    $this->alias('m')
    ->join('bs_customer c','c.id=m.customer_id','left')
    ->join('bs_consultation_way cw','cw.id=m.consultation_way_id','left')
    ->join('bs_consultation_way cwd','cwd.id=m.appointment_mode_id','left')
    ->join('bs_disease dep','dep.id=m.department_id','left')
    ->join('bs_disease d','d.id=m.disease_id','left')
    ->join('bs_site site','site.id=m.source_site_id','left')
    ->join('bs_source s','s.id=m.source_id','left')
    ->join('bs_come_way cww','cww.id=m.come_way_id','left')
    ->join('common_admin kf','kf.id=m.custom_service_id','left')
    ->join('bs_area ca','ca.id=m.province_id','left')
    ->join('bs_area cc','cc.id=m.city_id','left')
    ->join('common_admin dct','dct.id=m.yy_doctor_id','left')
    ->join('(select count(1) count,max(create_time) last_visit_time,booking_id from bs_visit group by booking_id) tvc','tvc.booking_id=m.id','left')
    ->join('bs_member bm','bm.id=m.member_id','left')
    ->join('bs_card bc','bc.id=bm.card_id','left')
    ->where($map)
    ->field('m.*,tvc.last_visit_time,c.name,c.zname,c.mobile,cw.name way_name,cwd.name way_namea,d.name disease_name,s.name source_name,kf.name custom_service_name,c.gender,dep.name department_name,cww.name come_way_name,ca.name province_name,cc.name city_name,c.address,site.name source_site_name,site.url site_url,dct.name yy_doctor_name,c.age,tvc.count visit_count,bm.name member_name,bc.card_no')->order($order);
}
public function getStatusTextAttr($value,$data)
{
    if($data['status']==1){
        return "登记";
    } else if($data['status']==3){
        return '已到';
    }else if($data['yy_time']<=date('Y-m-d')){
        return '过期';
    }else{
        return '预约';
    }

}

    #统计
public function _buildField($tims)
{
    $stime=$tims[0];
    $etime=$tims[1];
    $dj=$yy=$dz='';
    if($stime){
        $dj=' and create_time>='.$stime;
        $dz=' and jz_time>='.$stime;
        $yy=" and yy_time>='".date('Y-m-d',$stime)."'";
    }
    if($etime){
        $dj.=' and create_time<='.$etime;
        $dz.=' and jz_time<='.$etime;
        $yy.=" and yy_time<'".date('Y-m-d',$etime+1)."'";
    }
    return "sum(case when 1=1 $dj then 1 else 0 end ) dj,sum(case when status>1 $yy then 1 else 0 end ) yy,sum(case when status=3 $dz then 1 else 0 end ) dz";
}

public function tj_time()
{
    $map['is_del']=1;
    $map['status']=2;
    $map['hospital_id']=HID;
    $wh=' where is_del=1 and hospital_id='.HID;
    $rd['a']=$this->where($map)->count();
    $map['status']=3;
    $rd['a_dz']=$this->where($map)->count();
    //今天
    $df=$this->_buildField(Time::today());
    $rd['d']=$this->query("select $df from bs_booking $wh")[0];
    $mf=$this->_buildField(Time::month());
    $rd['m']=$this->query("select $mf from bs_booking $wh")[0];
    $wf=$this->_buildField(Time::week());
    $rd['w']=$this->query("select $wf from bs_booking $wh")[0];
    //昨天
    $zdf=$this->_buildField(Time::yesterday());
    $rd['zd']=$this->query("select $zdf from bs_booking $wh")[0];
    //上周
    $zwf=$this->_buildField(Time::lastWeek());
    $rd['zw']=$this->query("select $zwf from bs_booking $wh ")[0];
        //上月
    $zmf=$this->_buildField(Time::lastMonth());
    $rd['zm']=$this->query("select $zmf from bs_booking $wh ")[0];
    //统计概况
    $map2['status']=1;
    $map2['hospital_id']=HID;
    $yywayf=$this->_buildField(false,false);
        //客服统计


    $adminModel=model('common.admin');
    $clis=$adminModel->consult(HID);
    $ids=array();
    foreach ($clis as $key => $value) {
        $ids[]=$value['id'];
    }
    if ($ids) {
        # code...
    }else{
        $ids=1;
    }
    unset($map2['hospital_id']);
    $rd['kf']=$this->table('common_admin')->alias('w')->join("(select custom_service_id,$yywayf from bs_booking where is_del=1 group by custom_service_id) tj",'tj.custom_service_id=w.id','left')->field('w.id,w.name,tj.*')->where($map2)->where('w.id','in',$ids)->select();
        //医生
    $clis=$adminModel->doctor(HID);
    $ids=array();
    foreach ($clis as $key => $value) {
        $ids[]=$value['id'];
    }
    if ($ids) {
        # code...
    }else{
        $ids=1;
    }
    $rd['ys']=$this->table('common_admin')->alias('w')->join("(select yy_doctor_id,$yywayf from bs_booking where is_del=1 group by yy_doctor_id) tj",'tj.yy_doctor_id=w.id','left')->field('w.id,w.name,tj.*')->where($map2)->where('w.id','in',$ids)->select();
        //科室
    $map2['hospital_id']=HID;
    $rd['ks']=$this->table('bs_disease')->alias('w')->join("(select department_id,$yywayf from bs_booking where is_del=1 group by department_id) tj",'tj.department_id=w.id','left')->field('w.id,w.name,tj.*')->where($map2)->where('pid','eq','0')->select();
        //来源
    // $rd['comeway']=$this->table('bs_source')->alias('w')->join("(select source_id,$yywayf from bs_booking where is_del=1 group by source_id) tj",'tj.source_id=w.id','left')->field('w.id,w.name,tj.*')->where($map2)->select();
        // 来源媒体
    $rd['yyway']=$this->table('bs_consultation_way')->alias('w')->join("(select consultation_way_id,$yywayf from bs_booking where is_del=1 group by consultation_way_id) tj",'tj.consultation_way_id=w.id','left')->field('w.id,w.name,tj.*')->where('pid','eq','0')->where($map2)->select();
    //预约方式
    $rd['comeway']=$this->table('bs_consultation_way')->alias('w')->join("(select appointment_mode_id,$yywayf from bs_booking where is_del=1 group by appointment_mode_id) tj",'tj.appointment_mode_id=w.id','left')->field('w.id,w.name,tj.*')->where($map2)->where('pid>0')->select();

    return $rd;
}
    /**
     * 客服统计
     * @param  [type] $stime [description]
     * @param  [type] $etime [description]
     * @return [type]        [description]
     */
    public function tj_kf($year,$mouth)
    {
        $yywayf=$this->_buildField([mktime(0, 0 , 0,$mouth,1,$year),mktime(23,59,59,$mouth+1,0,$year)]);
        $map2['status']=1;
        $adminModel=model('common.admin');
        $clis=$adminModel->consult(HID);
        $ids=array();
        foreach ($clis as $key => $value) {
            $ids[]=$value['id'];
        }
        if ($ids) {
             # code...
        }else{
             $ids=1;
        }
        return $this->table('common_admin')->alias('w')->join("(select custom_service_id,$yywayf from bs_booking where is_del=1 group by custom_service_id) tj",'tj.custom_service_id=w.id','left')->field('w.id,w.name,tj.*')->where($map2)->where('w.id','in',$ids)->select();
    }
    #页面统计
    public function tj_ym($year,$mouth)
    {
        $map['is_del']=1;
        $map['hospital_id']=HID;
        return $this->where('create_time','between',[mktime(0, 0 , 0,$mouth,1,$year),mktime(23,59,59,$mouth+1,0,$year)])->group('source_consulting_page')->field('source_consulting_page name,count(1) c')->where($map)->select();
    }
    #关键词统计
    public function tj_kw($year,$mouth)
    {
        $map['is_del']=1;
        $map['hospital_id']=HID;
        return $this->where('create_time','between',[mktime(0, 0 , 0,$mouth,1,$year),mktime(23,59,59,$mouth+1,0,$year)])->group('source_search_keywords')->field('source_search_keywords name,count(1) c')->where($map)->select();
    }
    #按月统计
    public function tj_am($year)
    {
        $rd=array();
        $wh=' where is_del=1 and hospital_id='.HID;
        for ($i=1; $i <13 ; $i++) { 
            $mf=$this->_buildField([mktime(0, 0 , 0,$i,1,$year),mktime(23,59,59,$i+1,0,$year)]);
            $rd[$i]=$this->query("select $mf from bs_booking $wh ")[0];
        }
        return $rd;
    }
    /**
     * 分类时间统计
     * @param  string $value [description]
     * @return [type]        [description]
     */
    private function _type_time_tj($tims,$field,$wh)
    {
        $fileds=$this->_buildField($tims);
        return $this->query("select $field gl_id,$fileds from bs_booking $wh group by $field");
    }
    //统计数据关联
    private function _gl_data(&$data,$id)
    {
        foreach ($data as $key => $value) {
            if($value['gl_id']==$id){
                unset($data[$key]);
                return $value;
            }
        }
        return ['dj'=>0,'yy'=>0,'dz'=>0];
    }
    #科室统计
    public function tj_ks()
    {
        $map['status']=1;
        $map['pid']=0;//科室
        $map['hospital_id']=HID;
        $wh=' where is_del=1 and hospital_id='.HID;
        $field='department_id';
        $ksLis=$this->table('bs_disease')->where($map)->select();
        #统计数据
        $ds=$this->_type_time_tj(Time::today(),$field,$wh);#今天
        $zt=$this->_type_time_tj(Time::yesterday(),$field,$wh); #昨天
        $bz=$this->_type_time_tj(Time::week(),$field,$wh);#本周
        $by=$this->_type_time_tj(Time::month(),$field,$wh);#本月
        $sy=$this->_type_time_tj(Time::lastMonth(),$field,$wh);#上月
        for ($i=0; $i < count($ksLis); $i++) { 
            $ksLis[$i]['d']=$this->_gl_data($ds,$ksLis[$i]['id']);
            $ksLis[$i]['zt']=$this->_gl_data($zt,$ksLis[$i]['id']);
            $ksLis[$i]['bz']=$this->_gl_data($bz,$ksLis[$i]['id']);
            $ksLis[$i]['by']=$this->_gl_data($by,$ksLis[$i]['id']);
            $ksLis[$i]['sy']=$this->_gl_data($sy,$ksLis[$i]['id']);
        }
        return $ksLis;
    }

    /**
     * 分类统计
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function tj_fl()
    {
        $map['status']=1;
        $map['hospital_id']=HID;
        $wh=' where is_del=1 and hospital_id='.HID;
        $field='consultation_way_id';
        $ksLis=$this->table('bs_consultation_way')->where($map)->select();
        #统计数据
        $ds=$this->_type_time_tj(Time::today(),$field,$wh);#今天
        $zt=$this->_type_time_tj(Time::yesterday(),$field,$wh); #昨天
        $bz=$this->_type_time_tj(Time::week(),$field,$wh);#本周
        $by=$this->_type_time_tj(Time::month(),$field,$wh);#本月
        $sy=$this->_type_time_tj(Time::lastMonth(),$field,$wh);#上月
        for ($i=0; $i < count($ksLis); $i++) { 
            $ksLis[$i]['d']=$this->_gl_data($ds,$ksLis[$i]['id']);
            $ksLis[$i]['zt']=$this->_gl_data($zt,$ksLis[$i]['id']);
            $ksLis[$i]['bz']=$this->_gl_data($bz,$ksLis[$i]['id']);
            $ksLis[$i]['by']=$this->_gl_data($by,$ksLis[$i]['id']);
            $ksLis[$i]['sy']=$this->_gl_data($sy,$ksLis[$i]['id']);
        }
        return $ksLis;
    }
    /**
     * 地区
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function tj_dq()
    {
        $map['pid']=0;
        $wh=' where is_del=1 and hospital_id='.HID;
        $ksLis=$this->table('bs_area')->where($map)->select();
        $field='province_id';
        #统计数据
        $ds=$this->_type_time_tj(Time::today(),$field,$wh);#今天
        $zt=$this->_type_time_tj(Time::yesterday(),$field,$wh); #昨天
        $bz=$this->_type_time_tj(Time::week(),$field,$wh);#本周
        $by=$this->_type_time_tj(Time::month(),$field,$wh);#本月
        $sy=$this->_type_time_tj(Time::lastMonth(),$field,$wh);#上月
        for ($i=0; $i < count($ksLis); $i++) { 
            $ksLis[$i]['d']=$this->_gl_data($ds,$ksLis[$i]['id']);
            $ksLis[$i]['zt']=$this->_gl_data($zt,$ksLis[$i]['id']);
            $ksLis[$i]['bz']=$this->_gl_data($bz,$ksLis[$i]['id']);
            $ksLis[$i]['by']=$this->_gl_data($by,$ksLis[$i]['id']);
            $ksLis[$i]['sy']=$this->_gl_data($sy,$ksLis[$i]['id']);
        }
        return $ksLis;
    }
}
