<?php 
namespace app\index\controller;
use think\Controller;
use think\Session;
class Base extends Controller
{
	 protected function _initialize()
    {
        parent::_initialize();

        define('USER_ID', Session::has('student_id') ? Session::get('student_id'):null);
    }
     protected function isAuth()
    {
        if (is_null(USER_ID)) {
            $this -> error('用户未进行易班授权,无权访问',url('index/yblogin'));
        }
    }
   
}



 ?>