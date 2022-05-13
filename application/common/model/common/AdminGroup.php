<?php

namespace app\common\model\common;
use app\common\model\Base;
/**
 * 管理员角色关联
 */
class AdminGroup extends Base
{
	protected $autoWriteTimestamp = false;
    protected $LogMsgParams=array('编号','id','管理员角色关联');
}
