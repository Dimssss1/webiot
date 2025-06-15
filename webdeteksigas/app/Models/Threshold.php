<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Threshold extends Model
{
    protected $table = 'thresholds';

    protected $fillable = ['gas_threshold', 'fire_threshold'];
}

