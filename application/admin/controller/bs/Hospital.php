<?php
namespace app\admin\controller\bs;
//引入base控制器
use app\admin\controller\Base;
/**
 * 医院控制器
 * 
 */
class Hospital extends  Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this->lis=$this->model->allList();
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view();
    }
    /**
     * 显示编辑表单页.
     * 
     * @return \think\Response
     */
    public function edit($id)
    {
        $this->info=$this->model->get($id);
        return view();
    }

    public function disable($id)
    {
        if(IS_POST){
            $r=$this->model->disable($id);
            return json($r);
        }
    }
    
    public function recovery($id)
    {
        if(IS_POST){
            $r=$this->model->recovery($id);
            return json($r);
        }
    }
}
