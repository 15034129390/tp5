<?php
namespace app\wap\controller;

class Passport extends Wap
{
	//登录
    public function login()
    {
        if(IS_POST){
            $card=input('post.card');
            $pass=input('post.password');
            $result=model('bs.card')->login($card,$pass);
            if($result->status!=1){
                $this->error($result->info);
                return;
            }
            session("memberInfo",$result->data);
            $this->redirect('UserCenter/Index');
        }
        $this->page_title='会员登录';
        return view();
    }
    //退出登录
    public function logout()
    {
        session(null);
        $this->redirect('login');
    }
}