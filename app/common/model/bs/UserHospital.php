<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 医院用户关联表模型
 */
class UserHospital extends Base
{
	protected $autoWriteTimestamp = false;
    protected $LogMsgParams=array('名','name','医院用户关联表');
}
