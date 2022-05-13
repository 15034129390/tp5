<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 咨询方式模型
 */
class Sector extends Base
{
    protected $LogMsgParams=array('名','name','部门(组)');
    /**
     * 查询格式化后的列表
     * @return [type] [description]
     */
    public function listTreeFormat()
    {
    	$map['m.status']=1;
    	$lis=$this->alias('m')->where($map)->order('m.weight desc')->select();
    	$tree=new \ivier\Tree();
    	return $tree->toFormatTree($lis,'name');
    }
    public function addResult()
    {
        $data=$this->getData();
        if($data['pid']>0){

            $map['id']=$data['pid'];
            $data['path']=$this->where($map)->value('path').','.$data['pid'];
        }else{
            $data['path']='0';
        }
        $this->data($data);
        $this->isUpdate(false);
        $result=parent::addResult();
        return $result;
    }
    /**
     * 修改并返回状态
     * @return object Result object
     */
    public function updateResult()
    {
        $data=$this->getData();
        if($data['pid']>0){
            $map['id']=$data['pid'];
            $data['path']=$this->where($map)->value('path').','.$data['pid'];
        }else{
            $data['path']='0';
        }
        $this->data($data);
        $this->allowField(true);
        return parent::updateResult();
    }
    public function batchAddResult()
    {
    	$data=$this->getData();
    	$names=$data['names'];
        if($data['pid']>0){
            $map['id']=$data['pid'];
            $data['path']=$this->where($map)->value('path').','.$data['pid'];
        }else{
            $data['path']='0';
        }
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
    return RS("成功创建$count 个部门");
}

	/**
	 * 查询所有格式化后的列表
	 * @return [type] [description]
	 */
	public function allListTreeFormat()
	{
		$lis=$this->order('weight desc')->select();
		$tree=new \ivier\Tree();
		return $tree->toFormatTree($lis,'name');
	}
	/**
	 * 查询所有但排除当前项目
	 * @param  int $current_id 当前项目编号
	 * @return [type]             [description]
	 */
	public function allListTreeFormatNotCurrent($current_id)
	{
		$map['id']=array('neq',$current_id);
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

    
}
