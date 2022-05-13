<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 咨询方式模型
 */
class ConsultationWay extends Base
{
    protected $LogMsgParams=array('名','name','咨询方式');
    /**
     * 查询格式化后的列表
     * @return [type] [description]
     */
    public function listTreeFormat($hid)
    {
    	$map['m.status']=1;
        $map['m.hospital_id']=$hid;
    	$lis=$this->alias('m')->where($map)->order('m.weight desc')->select();
    	$tree=new \ivier\Tree();
    	return $tree->toFormatTree($lis,'name');
    }
    
    public function batchAddResult()
    {
    	$data=$this->getData();
    	$names=$data['names'];
    	unset($data['names']);
    	$count=0;
    	$nameArr=explode(',', $names);
    	foreach ($nameArr as $key => $name) {
    		$name=trim($name);
    		if(!empty($name)){
    			$data['name']=$name;
    			$this->data([]);
    			$this->allowField(true)->isUpdate(false)->save($data);
    			$id=$this->id;
    			if ($id>0) {
    				$count++;
    				model('common.OperationLog')->addLog($id,$this);
    			}
    		}
    	}
    	return RS("成功创建$count 个方式");
    }

	/**
	 * 查询所有格式化后的列表
	 * @return [type] [description]
	 */
	public function allListTreeFormat($hid)
	{
        $map['hospital_id']=$hid;
		$lis=$this->order('weight desc')->where($map)->select();
		$tree=new \ivier\Tree();
		return $tree->toFormatTree($lis,'name');
	}
	/**
	 * 查询所有但排除当前项目
	 * @param  int $current_id 当前项目编号
	 * @return [type]             [description]
	 */
	public function allListTreeFormatNotCurrent($current_id,$hid)
	{
		$map['id']=array('neq',$current_id);
        $map['hospital_id']=$hid;
		$lis=$this->order('weight desc')->where($map)->select();
		$tree=new \ivier\Tree();
		return $tree->toFormatTree($lis,'name');
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

    public function lis($hid,$pid=false)
    {
        $map=array();
        if($pid!==false){
            $map['pid']=$pid;
        }
        $map['hospital_id']=$hid;
        return $this->where($map)->select();
    }
}
