<?php 
namespace app\index\model;
use think\Model;
class Gyxm extends Model
{
	 public function getCreateTimeAttr($value)
    {
        return date('Y/m/d H:i', $value);
    }
    public function seviceteam()
    {
        return $this->belongsTo('Seviceteam','stmid');
    }
}



 ?>