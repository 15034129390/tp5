<?php
namespace app\admin\controller\bs;
//引入base控制器
use app\admin\controller\Base;
/**
 * 咨询方式控制器
 * 
 */
class Disease extends  Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this->lis=$this->model->allListTreeFormat(HID);
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $this->nodes=$this->model->allListTreeFormat(HID);
        return view();
    }
    /**
     * 批量创建
     * @return 
     */
    public function batch_create()
    {
        if($this->request->isPost()){
            $this->model->data($this->request->post());
            $result=$this->model->batchAddResult();
            return json($result);
        }
        $this->nodes=$this->model->allListTreeFormat(HID);
        return view();
    }
    /**
     * 显示编辑表单页.
     * 
     * @return \think\Response
     */
    public function edit($id)
    {
        $this->nodes=$this->model->allListTreeFormatNotCurrent(HID,$id);
        $this->info=$this->model->get($id);
        return view();
    }
    /**
     * 禁用
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function disable($id)
    {
        if(IS_POST){
            $r=$this->model->disable($id);
            return json($r);
        }
    }
    /**
     * 恢复
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function recovery($id)
    {
        if(IS_POST){
            $r=$this->model->recovery($id);
            return json($r);
        }
    }
}
