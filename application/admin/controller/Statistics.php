<?php 
namespace app\admin\controller;
use app\admin\controller\Base;
use think\Request;
use think\Db;
use app\admin\model\Statistics as UstaModel;
use app\admin\model\Mksta as MkModel;
use app\admin\model\Stk as StkModel;
use think\Session;

class Statistics extends Base
{
    public function UserSta()
    {
      $this -> isLogin(); 
      $this->view->assign(['title'=>'学生答题统计']);
      $this->view->assign(['keyword'=>'东华大学易班学霸思政题库']);
      $this->view->assign(['desc'=>'东华大学易班学霸思政题库']);

      /*$ustaList = UstaModel::paginate(1);*/
     /*关联查询*/
     $ustaList=db('statistics')->alias('s')->join('user u','s.student_id=u.student_id')->field('s.id,s.student_id,s.user_sum,s.user_wrong,s.km_sum,u.name')->paginate(10);
        //获取记录数量
        $count = count( $ustaList);
        $this -> view -> assign('ustaList', $ustaList);
        $this -> view -> assign('count', $count);
        return $this->view->fetch('usersta');
   }
   //试题统计
   public function Ststa()
   {
     $this -> isLogin(); 
      $this->view->assign(['title'=>'题目统计']);
      $this->view->assign(['keyword'=>'东华大学易班学霸思政题库']);
      $this->view->assign(['desc'=>'东华大学易班学霸思政题库']);
      $join=[['subinfo k','s.sub=k.id'],['chinfo c','s.ch=c.id']];
      $stList=Db::table('stk')->alias('s')->join($join)->field('k.abb,c.title,s.stem,s.type,s.ans_count,s.false_count')->paginate(10);
       /* $stList = StkModel::paginate(10);*/
        //获取记录数量
        $count = StkModel::count();
        $this -> view -> assign('stList', $stList);
        $this -> view -> assign('count', $count);
        return $this->view->fetch('ststa');
   }

    /*渲染模考统计模板*/
   public function mksta()
   {
     $this -> isLogin(); 
      $this->view->assign(['title'=>'学生模考统计']);
      $this->view->assign(['keyword'=>'东华大学易班学霸思政题库']);
      $this->view->assign(['desc'=>'东华大学易班学霸思政题库']);

      $ustaList = MkModel::paginate(10);
        //获取记录数量
        $count = MkModel::count();
        $this -> view -> assign('ustaList', $ustaList);
        $this -> view -> assign('count', $count);
        return $this->view->fetch('mksta');
   }

}



 ?>