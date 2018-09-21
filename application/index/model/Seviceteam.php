<?php 
namespace app\index\model;
use think\Model;
class Seviceteam extends Model
{
	 public function getCreateTimeAttr($value)
    {
        return date('Y/m/d H:i', $value);
    }
}



 ?>