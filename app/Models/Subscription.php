<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Subscription extends Model
{
    use HasFactory;
    protected $table = 'subscription';
    protected $primaryKey ="id";
    protected $fillable = [
        "subscription_name",
        "sub_id",
        "email",
        "plan_id",
        "description",
        "status",
        "company_file",
        "activated_date",
        "expired_date",
        "paddle_subscription_id"
    ];

}
