<?php
namespace app\admin\controller\bs;
//引入base控制器
use app\admin\controller\Base;
/**
 * 会员信息
 * 
 */
class Member extends  Base
{
	public function index()
	{  
		$pageInfo=$this->model->search(input('get.'));
		$this->pageInfo=$pageInfo;
		return view();
	}
	
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
        $this->info=$this->model->simple($id);
        return view();
    }
	//检查该卡是否已使用
	public function check_code()
	{
		if(IS_POST){
             $r = array('status'=>1);
            return json($r);
        }
	}
	public function log()
	{
		$this->lis=model('bs.tradesLog')->lis(input('param.id'));
		return view();
	}
	public function tjHc()
	{
		$this->lis=$this->model->tj_hc();
		return view();
	}
	//会员消费统计
	public function total()
	{
		$tlMd=model('bs.tradesLog');
		$this->pageInfo=$tlMd->tj_member($tlMd->date_to_time(input('get.sdate'),input('get.edate')),input('get.hid'));
		return view();
	}
	//消费报表
	public function reporting()
	{
		$this->page_title='财务报表'; 
		$tlMd=model('bs.tradesLog');
		$this->tjlis=$tlMd->tjcwbb($tlMd->date_to_time(input('get.sdate'),input('get.edate')),input('get.hid'));  
		return view();
	}
	 
}