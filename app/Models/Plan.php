<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Plan extends Model
{
    use HasFactory;
    protected $table = 'plans';
    protected $primaryKey ="plan_id";
    protected $fillable = [
        "name",
        "price",
        "features",
        "bill_period",
        "period",
        "paddle_id",
        "created_by",
        "updated_by"
    ];

}
