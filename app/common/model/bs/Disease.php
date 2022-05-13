<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 病种模型
 */
class Disease extends Base
{
	protected $LogMsgParams=array('名','name','病种');
    /**
     * 查询格式化后的列表
     * @return [type] [description]
     */
    public function listTreeFormat($hospital_id=0)
    {
    	$map['m.status']=1;
        if($hospital_id>0){
            $map['gl.hospital_id']=$hospital_id;
        }
    	$lis=$this->alias('m')->join('bs_hospital_disease gl','gl.disease_id=m.id','left')
        ->where($map)->order('m.weight desc')->select();
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
    	return RS("成功创建$count 个病种");
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

    /**
     * 查询所有来源标记
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function lisMark($hospital_id)
    {
    	return $this->alias('m')
    	->join('bs_hospital_disease hd ',' hd.disease_id=m.id and hd.hospital_id='.$hospital_id,'left')
    	->field('m.id,m.name,m.pid,case when hd.disease_id is null then 0 else 1 end checked')
    	->select();
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
