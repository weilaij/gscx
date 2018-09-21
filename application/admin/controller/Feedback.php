<?php 
namespace app\admin\controller;
use app\admin\controller\Base;
use think\Session;
use think\Db;
use think\Request;
use app\admin\model\Mistake as MisModel;
use app\admin\model\Stk as StkModel;
use app\index\model\Chinfo as ChModel;

class Feedback extends Base
{
    public function index()
    {
         $this -> isLogin(); 
        $this -> isLogin(); 
       $feedback=db('mistake')->where(['isback'=>0])->alias('m')->join('user u','m.user_id=u.student_id')
       ->join('stk s','m.id=s.id')
       ->join('chinfo c','s.ch=c.id')
       ->join('subinfo k','s.sub=k.id')
       ->field('m.st_id,m.mistake_location,m.mistake_des,m.create_time,u.name,m.id,k.abb,c.title')
       ->paginate(10);
         $count = count(MisModel::all(['isback'=>'0']));
      /*   for ($i=0; $i < $count ; $i++)
         { 
             $sub_info=StkModel::get(['id'=>$feedback[$i]['st_id']]);
             $ch_info=ChModel::get(['id'=>$sub_info['ch']]);
             array_push($feedback[$i],$sub_info['sub'],$ch_info['title']);
         //     $feedback[$i]['sub']=1;
         //  $feedback[$i]['ch_title']=$ch_info['title'];

         }*/
       print_r($feedback);
       /*  
        $this -> view -> assign('feedback', $feedback);
        $this -> view -> assign('count', $count);
          $this -> view -> assign('title', "未反馈");
          $this->view->assign(['keyword'=>'东华大学易班学霸思政题库']);
      $this->view->assign(['desc'=>'东华大学易班学霸思政题库']);
        return $this->view->fetch('');*/
    
    }
    public function choose(Request $request)
    {
    	 $id=$request->param('id');
    	 $type=Db::table('stk')->where('id',$id)->field('type')->select();

    	 if($type[0]['type'])
    	 {
    	 	$this->redirect('km/steditmany', ['id' => $id]);
    	 }else{
    	 	$this->redirect('km/steditone', ['id' => $id]);
    	 }
    }
    public function back(Request $request)
    {
       $this -> isLogin(); 
    	$id=$request->param('id');
    	 $this->view->assign('id',$id);
    	 return $this->view->fetch('back');
    }
    public function backupdat(Request $request)
    {
    	$status = 0;
        $message = "反馈失败";
    	$data=$request->param();
    	$msg=array('isback'=>'1',
                    'update_time'=>time(),
                     'back'=>$data['back']);
    	 $back=Db::table('mistake')->where('id',$data['id'])->update($msg);
    	 if ($back){
                $status = 1;
                $message = '反馈成功~~';
            }
        return ['status'=>$status, 'message'=>$message];

    }
     public function backinfo()
    {
        
        $this -> isLogin(); 
       $feedback=db('mistake')->where(['isback'=>1])->alias('m')->join('user u','m.user_id=u.student_id')->field('m.st_id,m.update_time,m.back,m.mistake_location,m.mistake_des,m.create_time,u.name,m.id')->paginate(10);
         $count = count(MisModel::all(['isback'=>'1']));
        $this -> view -> assign('feedback', $feedback);
        $this -> view -> assign('count', $count);
          $this -> view -> assign('title', "已处理	");
          $this->view->assign(['keyword'=>'东华大学易班学霸思政题库']);
      $this->view->assign(['desc'=>'东华大学易班学霸思政题库']);
        return $this->view->fetch('');
    
    }
    
    
};



 ?>