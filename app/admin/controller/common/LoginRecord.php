<?php
namespace app\admin\controller\common;
use app\admin\controller\Base;
class LoginRecord extends  Base
{
   public function index()
    {
        $this->pageInfo=$this->model->pageLis(input('get.kw'),input('get.sdate'),input('get.edate'));
        return view();
    }
}
