<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apps extends Model
{
    use HasFactory;
    use SoftDeletes;

    use SoftDeletes;

    protected $hidden = ['deleted_at'];

    protected $dates = [ 'created_at','updated_at','deleted_at'];

    protected $fillable = [
        'app_id',
        'app_name',
        'app_desc',
        'app_link',
        'created_by'
    ];
}
