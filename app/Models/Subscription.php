<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $hidden = ['deleted_at'];

    public function subscriptionPlans()
    {
        return $this->belongsTo(SubscriptionPlan::class,'subscription_plan','id');
    }
}
