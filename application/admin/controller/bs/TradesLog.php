<?php
namespace app\admin\controller\bs;
//引入base控制器
use app\admin\controller\Base;
/**
 * 会员信息
 * 
 */
class TradesLog extends  Base
{
	//充值记录
	public function index()
	{  
		$qt=input('get.qt','p');
		if($qt=='ex'){
			//导出
			$this->exlog();
		}else{
			$pageInfo=$this->model->pageLis(input('get.mid'),input('get.kw'),input('get.sdate'),input('get.edate'),2);
			$this->pageInfo=$pageInfo;
			$this->tj=$this->model->pageLisTj(input('get.mid'),input('get.kw'),input('get.sdate'),input('get.edate'),2);
			return view();
		}
		
	}

	/**
	 * 消费
	 * @return [type] [description]
	 */
	public function charge()
	{
		if(IS_POST){
			
		}
		return view('charge1');
	}
	/**
	 * 消费记录
	 * @return [type] [description]
	 */
	public function chargeLog()
	{
		$qt=input('get.qt','p');
		if($qt=='ex'){
			//导出
			$this->exCrgLog();
		}else{
			$pageInfo=$this->model->pageLis(input('get.mid'),input('get.kw'),input('get.sdate'),input('get.edate'),1);
			$this->pageInfo=$pageInfo;
			$this->tj=$this->model->pageLisTj(input('get.mid'),input('get.kw'),input('get.sdate'),input('get.edate'),1);
			return view();# code...
		}
	}
	/**
	 * 导出消费记录
	 * @return [type] [description]
	 */
	public function exCrgLog()
	{
		$lis=$this->model->exLis(input('get.mid'),input('get.kw'),input('get.sdate'),input('get.edate'),1);
		$tj=$this->model->pageLisTj(input('get.mid'),input('get.kw'),input('get.sdate'),input('get.edate'),1);
		if(count($lis)>0){
			$this->_export($lis,$tj,'消费记录');
		}else{
			echo "暂无数据";
		}
		die();
	}
	/**
	 * 导出日志
	 * @return [type] [description]
	 */
	public function exlog()
	{
		$lis=$this->model->exLis(input('get.mid'),input('get.kw'),input('get.sdate'),input('get.edate'),2);
		$tj=$this->model->pageLisTj(input('get.mid'),input('get.kw'),input('get.sdate'),input('get.edate'),2);
		if(count($lis)>0){
			$this->_export($lis,$tj,'充值记录');
		}else{
			echo "暂无数据";	
		}
		die();
		
	}
	public function _export($lis,$tj,$title){
		\think\Loader::import('ivier.PHPExcel');
		$resultPHPExcel =new \PHPExcel();
        //设置参数
        //设值
		$sheet=$resultPHPExcel->getActiveSheet();
        //标题
		$sheet->setTitle($title);
		$sheet->setCellValue('A1', '会员卡号');
		$sheet->setCellValue('B1', '会员姓名');
		$sheet->setCellValue('C1', '日志类型');
		$sheet->setCellValue('D1', '通用金额');
		$sheet->setCellValue('E1', '门诊金额');
		$sheet->setCellValue('F1', '住院金额');
		$sheet->setCellValue('G1', '现金金额');//abcdefghijklmnopqrstuvwxyz
		$sheet->setCellValue('H1', '操作时间');
		$sheet->setCellValue('I1', '操作单位');
		$sheet->setCellValue('J1', '备注');
		$i = 2;
		foreach($lis as $item){
			$sheet->setCellValue('A' . $i, $item['card_no']);
			$sheet->setCellValue('B' . $i, $item['name']);
			$dtx='支出';
			if($item['direction']=='2'){
				$dtx='充值';
			}
			$sheet->setCellValue('C' . $i, $dtx);
			$sheet->setCellValue('D' . $i, def($item['money'],'0.00'));
			$sheet->setCellValue('E' . $i, def($item['mz_money'],'0.00'));
			$sheet->setCellValue('F' . $i, def($item['zy_money'],'0.00'));
			$sheet->setCellValue('G' . $i, def($item['cash_money'],'0.00'));
			$sheet->setCellValue('H' . $i, date('Y-m-d H:i:s',$item['create_time']));
			$sheet->setCellValue('I' . $i, $item['hospital_name']);
			$sheet->setCellValue('J' . $i, $item['memo']);
			$i++;
		}
		$sheet->setCellValue('A' . $i,'总计');
		$sheet->setCellValue('B' . $i, '');
		$sheet->setCellValue('C' . $i, '');
		$sheet->setCellValue('D' . $i, def($tj['money'],'0.00'));
		$sheet->setCellValue('E' . $i, def($tj['mz_money'],'0.00'));
		$sheet->setCellValue('F' . $i, def($tj['zy_money'],'0.00'));
		$sheet->setCellValue('G' . $i, def($tj['cash_money'],'0.00'));
		$sheet->setCellValue('H' . $i, '');
		$sheet->setCellValue('I' . $i, '');
		$sheet->setCellValue('J' . $i, '');

        //设置导出文件名
		$outFileName =$title.".xls";
		\think\Loader::import('ivier.PHPExcel.PHPExcel_Writer_Excel5');
		$xlsWriter = new \PHPExcel_Writer_Excel5($resultPHPExcel);
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header('Content-Disposition:inline;filename="'.$outFileName.'"');
		header("Content-Transfer-Encoding: binary");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");
		$xlsWriter->save('php://output');

	}
}