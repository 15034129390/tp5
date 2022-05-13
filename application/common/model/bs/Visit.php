<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 来源信息模型
 */
class Visit extends Base
{
	protected $LogMsgParams=array('回访内容','content','回访信息');
	/**
     * 设置状态
     * @param  int $id 用户编号
     * @param  int $state  用户状态
     * @return [type]         [description]
     */
	public function updateStatus($id, $state) {
		$data['id']     = $id;
		$data['status'] = $state;
        $this->data($data);
        $this->modify();
        return RS("设置状态成功");
    }

    /**
     * 禁用
     * @param [type] $id 用户编号
     */
    public function disable($id) {
    	return $this->updateStatus($id, 2);
    }

    /**
     * 恢复
     * @param  [type] $id [description]
     * @return [type]          [description]
     */
    public function recovery($id) {
    	return $this->updateStatus($id, 1);
    }
}