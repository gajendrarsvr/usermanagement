<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

//Models
use App\Models\User;
use App\Models\Apps;
use App\Models\AppsUser;

class AppsUser extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'apps_user';

    protected $hidden = ['deleted_at'];

    protected $dates = [ 'created_at','updated_at','deleted_at'];

    protected $fillable = [
        'app_id',
        'user_id'
    ];


    public function app_user_detail()
    {
        return $this->hasOne(User::class,'id','user_id');
    }


    public function app_detail()
    {
        return $this->hasOne(Apps::class,'id','app_id');
    }
}
