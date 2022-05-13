<?php
namespace app\common\model\common;
use app\common\model\Base;
/**
 * 管理员模型
 */
class Admin extends Base
{
    public $LogMap=[
    'login_name'=>['登录名','',''],
    'name'=>['名称','',''],
    'password'=>['密码','',''],
    'salt'=>['密码盐','',''],
    'mobile'=>['手机号','',''],
    'email'=>['邮箱','',''],
    'last_login_ip'=>['最后登录IP','',''],
    'last_login_time'=>['最后登录日期','format_time','Y-m-d H:i:s'],
    'last_login_address'=>['最后登录地址','',''],
    'error_count'=>['异常登录次数','',''],
    'memo'=>['备注','',''],
    'status'=>['状态','',''],
    'create_time'=>['注册日期','format_time','Y-m-d H:i:s'],
    'skin'=>['皮肤','',''],
    'update_time'=>['更新日期','format_time','Y-m-d H:i:s']
    ];
    static public $__status=array(
     '1'=>'正常',
     '2'=>'禁用'
     );
    public function pageLis($keyword,$status=0)
    {
        $map=array();
        if(!empty($keyword)){
            $map['u.name|u.mobile|u.login_name']=array('like',"%$keyword%");
        }
        if($status>0){
            $map['u.status']=$status;
        }
        $this->alias('u');
        $this->where($map)->order('u.id desc');
        $data=$this->paginate(10);
        return $data;
    }
    public function getStatusTextAttr($value,$data)
    {
        return self::$__status[$data['status']];
    }

    public function getStatus()
    {
        return self::$__status;
    }

    
    /**
     * 添加管理员
     */
    public function addResult() {
    	$d=$this->data;
    	$lnl=strlen($d['login_name']);
    	if($lnl>4&&$lnl<=20){

    	}else{
    		return RE('登录名必须是5到20位的字符串！');
    	}
    	$this->created=time();
    	$map['login_name']=$d['login_name'];
    	$c=$this->db()->where($map)->count();
    	if($c>0){
    		return RE('用户名已存在');
    	}
    	$d['salt']=randomkeys(4);
    	$d['password']=md5(md5($d['password']).$d['salt']);
    	$d['last_login_ip']=request()->ip();
    	$d['last_login_time']=time();
    	$d['last_login_address']=get_ip_address($d['last_login_ip']);
    	$this->data([]);
        $this->allowField(true)->save($d);
        $id=$this->id;
        if ($id>0) {
            model('common.OperationLog')->addLog($id,$this);
        }
        //添加用户组
        if(isset($d['group_ids'])&&count($d['group_ids'])>0){
            $adminGroups=array();
            $adminGroup['admin_id']=$id;
            foreach ($d['group_ids'] as $key => $value) {
                $adminGroup['group_id']=$value;
                $adminGroups[]=$adminGroup;
            }
            $cag=new AdminGroup();
            $cag->saveAll($adminGroups);
        }
        return RD($this->data);
    }
    public function updateResult()
    {
        $d=$this->data;
        if($d['id']>0){
            $this->modify();
            if(isset($d['group_ids'])){
                $agModel=new AdminGroup();
                $map['admin_id']=$d['id'];
                $agModel->where($map)->delete();
                if(count($d['group_ids'])>0){
                    $adminGroups=array();
                    $adminGroup['admin_id']=$d['id'];
                    foreach ($d['group_ids'] as $key => $value) {
                        $adminGroup['group_id']=$value;
                        $adminGroups[]=$adminGroup;
                    }
                    $agModel->saveAll($adminGroups);
                }
            }

        }else{
            return RE('修改失败，未传管理员编号！');
        }
        return RS('成功');
    }

    /**
     * 保存权限组
     * @param  int $id   管理员编号
     * @param  array $gids 权限组编号数组
     * @return [type]       
     */
    public function saveGroups($id,$gids)
    {
    	$m=new AdminGroup();
    	$map['admin_id']=$id;
    	$m->db()->where($map)->delete();
    	$ads=array();
    	$ad['admin_id']=$id;
    	foreach ($variable as $key => $value) {
    		$ad['group_id']=$value;
    		$ads[]=$ad;
    	}
    	$m->saveAll($ads);
    }
    /**
     * 用户登录
     * @param  [type] $uname [description]
     * @param  [type] $pass  [description]
     * @return [type]        [description]
     */
    public function login($uname, $pass) {

    	$map['login_name'] = $uname;
        $map['status'] = 1;
        $ainfo=$this->get($map);
        if ($ainfo) {
            //验证用户密码是否正确
            if($ainfo['password']==md5($pass.$ainfo['salt'])){
                unset($ainfo['password']);
                unset($ainfo['salt']);
                $up['last_login_ip']   = request()->ip();
                $up['id']              = $ainfo['id'];
                $up['last_login_time'] = time();
                $up['last_login_address']=get_ip_address($up['last_login_ip']);
                $this->update($up);
                //添加登录日志
                $loginRecordModel=new LoginRecord();
                $loginRecordModel->ip= $up['last_login_ip'];
                $loginRecordModel->ip_address=$up['last_login_address'];
                $loginRecordModel->admin_id= $ainfo['id'];
                $loginRecordModel->addResult();
                return RD($ainfo);
            } else {
                return RE('用户名或密码错误');
            }
        }
        return RE('用户名或密码错误');
    }
    /**
     * 重置密码
     * @param  int $userId 用户编号
     * @return resultObj        
     */
    public function resetPwd($userId, $pwd) {
    	$data["id"]       = $userId;
    	$data['salt']     = randomkeys(4);
    	$data['password'] = md5(md5($pwd) . $data['salt']);
        $this->data($data);
        $this->modify();
        return RS("重置成功");
    }

    /**
     * 重置密码
     * @param  int $userId 用户编号
     * @return resultObj        
     */
    public function resetPwd2($userId, $oldPass, $pwd) {
    	$info = $this->get($userId);
    	if ($info['password'] == md5(md5($oldPass) . $info['salt'])) {
    		$data["id"]       = $userId;
    		$data['salt']     = randomkeys(4);
    		$data['password'] = md5(md5($pwd) . $data['salt']);
            $this->data($data);
            $this->modify();
            return RS("操作成功");
        } else {
          return RE('密码错误！');
      }
  }

    /**
     * 设置状态
     * @param  int $userId 用户编号
     * @param  int $state  用户状态
     * @return [type]         [description]
     */
    public function updateStatus($userId, $state) {
    	$data['id']     = $userId;
    	$data['status'] = $state;
    	$this->update($data);
    	return RS("设置状态成功");
    }

    /**
     * 禁用
     * @param [type] $user_id 用户编号
     */
    public function disable($user_id) {
    	return $this->updateStatus($user_id, 2);
    }

    /**
     * 恢复
     * @param  [type] $user_id [description]
     * @return [type]          [description]
     */
    public function recovery($user_id) {
    	return $this->updateStatus($user_id, 1);
    }

    /**
     * 删除
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function del($id) {
    	if ($id == 1) {
    		return RE('超级管理员不能删除');
    	} else {
    		$i=$this->db()->delete($id);
    		if ($i > 0) {
    			return RS('删除成功');
    		}else{
    			return RE('删除失败');
    		}
    	}
    }
    public function lisByActionCode($code,$hid)
    {
        $map['ca.code']=$code;
        $map['buh.hospital_id']=$hid;
        return $this->alias('m')->join('common_admin_group cag','cag.admin_id=m.id')
        ->join('common_group_action cga','cga.group_id=cag.group_id')
        ->join('common_actions ca','ca.id=cga.action_id')
        ->join('bs_user_hospital buh','m.id=buh.user_id')
        ->where($map)->field('m.id,m.name')
        ->distinct(true)->select();
    }
    public function consult($hid)
    {
        return $this->lisByActionCode('can_be_selected_as_consulting',$hid);
    }
    public function doctor($hid)
    {
        return $this->lisByActionCode('can_be_selected_as_doctor',$hid);
    }
}
