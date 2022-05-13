<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 客户信息表模型
 */
class Customer extends Base
{
	//protected $autoWriteTimestamp = false;
    protected $LogMsgParams=array('名','name','客户信息表');
}
