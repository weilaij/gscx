<?php 
namespace app\admin\controller;
use app\admin\controller\Base;
use think\Request;
use app\admin\model\Admin_user as UserModel;
use app\admin\model\Seviceteam as StmModel;
use think\Session;

class User extends Base
{
    public function login()
    {
         $this -> alreadyLogin();
        return $this->view->fetch();
    }
   /* 判断后台登录*/
     public function checklogin(Request $request)
    {
        $status=0;
        $result='123';
        $data=$request->param();    
       
    	$map=[
    		'name'=>$data['username'],
    		'password'=>md5($data['password']),
    	];

    	$user=UserModel::get($map);
    	if($user==null){
    		$result='没有找到该用户';
    	}else{
    		$status=1;
    		$result='登陆成功';
    		Session::set('user_id',$user->name);
    		Session::set('user_info',$user->getData());
    	}
        return ['status'=>$status,'message'=>$result];


    }
    /*注销登录操作*/
     public function logout()
    {
        Session::delete('user_id');
        Session::delete('user_infoer');
        $this->success('注销登陆，正在返回','index.php/admin/user/login');
    }
    /* 渲染管理员列表*/
    public function adminlist()
    {
       $this -> isLogin(); 
    	$userName=Session::get('user_info.name');

    	if($userName=='admin'){
    		$list=UserModel::all();

    	}else{
    		$list=UserModel::all(['name'=>$userName]);
    	}
        $count=count($list);
    	$this->view->assign(['list'=>$list]);
        $this->view->assign(['count'=>$count]);
     
      return $this->view->fetch();

    }

   public function  adminadd()
    {
       
        return $this->view->fetch();
    }
    public function stmdata()
    {
        $stminfo=StmModel::all();
        return $stminfo;
    }

    //检测用户名是否可用
    public function checkUserName(Request $request)
    {
        $data = $request -> param();
        $status = 1;
        $message = '用户名可用';

        if (UserModel::get(['name'=> $data['name']])) {
            //如果在表中查询到该用户名
            $status = 0;
            $message = '用户名重复,请重新输入~~';
        }
        return ['status'=>$status, 'message'=>$message];
    }
    //删除操作
    public function deleteUser(Request $request)
    {
        $user_id = $request -> param('id');
        UserModel::destroy(['id'=> $user_id]);
        

    }
     public function editUser(Request $request)
    {
        $date = $request -> param();
        $status = 1;
        $message = "更新失败";
        $update_time=time();
        $user=UserModel::where("id",$date['id'])->update(['password'=>md5($date['pass']),'update_time'=>$update_time]);
    
        $status = 0;
        $message = '更新成功~~';
            
         return ['status'=>$status, 'message'=>$message];
        

    }
    public function adminEdit(Request $request)
    {
        $user_id = $request -> param('id');
        $result = UserModel::get(['id'=>$user_id]);
        $this->view->assign('userinfo',$result);
        return $this->view->fetch();
    }
        public function addUser(Request $request)
    {
        $data = $request -> param();
        $status = 1;
        $message = "添加成功";
       $create_time=time();
        $map=[
            'name'=>$data['username'],
            'password'=>md5($data['pass']),
            'collegeid'=>$data['collegeid'],
            'create_time'=>$create_time
        ];

        $user= UserModel::create($map);
        if ($user === null) {
            $status = 0;
            $message = '添加失败~~';
        }
        


        return ['status'=>$status, 'message'=>$message];
    }
}



 ?>