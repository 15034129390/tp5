<?php
namespace app\wap\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
    	$this->redirect('UserCenter/index');
        return view();
    }
    
}
