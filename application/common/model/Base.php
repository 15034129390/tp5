<?php
namespace app\common\model;

use think\Model;

/**
 * 基础模型类（所有model都可以基础此类）
 */
class Base extends Model
{
	//每页显示数量
	protected $pageRowsCount=20;
	protected $autoWriteTimestamp = true;
	protected $LogMsgParams=array('编号','id','');
	/**
	 * 获取文本映射数组
	 * @return [type] [description]
	 */
	public function getTextMap()
	{
		return array();
	}
	/**
	 * 是否开启操作日志记录
	 * @param int $type 操作类型
	 */
	public function IsOpenOperationLog($type)
	{
		return true;
	}

	/**
	 * 获取日志消息
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function getLogMsg($type,$data)
	{
		$msg="了".$this->LogMsgParams[0]."为【".$data[$this->LogMsgParams[1]]."】的".$this->LogMsgParams[2]."信息";
		switch ($type) {
			case '1':
			return "添加".$msg;
			case '2':
			return "修改".$msg;
			case '3':
			return "删除".$msg;
		}
	}
	public function getTableName()
	{
		return $this->name;
	}
	/**
	 * 构造方法
	 * @param array $data 查询结果数据
	 */
	function __construct($data = [])
	{
		$path=get_class($this);
		$path=str_replace("app\\common\\model\\", '', $path);
		$path=ucfirst($path);
		$path=str_replace("\\", '', $path);
		$this->name=$path;
		parent::__construct($data);
	}

	/**
	 * 设置每页显示数量
	 * @param integer $val 数量
	 */
	public function setPageRowsCount($val=15) {
		$this->pageRowsCount = $val;
	}

	/**
	 * 检查数据中是否存在某个字段
	 * @param  string $key 字段名称
	 * @return boolean     存在返回tree 不存在返回false
	 */
	public function exists_field($key)
	{
		if(array_key_exists($key, $this->data)){
			return true;
		}
		return false;
	}
	/**
	 * 添加字段数组的项目
	 * @param string $key    数组字段名称
	 * @param object &$value 数组项目
	 */
	public function add_field_array_item($key,&$value)
	{
		if(!array_key_exists($key, $this->data)){
			$this->data[$key]=array();
		}
		$this->data[$key][]=$value;
	}
	/**
	 * 添加并返回ID
	 */
	public function addResult()
	{
		$data=$this->getData();
		$this->data([]);
		$this->save($data);
		$id=$this->id;
		if ($id>0) {
			model('common.OperationLog')->addLog($id,$this);
			return RD($id);
		}else{
			return RE('失败');
		}
	}
	/**
	 * 修改并返回状态
	 * @return object Result object
	 */
	public function updateResult()
	{
		if ($this->modify()) {
			return RS('操作成功');
		}else{
			return RE('失败');
		}
	}
	/**
	 * 删除数据
	 * @param  int $id Id
	 * @return object Result object
	 */
	public function delResult($id)
	{
		$result=model('common.OperationLog')->delLog($id,$this);
		if ($result) {
			return RS('删除成功');
		}else{
			return RE('失败');
		}
	}
	public function timeWhereD($sdate,$edate,$params=false){
        $map=array();
        if($params){
            if(array_key_exists($sdate, $params)){
                $sdate=$params[$sdate];
            }else{
                $sdate='';
            }
            if(array_key_exists($edate, $params)){
                $edate=$params[$edate];
            }else{
                $edate='';
            }
        }
        if(!empty($sdate)){
            $map[]=array('egt',strtotime($sdate));
        }

        if(!empty($edate)){
            $map[]=array('lt',strtotime(substr($edate,0,10))+24*60*60);
        }
        if(count($map)==0){
            return false;
        }else if(count($map)==1){
            return $map[0];
        }
        return $map;
    }

    public function dateWhereD($sdate,$edate,$params=false)
    {
        $map=array();
        if($params){
            if(array_key_exists($sdate, $params)){
                $sdate=$params[$sdate];
            }else{
                $sdate='';
            }
            if(array_key_exists($edate, $params)){
                $edate=$params[$edate];
            }else{
                $edate='';
            }
        }
        if(!empty($sdate)){
            $map[]=array('egt',$sdate);
        }

        if(!empty($edate)){
            $map[]=array('lt',date('Y-m-d',strtotime(substr($edate, 0,10))+24*60*60));
        }
        if(count($map)==0){
            return false;
        }else if(count($map)==1){
            return $map[0];
        }
        return $map;
    }
	/**
	 * 批量删除
	 * @param  array(integer) $ids id数组
	 * @return object Result object
	 */
	public function delsResult($ids)
	{
		$result=model('common.OperationLog')->delsLog($ids,$this);
		if ($result>0) {
			return RS("成功删除$result 条数据");
		}else{
			return RE('失败');
		}
	}
	public function modify($id=0)
	{
		$data=$this->data;
		if($id==0){
			$id=$data['id'];
		}
		return model('common.OperationLog')->updateLog($id,$data,$this);
	}
}