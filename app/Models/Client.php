<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
   use HasFactory;
    protected $fillable= [
        'name',
        'nit',
        'phone',
        'url_page',
        'status',
        'created_by',
        'updated_by',

    ];

    protected $casts =[
        'status' => 'string',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
