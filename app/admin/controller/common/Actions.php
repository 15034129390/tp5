<?php
namespace app\admin\controller\common;
use app\admin\controller\Base;
use think\Request;

class Actions extends  Base
{
    public function index()
    {
        $this->lis=$this->model->listTreeFormat();
        return view();
    }
    public function create()
    {
        $this->lis=$this->model->listGroupTreeFormat();
        return view();
    }
    public function edit($id)
    {
        $this->info=$this->model->get($id);
        $this->lis =$this->model->listGroupTreeFormat();
        return view();
    }

    private function _refreshAction($fileName,$path,$dir='')
    {

        $ar=explode(".", $fileName);
        if(count($ar)==1){
            //文件夹
            $filesnames = scandir($path.'/'.$fileName);
            foreach ($filesnames as $key => $value) {
                if($value != "." && $value != ".."){
                    $_dir='';
                    if(empty($dir)){
                        $_dir=$fileName;
                    }else{
                        $_dir=$dir.'.'.$fileName;
                    }
                    $this->_refreshAction($value,$path.'/'.$fileName,$_dir);
                }
            }
        }else{
            if($ar[1]=='php'){
                $model=$this->model;
                if(in_array($ar[0], array('Base','Common'))){
                    return;
                }
                $content=file_get_contents($path."/".$fileName);
                preg_match_all("/public\sfunction\s(.*?)\(/",$content,$mat);
                $code=lcfirst($ar[0]);
                if(!empty($dir)){
                    $code=$dir.'.'.$code;
                }
                $map['code']=$code;
                $info=$model->where($map)->find();
                if(!$info){
                    $data['code']=$code;
                    $data['pid']=0;
                    $data['is_group']=1;
                    $data['name']=$code;
                    $id=db('CommonActions')->insertGetId($data);
                    echo $code."<br/>";
                }else{
                    $id=$info['id'];
                }
                $actions=$mat[1];
                $actions[]="delete";
                foreach ($actions as $key => $value) {
                    $map['code']=$code.'/'.strtolower($value);
                    $c=$model->where($map)->count();
                    if($c==0){
                        $msg=$code.'/'.$value;
                        if($value=='index'){
                            $msg="列表";
                        }else if($value=='edit'){
                            $msg="修改";
                        }
                        else if($value=='create'){
                            $msg="添加";
                        }else if($value=='delete'){
                            $msg="删除";
                        }
                        else if($value=='disable'){
                            $msg="禁用";
                        }
                        else if($value=='recovery'){
                            $msg="恢复";
                        }
                        else if($value=='batch_create'){
                            $msg="批量添加";
                        }
                        else if($value=='save'||$value=='update'){
                            continue;
                        }
                        // else if($value=='read'){
                        //     $msg="查看";
                        // }
                        $data['code']=$map['code'];
                        $data['pid']=$id;
                        $data['is_group']=0;
                        $data['name']=$msg;
                        $data['remark']=$msg;
                        db('CommonActions')->insert($data);
                        echo "--".$msg."<br/>";
                    }
                }
            }
        }
    }

    public function refreshAction()
    {
        $path=APP_PATH.'admin/controller';
        $filesnames = scandir($path);
        foreach ($filesnames as $key => $value) {
            if($value != "." && $value != ".."){
                $this->_refreshAction($value,$path,'');
            }
        }
        echo "更新完成<br/>";
    }
}
