<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 来源信息模型
 */
class Source extends Base
{
	protected $LogMsgParams=array('名','name','来源信息');
	public function allList($hid)
	{
        $map['hospital_id']=$hid;
		return $this->order('weight desc')->where($map)->select();
	}
    /**
     * 查询所有来源
     * @return [type] [description]
     */
    public function lis($hid)
    {
        $map['m.status']=1;
        $map['m.hospital_id']=$hid;
        return $this->alias('m')->where($map)->order('m.weight desc')->select();
    }
    public function saveSource($link,$hid)
    {
        $map['url']=array('like','%'.$link.'%');
        $info=$this->where($map)->field('id,name')->find();
        if($info){
            return $info;
        }else{
            $ad['name']=$link;
            $ad['url']=$link;
            $ad['m.hospital_id']=$hid;
            $this->data($ad);
            $rs=parent::addResult();
            $r['name']=$link;
            $r['id']=$rs->data;
            $r['is_add']=true;
            return $r;
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
