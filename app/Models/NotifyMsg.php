<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifyMsg extends Model
{
    use HasFactory;
    protected $table='notification';
    protected $fillable=[
        'lead_id',
        'user_id',
        'user_name',
        'channel_id'
       
    ];
}
