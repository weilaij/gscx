<?php 
namespace app\index\model;
use think\Model;
class Upload extends Model
{
	public function getCreateTimeAttr($value)
	{
		return date('Y/m/d ', $value);
	}
}



 ?>