<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChannel extends Model
{
    use HasFactory;
    protected $table='user_channel';
    protected $fillable=[
        'user_id',
        'channel_id',
    ];


}
