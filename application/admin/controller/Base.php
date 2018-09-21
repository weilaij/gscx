<?php 
namespace app\admin\controller;
use think\Session;
//namespace phpexcel;
use think\Controller;
class Base extends Controller
{
	 protected function _initialize()
    {
        parent::_initialize();

        define('USER_ID', Session::has('user_id') ? Session::get('user_id'):null);
    }
	 protected function isLogin()
    {
        if (is_null(USER_ID)) {
            $this -> error('用户未登陆,无权访问',url('user/login'));
        }
    }

    //防止用户重复登陆,放在登陆操作前面:user/login
    protected function alreadyLogin(){
        if (USER_ID) {
            $this -> error('用户已经登陆,请勿重复登陆',url('index/index'));
        }
    }
   
}



 ?>