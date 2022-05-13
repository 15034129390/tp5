<?php
namespace app\wapadmin\controller;

class Passport extends Wap
{
	//登录
    public function login()
    {
        if(IS_POST){
            $card=input('post.card');
            $pass=input('post.password');
            $r=model('common.Admin')->login($card,md5($pass));
            if($r->status==1){
                session('WapAdminInfo',$r->data);
                $this->_getActions($r->data['id']);
                $this->redirect('Index/Index');
            }else{
                $this->error($r->info);
                return;
            }
        }
        $this->page_title='系统登录';
        return view();
    }
     #获取权限
    private function _getActions($uid)
    {
        $codes=model('common.Actions')->listByAdminId($uid);
        session('WapAdminActions',$codes);
    }
    //退出登录
    public function logout()
    {
        session(null);
        $this->redirect('login');
    }
}