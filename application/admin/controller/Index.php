<?php 
namespace app\admin\controller;
use app\admin\controller\Base;
use think\Session;

class Index extends Base
{
    public function index()
    {
        
        $this -> isLogin();
        return $this->view->fetch();  
    
    }
    public function welcome()
    {
       return $this->view->fetch(); 
    }
    
}



 ?>