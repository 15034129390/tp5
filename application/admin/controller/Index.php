<?php

namespace app\admin\controller;

class Index extends Base
{
  protected $isCRUD=false;
  public function index(){
    $this->tj=model('bs.booking')->indexTdata();
    $this->hs=model('bs.hospital')->lisByUserId(ADMIN_ID);
    return view();
  }
  public function config()
  {
    $cModel=model('Config');
    if(IS_POST){
      $map['key']='subscribe_integral';
      $ud['value']=input('subscribe_integral');
      $cModel->where($map)->save($ud);
      $map['key']='subscribe_red_packets';
      $ud['value']=input('subscribe_red_packets');
      $cModel->where($map)->save($ud);
      return json(RS(''));
    }
    $this->subscribe_red_packets=$cModel->getConfig('subscribe_red_packets',false);
    $this->subscribe_integral=$cModel->getConfig('subscribe_integral',false);	
    return view();
  }
  public function changeHospital($id)
  {
    $hids=model('bs.hospital')->firstIds(ADMIN_ID);
    if($hids){
      if($id>0 && in_array($id, $hids)){
        session("Admin_HID",$id);
        cookie('hospital_id',$id);
        return json(RS('切换成功'));
      }
    }
    return json(RE('切换失败！无医院权限',-1));
  }
  public function resetPwd()
  {
    if(IS_POST){
      $s=model('common.Admin');
      $r=$s->resetPwd2(ADMIN_ID,input('old'),input('pass'));
      return json($r);
    }
    return view();
  }
  /**
     * 账号
     * @param  string $value [description]
     * @return [type]        [description]
     */
  public function personalInfo($value='')
  {
    $this->info=session('AdminInfo');
    return view();
  }
  /**
     * 设置皮肤(颜色)
     */
  public function setSkin()
  {
    $w['id']=ADMIN_ID;
    $d['skin']=input('skin');
    model('common.Admin')->save($d,$w);
    return json(RS());
  }
  public function welcome()
  {
    if(checkAuth('consulting_survey')){ 
      $this->redirect('bs.statistics/index');
    }else if(checkAuth('view_cashier_working_interface')){
      $this->tj=model('bs.member')->welcome_tj();
      return view('welcome_syy');
    }else{
      $this->redirect('bs.booking/index'); 
    }
    return view();
  }
}
