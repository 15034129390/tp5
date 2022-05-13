<?php
namespace app\admin\controller\bs;
use app\admin\controller\Base;
/**
 * ajax数据请求控制器
 * 
 */
class Ajax extends  Base
{
	protected $isCRUD=false;
	/**
	 * 获取医生
	 * @return [type] [description]
	 */
	public function doctor()
	{
		$lis=model('common.admin')->doctor(HID);
		return json(RD($lis));
	}
	/**
	 * 科室
	 * @return [type] [description]
	 */
	public function department()
	{
		$lis=model('bs.disease')->lis(HID,0);
		return json(RD($lis));
	}
	/**
	 * 病种
	 * @return [type] [description]
	 */
	public function disease($depid)
	{
		if($depid>0){
			$lis=model('bs.disease')->lis(HID,$depid);
		}else{
			$lis=array();
		}
		return json(RD($lis));
	}
	/**
	 * 保存站点
	 * @param  [type] $site [description]
	 * @return [type]       [description]
	 */
	public function saveSite($site)
	{
		$r=model('bs.site')->saveSite(HID,$site);
		return json(RD($r));
	}
	/**
	 * 保存来院
	 * @param  [type] $site [description]
	 * @return [type]       [description]
	 */
	public function saveSource($site)
	{
		$r=model('bs.source')->saveSource(HID,$site);
		return json(RD($r));
	}
	
	/**
	 * 来源
	 * @return [type] [description]
	 */
	public function source()
	{
		$lis=model('bs.source')->lis(HID);
		return json(RD($lis));
	}
	/**
	 * 客服
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function customService()
	{
		$lis=model('common.admin')->consult(HID);
		return json(RD($lis));
	}
	/**
	 * 站点
	 */
	public function site()
	{
		$lis=model('bs.site')->lis(HID);
		return json(RD($lis));
	}
	/**
	 * 咨询方式
	 * @return [type] [description]
	 */
	public function consultationWay()
	{
		$lis=model('bs.consultationWay')->lis(HID,0);
		return json(RD($lis));
	}
	/**
	 * 咨询方式2
	 * @return [type] [description]
	 */
	public function consultationWay1($depid)
	{
		// file_put_contents('1.text', $depid);
		if($depid>0){
			$lis=model('bs.consultationWay')->lis(HID,$depid);
		}else{
			$lis=array();
		}
		return json(RD($lis));
	}
	/**
	 * 来院方式
	 * @return [type] [description]
	 */
	public function comeWay()
	{
		$lis=model('bs.comeWay')->lis(HID);
		return json(RD($lis));
	}
	/**
	 * 会员等级
	 * @return [type] [description]
	 */
	public function memberLevel()
	{
		$lis=model('bs.memberLevel')->lis();
		return json(RD($lis));
	}
	
	/**
	 * 搜索会员
	 * @return [type] [description]
	 */
	public function searchMember()
	{
		//搜索会员
		//
		//消息
		
		
	}
	
}