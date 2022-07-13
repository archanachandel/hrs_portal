<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadsComment extends Model
{
    use HasFactory;
    protected $table='lead_comment';
    protected $fillable=[
    'lead_id','comment','user_id'
     
    ];
}
