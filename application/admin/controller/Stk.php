<?php 
namespace app\admin\controller;
use app\admin\controller\Base;
use think\Session;
use think\Request;
use think\Db;
use \think\File;
use app\admin\model\Stk as StkModel;
use app\index\model\Chinfo as ChModel;

class Stk extends Base
{
    public function index(){
        $this -> isLogin(); 
        $this->view->assign(['title'=>'配置题库']);
        $this->view->assign(['keyword'=>'东华大学易班学霸思政题库']);
        $this->view->assign(['desc'=>'东华大学易班学霸思政题库']);
     
      return $this->view->fetch();
    }

     public function choose(Request $request){//录入试题科目选择
        $status=0;
        $result='';
        $data=$request->param();

        $rule=[
         'sub'=>'require',
         'ch'=>'require',
        ];
        
        $msg=[
            'sub'=>['require'=>'请选择科目！'],
            'ch'=>['require'=>'请选择章节'],
           ];

        $result=$this->validate($data,$rule,$msg);

        if($result===true){
           
                $sub_id=$data['sub'];
                $ch_id=$data['ch'];
                $status=1;
                $chinfo=ChModel::get(['sub'=>$sub_id,'id'=>$ch_id]);
                Session::set('sub_id',$sub_id);
                Session::set('ch',$chinfo['title']);
                  Session::set('ch_id',$ch_id);
            }
        

        return ['status'=>$status,'message'=>$result];

     
    }
    public function muludata(Request $request)
    {
       $data=$request->param();
       $sub=$data['sub'];
       $chinfo=ChModel::all(['sub'=>$sub]);
       $chinfo_count=count($chinfo);
       $ch=array();
       for ($i=0; $i < $chinfo_count; $i++) { 
         $ch[$i]['id']=$chinfo[$i]['id'];
         $ch[$i]['name']=$chinfo[$i]['name'];
         $ch[$i]['title']=$chinfo[$i]['title'];
       }
      $chinfo_json=json_encode($ch,JSON_UNESCAPED_UNICODE);
       return $chinfo_json;

    }
    public function begin(){//题目编辑页面
        $this -> isLogin(); 
        $sub=[1=>"中国近代史",2=>"思修",3=>"毛概上",4=>"毛概下",5=>"马克思"];
        $this->view->assign(['title'=>'录入试题']);
        $this->view->assign(['keyword'=>'东华大学易班学霸思政题库']);
        $this->view->assign(['desc'=>'东华大学易班学霸思政题库']);
        $this->view->assign(['sub'=>$sub[Session::get('sub_id')]]);
        $this->view->assign(['ch'=>Session::get('ch')]);
        return $this->view->fetch('begin');

    }
    public function submit(Request $request){//提交试题到数据库
         $status=0;
         $data=$request->param();

        $rule=[
         'stem'=>'require',
         'key_a'=>'require',
         'key_b'=>'require',
         'key_c'=>'require',
         'key_d'=>'require',
         'ans'=>'require',
         ];
        if($data['ans']==1)
        {
            $ans='status_a';
        }elseif($data['ans']==2)
        {
            $ans='status_b';
        }elseif($data['ans']==3)
        {
            $ans='status_c';
        }else{
            $ans='status_d';
        }

        $msg=[
            'stem'=>['require'=>'请输入题干！'],
            'key_a'=>['require'=>'选项A不能为空'],
            'key_b'=>['require'=>'选项B不能为空'],
            'key_c'=>['require'=>'选项C不能为空'],
            'key_d'=>['require'=>'选项D不能为空'],
             'ans'=>['require'=>'请选择正确答案'],
           ];

        $result=$this->validate($data,$rule,$msg);

 
        if($result===true){
            $stk_data=array('stem'=>$data['stem'],
                            'type'=>0,
                          'sub'=>Session::get('sub_id'),
                          'ch'=>Session::get('ch_id'),
                          'create_time'=>time(),
                );
            $stk_op=array('key_a'=>$data['key_a'],
                          'key_b'=>$data['key_b'],
                          'key_c'=>$data['key_c'],
                          'key_d'=>$data['key_d'],
                          $ans=>1,
                          );
                $stk=Db::table('stk')->insert($stk_data);
                $stkop=Db::table('option')->insert($stk_op);
                if ($stk&&$stkop) {
                     $status = 1;
                     $result = '添加成功';
                }else{
                      
                     $result = '添加失败';
                }
           }
              
        return ['status'=>$status,'message'=>$result];
 

    }

    public function excel(){//渲染execl上传模板
        $this -> isLogin(); 
        $this->view->assign(['title'=>'模板导入']);
        $this->view->assign(['keyword'=>'东华大学易班学霸思政题库']);
        $this->view->assign(['desc'=>'东华大学易班学霸思政题库']);


        return $this->view->fetch('excel');
    }

    public function excel_input(){//处理excel上传内容
        

        
        import('phpexcel.PHPExcel', EXTEND_PATH);

        $file = request()->file('excel');  
        $info = $file->validate(['size'=>15678,'ext'=>'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'excel');  
        if($info){  
            $exclePath = $info->getSaveName();  //获取文件名  
            $file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;   //上传文件的地址  
      
       
         $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $PHPExcel = $objReader->load( $file_name);   //载入文件  
        $sheet = $PHPExcel->setActiveSheetIndex(0);         //设置第一个Sheet  
        //$data = $objPHPExcel->getActiveSheet()->getCell('A')->getValue();  //获取单元格A2的值  
        $highestRow = $sheet->getHighestRow();
        $highestColumm = $sheet->getHighestColumn();

        for($currentRow =2;$currentRow <= $highestRow;$currentRow++)
        {
         $stem= $PHPExcel->getActiveSheet()->getCell("A".$currentRow)->getValue();//获取A列的值
         $key_a= $PHPExcel->getActiveSheet()->getCell("B".$currentRow)->getValue();//获取B列的值
         $key_b= $PHPExcel->getActiveSheet()->getCell("C".$currentRow)->getValue();//获取C列的值
         $key_c= $PHPExcel->getActiveSheet()->getCell("D".$currentRow)->getValue();//获取D列的值
         $key_d= $PHPExcel->getActiveSheet()->getCell("E".$currentRow)->getValue();//获取E列的值
         $ans= $PHPExcel->getActiveSheet()->getCell("F".$currentRow)->getValue();//获取F列的值
         $sub_id= $PHPExcel->getActiveSheet()->getCell("G".$currentRow)->getValue();//获取G列的值
         $ch_id= $PHPExcel->getActiveSheet()->getCell("F".$currentRow)->getValue();//获取F列的值
    
         
           $stk_data=array('stem'=>$stem,
                          'key_a'=>$key_a,
                          'key_b'=>$key_b,
                          'key_c'=>$key_c,
                          'key_d'=>$key_d,
                          'ans'=>$ans,
                          'sub_id'=>$sub_id,
                          'ch_id'=>$ch_id,
                          'create_time'=>time(),
                );
          $stk=Db::table('stk')->insert($stk_data);
         if ($stk === null) {
             $this->error('导入题库失败','index.php/admin/stk/excel');
                exit;
                }
            
        
      }
      $this->success('导入题库成功','index.php/admin/stk/excel');

    }
}
    public function many(){//题目编辑页面
        $this -> isLogin(); 
        $sub=[1=>"中国近代史",2=>"思修",3=>"毛概上",4=>"毛概下",5=>"马克思"];
       
        $this->view->assign(['title'=>'录入试题']);
        $this->view->assign(['keyword'=>'东华大学易班学霸思政题库']);
        $this->view->assign(['desc'=>'东华大学易班学霸思政题库']);
        $this->view->assign(['sub'=>$sub[Session::get('sub_id')]]);
        $this->view->assign(['ch'=>Session::get('ch')]);
        return $this->view->fetch('many');

    }
      public function manysub(Request $request){//提交试题到数据库
         $status=0;
         $data=$request->param();
          $result = $data['ans6'];

            $stk_data=array('stem'=>$data['stem'],
                            'type'=>1,
                          'sub'=>Session::get('sub_id'),
                          'ch'=>Session::get('ch_id'),
                          'create_time'=>time(),
                );
            $stk_op=array('key_a'=>$data['key_a'],
                          'key_b'=>$data['key_b'],
                          'key_c'=>$data['key_c'],
                          'key_d'=>$data['key_d'],
                          'key_e'=>$data['key_e'],
                          'key_f'=>$data['key_f'],
                          'status_a'=>$data['ans1'],
                          'status_b'=>$data['ans2'],
                          'status_c'=>$data['ans3'],
                          'status_d'=>$data['ans4'],
                          'status_e'=>$data['ans5'],
                          'status_f'=>$data['ans6'],
                          );
                $stk=Db::table('stk')->insert($stk_data);
                $stkop=Db::table('option')->insert($stk_op);
                if ($stk&&$stkop) {
                     $status = 1;
                     $result = '添加成功';
                }else{
                      
                     $result = '添加失败';
                }
           
              
        return ['status'=>$status,'message'=>$result];
 

    }

}



 ?>