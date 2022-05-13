<?php
namespace app\admin\controller\common;
use app\admin\controller\Base;

class OperationLog extends  Base
{
    public function index()
    {
        $this->pageInfo=$this->model->pageLis(input('get.kw'),input('get.sdate'),input('get.edate'));
        return view();
    }
    public function index2($tname,$mid)
    {
        $this->pageInfo=$this->model->pageLis('',false,false,$tname,$mid);
        return view();
    }
    public function read($id)
    {
    	$this->info=$this->model->detail($id);
    	return view();
    }
}
