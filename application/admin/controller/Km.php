<?php 
namespace app\admin\controller;
use app\admin\controller\Base;
use think\Session;
use think\Request;
use think\Db;
use app\admin\model\Seviceteam as StmModel;
use app\admin\model\Gyxm as XmModel;
use app\admin\model\Time as TmModel;
class Km extends Base
{
  public function colleglist()
  {
   $this -> isLogin(); 
    $stList = StmModel::all();
    $count = StmModel::count();
    $this -> view -> assign('stList', $stList);
    $this -> view -> assign('count', $count);     
     return $this->view->fetch();
  }
  public function collegeadd()
  {
    return $this->view->fetch();
  }
  public function collegedit(Request $request)
  {
    $data=$request -> param();
    $stminf=StmModel::get(['id'=>$data['id']]);
    $this->view->assign('stm',$stminf); 
    $this->view->assign('id',$data['id']); 
    return $this->view->fetch();
  }


  public function editch(Request $request)
  {
    $date = $request -> param();
    $status = 1;
    $message = "更新失败";
    $ch=StmModel::where("id",$date['id'])->update(['name'=>$date['name'],'colleg'=>$date['colleg']]);
    if ($ch) {
      $status = 0;
      $message = '更新成功~~';
    }
    return ['status'=>$status, 'message'=>$message];
  }
  

      //检测id是否可用
  public function checkCh(Request $request)
  {
    $data = $request -> param();
    $status = 1;
    $message = '服务队名可用';

    if (StmModel::get(['name'=>$data['name']])){
      //如果在表中查询到该用户名
      $status = 0;
      $message = '服务队名重复,请重新输入~~';
    }
    return ['status'=>$status, 'message'=>$message];
  }
  public function addch(Request $request)
  {
   $data = $request -> param();
    $status = 0;
    $message = "添加失败";
    $map=[
      'name'=>$data['name'],
      'colleg'=>$data['colleg'],
      'createtime'=>time()
    ];

    $ch= StmModel::create($map);
    if ($ch) {
      $status = 1;
      $message = '添加成功';
    }
  return ['status'=>$status, 'message'=>$message];
   
  }
  public function deleteCh(Request $request)
  {
    $ch_id = $request -> param('id');
    StmModel::destroy(['id'=> $ch_id]);   
  }
  public function gyxmlist()
  {
    $id=Session::get('user_info.stmid');
    if($id==0)
    {
      $xminfo=XmModel::all();
    }else{
      $xminfo=XmModel::all(['stmid'=>$id]);
    }
    $count = count($xminfo);
    $this -> view -> assign('xmList', $xminfo);
    $this -> view -> assign('count', $count); 
    return $this->view->fetch();
  }
  public function gyxmadd()
  {
    return $this->view->fetch();
  }
  public function addxm(Request $request)
  {
    $data = $request -> param();
    $status = 0;
    $message = "添加失败";
    if(XmModel::get(['name'=>$data['name']]))
      {
        $message = "项目名称重复";
      }else{
       $map=[
        'name'=>$data['name'],
        'stmid'=>Session::get('user_info.collegeid'),
        'createtime'=>time()
      ];
      $ch= XmModel::create($map);
      if ($ch) {
        $status = 1;
        $message = '添加成功';
      }
    }  
  return ['status'=>$status, 'message'=>$message];
  }
  public function gyxmedit(Request $request)
  {
    $date = $request -> param();
    $id=$date['id'];
    $xminf=XmModel::get(['id'=>$date['id']]);
    $this->view->assign('xm',$xminf); 
    $this->view->assign('id',$date['id']); 
    return $this->view->fetch();
  }
  public function gyxmeditinfo(Request $request)
  {
    $date = $request -> param();
    $status = 1;
    $message = "更新失败";
    $ch=XmModel::where("id",$date['id'])->update(['name'=>$date['name']]);
    if ($ch) {
      $status = 0;
      $message = '更新成功~~';
    }
    return ['status'=>$status, 'message'=>$message];
  }
  public function deletegyxm(Request $request)
  {
    $ch_id = $request -> param('id');
    XmModel::destroy(['id'=> $ch_id]);
    TmModel::destroy(['xmid'=>$ch_id]);   
  }









}



?>