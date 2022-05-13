<?php
namespace app\admin\controller\common;
use app\admin\controller\Base;
class Menus extends  Base
{
    public function index()
    {
        $this->lis=$this->model->listTreeFormat();
        return view();
    }
    public function create()
    {
        $this->lis=$this->model->listByPid();
        $this->icons=$this->model->defaultIcon();
        return view();
    }
    public function edit($id)
    {
        $this->info=$this->model->get($id);
        $this->lis=$this->model->listByPid();
        $this->icons=$this->model->defaultIcon();
        return view();
    }
}
