<?php 
namespace app\admin\model;
use think\Model;
class Time extends Model
{
	public function seviceteam()
    {
        return $this->belongsTo('Seviceteam','stmid');
    }
	
}



 ?>