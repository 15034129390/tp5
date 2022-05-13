<?php
namespace app\admin\controller\bs;
//引入base控制器
use app\admin\controller\Base;
use think\helper\Time;
/**
 * 预约信息表控制器
 * 
 */
class Booking extends  Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
      $this->zx_dateStart=input('zx_dateStart');
      $this->zx_dateEnd=input('zx_dateEnd');

      $this->yy_dateStart=input('yy_dateStart');
      $this->yy_dateEnd=input('yy_dateEnd');

      $this->dz_dateStart=input('dz_dateStart');
      $this->dz_dateEnd=input('dz_dateEnd');

        $ft=input('ft');//时间类型
        if($ft){
            //今天
          if($ft=='today')
          {
            list($start, $end) = Time::today();
          }
          else if($ft=='yday')
          {
                //昨天
            list($start, $end) = Time::yesterday();
          }
          else if($ft=='week')
          {
                //本周
            list($start, $end) = Time::week();
          }

          else if($ft=='lweek')
          {
                //上周
            list($start, $end) = Time::lastWeek();
          }
          else if($ft=='month')
          {
                //本月
            list($start, $end) = Time::month();
          }
          else if($ft=='lmonth')
          {
            //上月
            list($start, $end) = Time::lastMonth();
          }

          $start=date('Y-m-d',$start);
          $end=date('Y-m-d',$end);
            $ty=input('sltDateType');//到院 ，登记
            if($ty=="2"){
              $this->yy_dateStart=$start;
              $this->yy_dateEnd=$end;
            }else if($ty=='1'){
              //咨询
              $this->zx_dateStart=$start;
              $this->zx_dateEnd=$end;
            }else{ 
              //到院
              $this->dz_dateStart=$start;
              $this->dz_dateEnd=$end;
            }
            $this->sltDateType=$ty;
          }else{
           $this->sltDateType=0;
         }

         return view();
       }
       public function form()
       {
           var_dump('aa');die;
        $op=input('get.op','0');
        if($op=='0'){
          $this->pageInfo=$this->model->search(input('get.'));
          return view();
        }else{
          //导出
          parent::checkAuth('bs.booking/export');
          $lis=$this->model->search(input('get.'));
          $this->_export($lis,'预约数据');
          die();
        }
      }
        /**
         * @return [type] [description]
         */
        public function self_booking()
        {
          $this->pageInfo=$this->model->selfBookingSearch(input('get.'));
          return view();
        }
        
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
      return view();
    }
    /**
     * 更新提交方法  
     * @param  int  $id
     * @return \think\Response
     */
  public function update($id)
  {
    if(IS_POST){
      $this->model->data($this->request->post());
      $this->model->id=$id;
      $zjtime=input('post.jz_time');
      $this->model->jz_time=strtotime($zjtime);
      $result=$this->model->updateResult();
      return json($result);
    }
  }

    /**
     * 显示编辑表单页.
     * 
     * @return \think\Response
     */
    public function edit($id)
    {
      $this->info=$this->model->simple($id);
      return view();
    }
    public function sms($id,$tel)
    {
      $this->tel=$tel;
      $this->id=$tel;
      return view();
    }
    public function visit()
    {
      if(IS_POST){
        $model=model('bs.visit');
        $model->data($this->request->post());
        $model->user_id=ADMIN_ID;
        $result= $model->addResult();
        return json($result);
      }
    }
    /**
     * 检测手机号是否存在
     * @return [type] [description]
     */
    public function mcheck()
    {
      if(IS_POST){
        $maa=input('mobile');
        $map['mobile']=$maa;
        $model=model('bs.customer')->where($map)->select();
        if(!$model==''){
          return json($model);
        }
      }
    }
    /**
     * 到诊
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function arriving($id)
    {
      if(IS_POST){
        $r=$this->model->arriving($id);
        return json($r);
      }
    }
    public function receive($id)
    {
      if(IS_POST){
        $r=$this->model->receive($id);
        return json($r);
      }
    }
    private function _export($lis,$title){
      \think\Loader::import('ivier.PHPExcel');
      $resultPHPExcel =new \PHPExcel();
        //设置参数
        //设值
      $sheet=$resultPHPExcel->getActiveSheet();
        //标题
      $sheet->setTitle($title);
      //abcdefghijklmnopqrstuvwxyz
      $sheet->setCellValue('A1', '预约号/(老预约号)');
      $sheet->setCellValue('B1', '姓名');
      $sheet->setCellValue('C1', '咨询人性别');
      $sheet->setCellValue('D1', '使用时间');
      $sheet->setCellValue('E1', '电话');
      $sheet->setCellValue('F1', '品牌');
      $sheet->setCellValue('G1', '故障类别');
      // $sheet->setCellValue('H1', '区域');
      $sheet->setCellValue('H1', '所属省份');
      $sheet->setCellValue('I1', '所在市区');
      $sheet->setCellValue('J1', '来源媒体');
      $sheet->setCellValue('K1', '预约方式');
      $sheet->setCellValue('L1', '客服');
      $sheet->setCellValue('M1', '登记时间');
      $sheet->setCellValue('N1', '预约到店时间');
      $sheet->setCellValue('O1', '到店时间'); 
      $sheet->setCellValue('P1', '状态'); 
      $sheet->setCellValue('Q1', '会员');
      $sheet->setCellValue('R1', '回访');
      $sheet->setCellValue('S1', '回访时间');
      $i = 2;
      foreach($lis as $item){
        $sheet->setCellValue('A' . $i, $item['id'].'('.$item['old_id'].')');
        $sheet->setCellValue('B' . $i, $item['name']);
        if($item['gender']=='1'){
          $sheet->setCellValue('C' . $i, '男');
        }else if($item['gender']=='2'){
          $sheet->setCellValue('C' . $i, '女');
        }else{
          $sheet->setCellValue('C' . $i, '保密');
        }
        $sheet->setCellValue('D' . $i, def($item['age'],'未知'));
        $sheet->setCellValue('E' . $i, def($item['mobile'],'未知'));

        $sheet->setCellValue('F' . $i, def($item['department_name'],'无'));
        $sheet->setCellValue('G' . $i, def($item['disease_name'],'无'));
        // $sheet->setCellValue('H' . $i, def($item['yy_doctor_name'],'无'));
        $sheet->setCellValue('H' . $i, def($item['sf_name'],'无'));
        $sheet->setCellValue('I' . $i, def($item['sq_name'],'无'));
        $sheet->setCellValue('J' . $i, $item['way_name']);
        $sheet->setCellValue('K' . $i, def($item['way_namea'],'无'));
        $sheet->setCellValue('L' . $i,def($item['custom_service_name'],'无'));
        $sheet->setCellValue('M' . $i, date('Y-m-d H:i',$item['create_time']));
        $sheet->setCellValue('N' . $i, $item['yy_time']);
        if($item['status']==3){
          $sheet->setCellValue('O' . $i, date('Y-m-d H:i',$item['jz_time']));
          $sheet->setCellValue('P' . $i, '已到');

        }else if($item['status']==2){
          $sheet->setCellValue('P' . $i, '预约');
          $sheet->setCellValue('O' . $i, '未到');
        }else{
          $sheet->setCellValue('P' . $i, '登记');
          $sheet->setCellValue('O' . $i, '未到');
        }
        $sheet->setCellValue('Q' . $i, def($item['member_name'],'无').'('.def($item['card_no'],'无').')');
        
        // $sheet->setCellValue('S' . $i, date('Y-m-d H:i',$lv['create_time']));
        $i++;
      }
      //设置导出文件名
      $outFileName =$title.".xls";
      \think\Loader::import('ivier.PHPExcel.PHPExcel_Writer_Excel5');
      $xlsWriter = new \PHPExcel_Writer_Excel5($resultPHPExcel);
      header("Content-Type: app/force-download");
      header("Content-Type: app/octet-stream");
      header("Content-Type: app/download");
      header('Content-Disposition:inline;filename="'.$outFileName.'"');
      header("Content-Transfer-Encoding: binary");
      header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Pragma: no-cache");
      $xlsWriter->save('php://output');
    }
  }
