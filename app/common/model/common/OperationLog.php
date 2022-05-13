<?php
namespace app\common\model\common;
use app\common\model\Base;
class OperationLog extends Base
{
	protected $autoWriteTimestamp = false;
	//定义日志类型
	static public $_log_type=array(
		'1'=>'添加',
		'2'=>'修改',
		'3'=>'删除'
		);
	/**
	 * type_text获取器
	 * @param  [type] $value [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	public function getTypeTextAttr($value,$data)
	{
		return self::$_log_type[$data['type']];
	}


    /**
     * 返回所有类型
     * @return [type] [description]
     */
    public function getTypes()
    {
    	return self::$_log_type;
    }

    public function pageLis($keyword='',$sdate='',$edate='',$table=false,$main_id=0)
    {
    	$map=array();
    	if(!empty($keyword)){
    		$map['m.main_id|m.table|u.name']=array('like',"%$keyword%");
    	}
    	if(!empty($sdate)){
    		$sdate=strtotime($sdate);
    	}else{
    		$sdate=0;
    	}
    	if(!empty($edate)){
    		$edate=(strtotime($edate)+24*60*60);
    	}else{
    		$edate=time();
    	}
    	if($table){
    		$map['m.table']=$table;
    	}
    	if($main_id){
    		$map['m.main_id']=$main_id;
    	}
    	$this->whereTime('m.created', 'between', [$sdate, $edate]);
    	$this->alias('m')->join('common_admin a','a.id=m.admin_id','left')
    	->field('m.*,a.name');
    	$p=$this->order('m.id desc')->where($map)->paginate();
    	return $p;
    }

    public function detail($id)
    {
    	$m=$this;
    	$m->alias('m');
    	$m->join('common_admin a ',' a.id=m.admin_id','left');
    	$m->field("m.*,a.name");
    	$where['m.id']=$id;
    	return $m->where($where)->find();
    }
	/**
	 * 添加日志记录
	 * @param [type] $id    [description]
	 * @param [type] $data  [description]
	 * @param [type] $table [description]
	 */
	public function addLog($id,$_model)
	{
		if($_model->IsOpenOperationLog(1)){
			$objData=$_model->find($id);
			$data=$this->_initLogData(1);
			$data['table']=$_model->getTableName();
			$data['msg']=$_model->getLogMsg(1,$objData);
			$data['main_id']=$id;
			$data['log']=json_encode($objData);
			$this->insert($data);
		}
	}
	private function _initLogData($type)
	{
		if(defined('ADMIN_ID')){
			$data['admin_id']=ADMIN_ID;
		}
		else if(defined('MID')){
			$data['admin_id']=MID;
		}
		$data['created']=time();
		$data['type']=$type;
		$data['ip']=request()->ip();
		$data['ip_address']=get_ip_address($data['ip']);
		return $data;
	}
	
	public function delLog($id,$_model)
	{
		$objData=$_model->find($id);
		if($objData){
			$count=$objData->delete();
			if($count>0){
				if($_model->IsOpenOperationLog(3)){
					$data=$this->_initLogData(3);
					$data['table']=$_model->getTableName();
					$data['msg']=$_model->getLogMsg(3,$objData);
					$data['main_id']=$id;
					$data['log']=json_encode($objData);
					$this->insert($data);
				}
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 批量删除日志记录
	 * @param  [type] $ids    [description]
	 * @param  [type] $_model [description]
	 * @return [type]         [description]
	 */
	public function delsLog($ids,$_model)
	{
		$successCount=0;
		if(is_array($ids)){
			foreach ($ids as $key => $id) {
				if($this->delLog($id,$_model)){
					$successCount++;
				}
			}
		}
		return $successCount;
	}
	public function updateLog($id,$updataData,$_model)
	{
		$objData=$_model->find($id);
		if($objData){
			$oldData=$objData->getData();
			$_model->data([]);
			$count=$_model->save($updataData,['id' => $id]);
			if($count>0){
				if($_model->IsOpenOperationLog(2)){
					$newData=$_model->find($id)->getData();
					$diff=array();
					foreach ($oldData as $key => $value) {
						if($oldData[$key]!=$newData[$key]){
							$diff[$key]=array($oldData[$key],$newData[$key]);
						}
					}
					$data=$this->_initLogData(2);
					$data['table']=$_model->getTableName();
					$data['msg']=$_model->getLogMsg(2,$objData);
					$data['main_id']=$id;
					$data['log']=json_encode($diff);
					$this->insert($data);
				}
				return true;
			}
		}
		return false;
	}
	public function modifysLog($ids,$updataData,$_model)
	{
		$successCount=0;
		if(is_array($ids)){
			foreach ($ids as $key => $id) {
				if($this->updateLog($id,$updataData,$_model)){
					$successCount++;
				}
			}
		}
		return $successCount;
	}
	/**
	 * 删除数据
	 * @param  int $id Id
	 * @return object Result object
	 */
	public function delResult($id)
	{
		$result=$this->db()->delete($id);
		if ($result) {
			return RS('删除成功');
		}else{
			return RE('失败');
		}
	}
	/**
	 * 批量删除
	 * @param  array(integer) $ids id数组
	 * @return object Result object
	 */
	public function delsResult($ids)
	{
		$map['id']=array('in',$ids);
		$result=$this->db()->delete($map);
		if ($result) {
			return RS('删除成功');
		}else{
			return RE('失败');
		}
		if ($result>0) {
			return RS("成功删除$result条数据");
		}else{
			return RE('失败');
		}
	}
}
