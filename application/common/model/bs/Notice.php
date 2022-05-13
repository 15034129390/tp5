<?php
namespace app\common\model\bs;
use app\common\model\Base;
use think\helper\Time;
/**
 * 公告
 */
class Notice extends Base
{

	public function pageLis()
	{
		$lis=$this->alias('m')->join('bs_hospital h','h.id=m.hospital_id','left')
		->field('m.*,h.name')->paginate();
		return $lis;
	}
}