<?php
namespace app\common\taglib;

use think\template\TagLib;

/**
 * ivier 标签库解析类
 * @category   Think
 * @package  Think
 * @subpackage  Driver.Taglib
 * @author    liu21st <liu21st@gmail.com>
 */
class ivier extends Taglib
{
	 // 标签定义
	protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
		'auth'        => ['attr' => 'value,action']
	];
	public function tagAuth($tag,$content){
		
		$value='';
		if(!array_key_exists('value', $tag)){
			$value=request()->controller()."/".$tag['action'];
		}else{
			$value=$tag['value'];
		}
		$parseStr  = '<?php if(checkAuth("' . $value . '")): ?>' . $content . '<?php endif; ?>';
		return $parseStr;
	}
}