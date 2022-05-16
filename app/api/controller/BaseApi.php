<?php


namespace app\api\controller;



class BaseApi{

    public $params;

    public $token;

    public function __construct(){
        $this->params = input();

    }
}
