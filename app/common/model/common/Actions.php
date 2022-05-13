<?php
namespace app\common\model\common;
use app\common\model\Base;
/**
 * 权限项目控制器
 */
class Actions extends Base
{
	protected $LogMsgParams=array('编码','code','权限');
    public function listTreeFormat()
	{
		$lis=$this->select();
		$tree=new \ivier\Tree();
		return $tree->toFormatTree($lis,'name');
	}
	public function listGroupTreeFormat()
	{
		$map['is_group']=1;
		$lis=$this->where($map)->select();
		$tree=new \ivier\Tree();
		return $tree->toFormatTree($lis,'name');
	}
	public function listByAdminId($adminId)
	{
		$map['ug.admin_id']=$adminId;
        $codes=model('common.admin_group')->alias('ug')->where($map)
        ->join('common_group_action ga',' ga.group_id=ug.group_id')
        ->join('common_actions a ',' a.id=ga.action_id')
        ->distinct('code')
        ->column('code');
        return $codes;
	}
}
