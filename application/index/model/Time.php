<?php 
namespace app\index\model;
use think\Model;
class Time extends Model
{
	public function seviceteam()
    {
        return $this->belongsTo('Seviceteam','stmid');
    }
    public function gyxm()
    {
        return $this->belongsTo('gyxm','xmid');
    }
	
}



 ?>