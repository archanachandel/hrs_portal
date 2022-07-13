<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $table='leads';
    protected $fillable=[
        'name',
        'email',
        'assignee',
        'category_id',
        'skype_id',
        'phone_number',
        'message',
        'ip_address',
        'status',
        'channel_id',
        'created_by',
        'datetime',
        'company_name',
        'contacted_website',
    ];


}
