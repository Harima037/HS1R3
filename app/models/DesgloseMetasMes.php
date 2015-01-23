<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DesgloseMetasMes extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "desgloseMetasMes";
}