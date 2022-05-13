<?php
namespace app\admin\controller\common;
use app\admin\controller\Base;
class Groups extends  Base
{
    public function index()
    {
        $this->lis=$this->model->select();
        return view();
    }
    public function create()
    {
        return view();
    }
    public function edit($id)
    {
        $this->info=$this->model->get($id);
        return view();
    }
    public function auth($id)
    {
        $model=$this->model;
        if(IS_POST){
            $r=$model->saveAuth($id,input('post.ids/a'));
            return json($r);
        }
        $this->tree=$model->ActionTree($id);
        $this->info=$model->get($id);
        return view();
    }
}
