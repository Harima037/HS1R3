<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DesgloceMetasMes extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "desgloceMetasMes";
}