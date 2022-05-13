<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 用户表模型
 */
class User extends Base
{
	protected $autoWriteTimestamp = false;
	protected $LogMsgParams=array('名','name','用户表');
	/**
	 * 获取日志消息
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function getLogMsg($type,$data)
	{
		$info=model('common.admin')->get($data['id']);
		$msg="了登录名为【".$info['login_name']."】的用户信息";
		switch ($type) {
			case '1':
			return "添加".$msg;
			case '2':
			return "修改".$msg;
			case '3':
			return "删除".$msg;
		}
	}
	
	public function addResult()
	{
		$data=$this->getData();
		$admin=model('common.admin');
		$admin->allowField(true);
		$admin->data($data);
		$result=$admin->addResult();
		//向admin中写入数据这里调用了commonadmin里面的添加方法
		if($result->status==1){
			$this->id=$result->data['id'];
			$this->allowField(true);
			parent::addResult();//调用父级的添加方法添加user的数据
			//添加用户组
			if(isset($data['hospital_ids'])&&count($data['hospital_ids'])>0){
				$userHospitals=array();
				$userHospital['user_id']=$this->id;
				foreach ($data['hospital_ids'] as $key => $value) {
					$userHospital['hospital_id']=$value;
					$userHospitals[]=$userHospital;
				}
				$cag=new UserHospital();
				$cag->saveAll($userHospitals);//批量添加用户与hospital的关联数据
			}
		}
		return $result;
	}
	/**
     * 修改并返回状态
     * @return object Result object
     */
	public function updateResult()
	{
		$data=$this->data;
		$result=model('common.Admin')
		->data($data)->allowField(true)
		->updateResult();
		$this->allowField(true);
		$result2=parent::updateResult();
		if($result2->status==1||$result->status==1){
        	//添加用户组
			$cag=new UserHospital();
			$map['user_id']=$data['id'];
			$cag->where($map)->delete();
			if(isset($data['hospital_ids'])&&count($data['hospital_ids'])>0){
				$userHospitals=array();
				$userHospital['user_id']=$this->id;
				foreach ($data['hospital_ids'] as $key => $value) {
					$userHospital['hospital_id']=$value;
					$userHospitals[]=$userHospital;
				}
				$cag->saveAll($userHospitals);
			}
			return RS('修改成功');
		}else{
			return RE('修改失败');
		}
		return $result2;
	}
	public function pageLis($keyword,$status=0)
	{
		$map=array();
		if(!empty($keyword)){
			$map['a.name|a.mobile|a.login_name']=array('like',"%$keyword%");
		}
		if($status>0){
			$map['a.status']=$status;
		}
		$this->alias('u');
		$this->where($map)->join('common_admin a','a.id=u.id')->order('u.id desc');
		$data=$this->paginate();
		return $data;
	}
	public function detail($id)
	{
		$map['m.id']=$id;
		return $this->alias('m')->join('common_admin ca','ca.id=m.id')->where($map)->find();
	}
	public function authDeptUids($id)
	{
		$map['id']=$id;
		$sector_id=$this->where($map)->value('sector_id');
		if($sector_id){
			$where="FIND_IN_SET('$sector_id',bs.path) or m.sector_id=$sector_id";
			return $this->alias('m')->join('bs_sector bs','bs.id=m.sector_id')->where($where)->column('m.id');
		}
		return false;
	}
}
