<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    protected $fillable= [
        'name',
        'phone',
        'email',
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
}
