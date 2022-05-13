<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 城市
 */
class Area extends Base
{
	protected $autoWriteTimestamp = false;
	public function lis($pid=0)
	{
		$map=array();
		if($pid!==false){
			$map['pid']=$pid;
		}
		return $this->where($map)->select();
	}
}