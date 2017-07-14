<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TokenStorage extends Model
{
	protected $table = 'token_storage';
	protected $fillable = ['object'];

}
