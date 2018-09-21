<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Exam_info as ExamModel;
use think\Request;
use think\Session;
use think\Db;
use app\admin\model\Gyxm as XmModel;
use app\admin\model\Time as TmModel;
use app\admin\model\Upload as UpModel;
use \think\File;


class Exam extends Base
{
    public function index()
    {
      $this -> isLogin(); 
      return $this->view->fetch();
    }
    public function download()
    {
      Vendor('phpexcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
      Vendor('phpexcel.PHPExcel.Worksheet.Drawing');
      Vendor('phpexcel.PHPExcel.Writer.Excel2007');
      $excel = new \PHPExcel();
      $letter = array('A','B','C','D','E','F','F','G','H','I','J','K','L','M','N','O','P','Q');
      $xminfo=XmModel::ALL(['stmid'=>Session::get('user_info.collegeid')]);
      for ($i=0; $i <count($xminfo) ; $i++) { 
        $tableheader[$i+1] = $xminfo[$i]['name'];
      }
       
      $excel->getActiveSheet()->setCellValue("$letter[0]1","学号");
      for($i = 1;$i < count($tableheader)+1;$i++)
      {
        $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
      }
      // for ($i = 2;$i <= count($list) + 1;$i++) 
      // {
      //   $j = 0;
      //   foreach ($list[$i - 2] as $key=>$value) 
      //   {
      //     $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
      //     $j++;
      //   }
      // }
      //创建Excel输入对象
      $write = new \PHPExcel_Writer_Excel5($excel);
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
      header("Content-Type:application/force-download");
      ob_end_clean();
      header("Content-Type:application/vnd.ms-execl");
      header("Content-Type:application/octet-stream");
      header("Content-Type:application/download");;
      header('Content-Disposition:attachment;filename="服务时长录入模板.xls"');
      header("Content-Transfer-Encoding:binary");
      $write->save('php://output');


    }

    public function input()
    {
      import('phpexcel.PHPExcel', EXTEND_PATH);
      $file = request()->file('exam');
      $info = $file->validate(['size'=>15678000,'ext'=>'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'excel');
      if($info)
      {
        $exclePath = $info->getSaveName();  //获取文件名  
        $file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;   //上传文件的地址  
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $PHPExcel = $objReader->load( $file_name);   //载入文件  
        $sheet = $PHPExcel->setActiveSheetIndex(0);         //设置第一个Sheet   
        $highestRow = $sheet->getHighestRow();
        $highestColumm = $sheet->getHighestColumn();
        $letter = array('A','B','C','D','E','F','F','G','H','I','J','K','L','M','N','O','P','Q');
        $highestColumm=array_search($highestColumm,$letter);
        $xmcount=XmModel::all(['stmid'=>Session::get('user_info.collegeid')]);
       
        if(count($xmcount)==$highestColumm)
        {
         $up=TmModel::all(['stmid'=>Session::get('user_info.collegeid')]);
         if($up)
         {
          TmModel::destroy(['stmid'=>Session::get('user_info.collegeid')]); 
        }
        $highestColumm+=1;
        for ($i=1; $i < $highestColumm; $i++) 
        {
          $xmname=$PHPExcel->getActiveSheet()->getCell($letter[$i]."1")->getValue();
          $xminfo=XmModel::get(['name'=>$xmname]);
          $xmid[$i]=$xminfo['id'];
        }
        for($currentRow =2;$currentRow <= $highestRow;$currentRow++)
        {
         $school_id= $PHPExcel->getActiveSheet()->getCell("A".$currentRow)->getValue();//获取A列的值
         for ($i=1; $i < $highestColumm; $i++) { 
           $id=$xmid[$i];
           $stmid=Session::get('user_info.collegeid');
           $time=$PHPExcel->getActiveSheet()->getCell($letter[$i].$currentRow)->getValue();
           $map=[
            'stmid'=>$stmid,
            'schoolid'=>$school_id,
            'xmid'=>$id,
            'time'=>$time
          ];
          $ch= TmModel::create($map);
          if ($ch === null) {
           $this->error('导入工时信息失败，请重新导入，或联系管理员！','index.php/admin/exam/index');
           exit;
         }
       }        
     }
   }else{
     $this->error('导入工时信息失败，导入的项目数与所创建项数不符！','index.php/admin/exam/index');
   }
    $up=UpModel::all(['stmid'=>Session::get('user_info.collegeid')]);
    if($up)
    {
      UpModel::destroy(['stmid'=>Session::get('user_info.collegeid')]); 
    }
    $msg=[
      "stmid"=>Session::get('user_info.collegeid'),
      "createtime"=>time()
    ];
    $ch=UpModel::create($msg);
    $this->success('导入工时成功','index.php/admin/exam/index');
    }

    }
    public function output(){
      $this -> isLogin(); 
      $id=Session::get('user_info.collegeid'); 
      $xninfo=XmModel::all(['stmid'=>$id]);
      $upinfo=UpModel::get(['stmid'=>$id]);
      $uptime=$upinfo['createtime'];
      for ($i=0; $i <count($xninfo) ; $i++) { 
        if($xninfo[$i]['createtime'] > $uptime)
        {
          $this->error('查看导入信息失败失败，有新增项目，需要重新上传工时！','index.php/admin/exam/index');
        }
      }
        $tmlist=TmModel::all(['stmid'=>$id]);
        $xm=XmModel::all(['stmid'=>$id]);
        $k=0;
        $count=count($xm);
        $tmcount=count($tmlist);
        for ($j=0; $j <(count($tmlist)/$count) ; $j++) { 
          $tmxm[$j]["schoolid"]=$tmlist[$k]['schoolid'];
          for ($i=0; $i < $count; $i++) { 
            $tmxm[$j]['time'][$i]=$tmlist[$k]['time'];
            $k++;
          }
        }   
      $tmxmcount=count($tmxm);
         $this -> view -> assign('xmlist', $xm);
         $this -> view -> assign('tmlist', $tmxm);
         $this -> view -> assign('count', $tmxmcount);
    	   return $this->view->fetch();
    }
}


?>