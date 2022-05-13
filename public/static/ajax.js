var BS_Ajax={};//定义对象
;(function(bs){
	var baseUrl='/bs.ajax/';
	var errorCount=0;
	var initObj={};
	/**
	 * 获取医生
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.doctor=function($slt,lh,succ){
	 	_ajax(baseUrl+'doctor',$slt,lh,succ);
	 }
	/**
	 * 获取科室
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.department=function($slt,lh,succ){
	 	_ajax(baseUrl+'department',$slt,lh,succ);
	 }
	/**
	 * 获取病种
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.disease=function($slt,depid,lh,succ){
	 	_ajax(baseUrl+'disease?depid='+depid,$slt,lh,succ);
	 }
	/**
	 * 科室病种
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.depDisease=function($depSlt,$disSlt,lh){
	 	$depSlt.change(function() {
	 		bs.disease($disSlt,$depSlt.val(),lh);
	 	});
	 	bs.department($depSlt,lh,function(){
	 		$depSlt.change();
	 	});
	 }
	/**
	 * 获取来源
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.source=function($slt,lh,succ){
	 	_ajax(baseUrl+'source',$slt,lh,succ);
	 }
	 /**
	 * 预约方式
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.consultationWay=function($slt,lh,succ){
	 	_ajax(baseUrl+'consultationWay',$slt,lh,succ);
	 }
	 /**
	 * 预约方式2
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.consultationWay1=function($slt,depid,lh,succ){
	 	_ajax(baseUrl+'consultationWay1?depid='+depid,$slt,lh,succ);
	 }
	 /**
	 * 预约
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.depconsultationWay=function($depSlt,$disSlt,lh){
	 	$depSlt.change(function() {
	 		bs.consultationWay1($disSlt,$depSlt.val(),lh);
	 	});
	 	bs.consultationWay($depSlt,lh,function(){
	 		$depSlt.change();
	 	});
	 }
	  /**
	 * 来院方式
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.comeWay=function($slt,lh,succ){
	 	_ajax(baseUrl+'comeWay',$slt,lh,succ);
	 }
	 /**
	 * 会员等级
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]} 无
	 */
	 bs.memberLevel=function($slt,lh,succ){
	 	_ajax(baseUrl+'memberLevel',$slt,lh,succ);
	 }


	/**
	 * 获取客服
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.customService=function($slt,lh,succ){
	 	_ajax(baseUrl+'customService',$slt,lh,succ);
	 }

	/**
	 * 获取客服
	 * @param  {[type]} $slt 下拉列表Jquery对象
	 * @param  {[type]} lh   是否保留第一个选项
	 * @param  {[type]} succ 成功回调方法
	 * @return {[type]}     无
	 */
	 bs.site=function($slt,lh,succ){
	 	_ajax(baseUrl+'site',$slt,lh,succ,initObj.site);
	 }

	 var _ajax=function(url,$slt,lh,succ,noDefault){
	 	$.ajax({
	 		url: url,
	 		success:function (result) {
	 			if(lh){
	 				$slt.find('option:gt(0)').remove();
	 			}else{
	 				$slt.find('option').remove();
	 			}
	 			$.each(result.data,function(index, el) {
	 				var text='title_show' in el ?el.title_show:el.name
	 				$slt.append('<option value="'+el.id+'">'+text+'</option>');
	 			});
	 			if(!noDefault){
	 				var did=$slt.attr('did');
	 				if(did){
	 					$slt.val(did);
	 				}
	 				noDefault=true;
	 			}
	 			if(succ){
	 				succ($slt,result.data);
	 			}
	 		}
	 	})
	 	.done(function() {
	 		console.log("success");
	 	})
	 	.fail(function() {
	 		errorCount++;
			//出现错误继续访问
			if(errorCount<10){
				_ajax(url,$slt,lh,succ);
			}
		})
	 	.always(function() {
			//结束
		});
	 }
	}(BS_Ajax));