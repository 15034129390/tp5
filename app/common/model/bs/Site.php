<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 站点模型
 */
class Site extends Base
{
    protected $LogMsgParams=array('名','name','医院站点');
    public function allList($hid)
    {
        $map=array();
        $map['hospital_id']=$hid;
        return $this->order('weight desc')->where($map)->select();
    }
    /**
     * 查询所有来源
     * @return [type] [description]
     */
    public function lis($hid)
    {
        $map=array();
        $map['status']=1;
        $map['hospital_id']=$hid;
        return $this->where($map)->order('weight desc')->select();
    }
    public function saveSite($link,$hid)
    {
        $map['url']=array('like','%'.$link.'%');
        $info=$this->where($map)->field('id,name')->find();
        if($info){
            return $info;
        }else{
            $ad['name']=$link;
            $ad['hospital_id']=$hid;
            $ad['url']=$link;
            $this->data($ad);
            $rs=parent::addResult();
            $r['name']=$link;
            $r['id']=$rs->data;
            $r['is_add']=true;
            return $ad;
        }
    }
	/**
     * 设置状态
     * @param  int $id 用户编号
     * @param  int $state  用户状态
     * @return [type]         [description]
     */
	public function updateStatus($id, $state) {
		$data['id']     = $id;
		$data['status'] = $state;
        $this->data($data);
        $this->modify();
        return RS("设置状态成功");
    }

    /**
     * 禁用
     * @param [type] $id 用户编号
     */
    public function disable($id) {
    	return $this->updateStatus($id, 2);
    }

    /**
     * 恢复
     * @param  [type] $id [description]
     * @return [type]          [description]
     */
    public function recovery($id) {
    	return $this->updateStatus($id, 1);
    }
}
