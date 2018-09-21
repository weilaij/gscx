<?php 
namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use think\Session;
use app\index\model\Chinfo as ChModel;
use think\Db;
use app\index\model\User as UserModel;
use app\index\model\Seviceteam as StmModel;
use app\index\model\Time as TimModel;
use app\index\model\Upload as UplModel;

class Index extends Base
{
    public function index()
    {
      ybauth();
     isfirst();//判断是否第一次登陆
     $stminfo=StmModel::get(['colleg'=>Session::get('colleg')]);
     if($stminfo)
     {
      $gsdata=TimModel::all(['schoolid'=>Session::get('student_id')]);
      if($gsdata)
      {
        $time=UplModel::get(['stmid'=>$stminfo['id']]);
        $this -> view -> assign('timelist', $gsdata);
        $this -> view -> assign('update', $time['createtime']);
        $this -> view -> assign('name', $stminfo['name']);
        return $this->view->fetch();
      }else{
        return $this->view->fetch('select');
      }

     }else{
       return $this->view->fetch('error');
     }
     
    }
    
    
}



 ?>