<?php
namespace app\admin\controller\bs;
//引入base控制器
use app\admin\controller\Base;
/**
 * 用户表控制器
 * 
 */
class User extends  Base
{
    public function index()
    {
        $s=$this->model;
        $p=$s->pageLis(input('kw'));
        $this->pageInfo=$p;
        return view();
    }
    public function disable($id)
    {
        if(IS_POST){
            $r=model('common.admin')->disable($id);
            return json($r);
        }
    }
    public function recovery($id)
    {
        if(IS_POST){
            $r=model('common.admin')->recovery($id);
            return json($r);
        }
    }
    public function create()
    {
        $this->groups=model('common.groups')->select();
        $this->sectors=model('bs.sector')->listTreeFormat();
        $this->hospitals=model('bs.hospital')->lis();
        return view();
    }
    public function edit($id)
    {
        $this->info=$this->model->detail($id);
        $this->groups=model('common.groups')->listAllByAdminId($id);
        $this->sectors=model('bs.sector')->listTreeFormat();
        $this->hospitals=model('bs.hospital')->listAllByUserId($id);
        return view();
    }
    public function setPassword($id)
    {
        $model=$this->model;
        if(IS_POST){
            $r=model('common.admin')->resetPwd($id,input('password'));
            return json($r);
        }
        $this->info=$model->get($id);
        return view();
    }
}
