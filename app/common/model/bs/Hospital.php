<?php
namespace app\common\model\bs;
//引入Base
use app\common\model\Base;

/**
 * 医院模型
 */
class Hospital extends Base
{
    protected $LogMsgParams=array('名','name','医院');
    public function lis()
    {
    	$map['status']=1;
    	return $this->where($map)->order('weight desc')->select();
    }
    public function allList()
    {
    	return $this->order('weight desc')->select();
    }

    public function listAllByUserId($userid)
    {
        $map['h.status']=1;
        return $this->alias('h')
        ->join('bs_user_hospital uh ',' uh.hospital_id=h.id and uh.user_id='.$userid,'left')
        ->field('h.id,h.name,case when uh.hospital_id is null then 0 else 1 end checked')
        ->where($map)
        ->select();
    }
    public function lisByUserId($userid)
    {
        if($userid==1){
            $map['h.status']=1;
            return $this->alias('h')
           ->field('h.id,h.name')
            ->where($map)
            ->select();
        }else{
            $map['h.status']=1;
            return $this->alias('h')
            ->join('bs_user_hospital uh ',' uh.hospital_id=h.id and uh.user_id='.$userid)
            ->field('h.id,h.name')
            ->where($map)
            ->select();
        }
    }
    public function firstIds($userid)
    {
        if($userid==1){
            $map['h.status']=1;
            return $this->alias('h')
            ->where($map)
            ->column('id');
        }else{
            $map['h.status']=1;
            return $this->alias('h')
            ->join('bs_user_hospital uh ',' uh.hospital_id=h.id and uh.user_id='.$userid)
            ->where($map)
            ->column('id');
        }
    }
    /**
     * 用户
     * @return [type] [description]
     */
    public function listUser()
    {
        //状态啊状态
        $map['m.status']=1;
        //用户限制
        $query=$this->alias('m');
        return $query->where($map)->order('m.weight desc')->select();
    }
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
