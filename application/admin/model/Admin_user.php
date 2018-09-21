<?php 
namespace app\admin\model;
use think\Model;
class Admin_user extends Model
{
    /*输出时间格式调整*/
      public function getLoginTimeAttr($value)
    {
        return date('Y/m/d H:i', $value);
    }
   /*   管理员状态和说明文字匹配*/
     public function getStatusAttr($value)
    {
        $status = [
            0=>'已停用',
            1=> '已启用'
        ];
        return $status[$value];
    }
     /*管理员身份id与说明文字匹配*/
     public function getRoleAttr($value)
    {
        $role = [
           0=>'管理员',
            1=> '超级管理员'
        ];
        return $role[$value];
    }
    public function seviceteam()
    {
        return $this->belongsTo('Seviceteam','collegeid');
    }
}



 ?>