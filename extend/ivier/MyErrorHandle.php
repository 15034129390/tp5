<?php 
namespace ivier;
/**
 * 生成多层树状下拉选框的工具模型
 */
class MyErrorHandle extends \think\exception\Handle{
	/**
     * Render an exception into an HTTP response.
     *
     * @param  \Exception $e
     * @return Response
     */
	public function render(\Exception $e)
	{
		Header("Location:/"); 
	}

}