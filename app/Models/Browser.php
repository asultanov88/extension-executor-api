<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Browser extends Model
{
    use HasFactory;
    protected $table ='browser';
    protected $primaryKey = 'browserId';
    public $timestamps = false;
    protected $fillable=[
        'browserId',
        'name',
        'deleted',
        'replacedByBrowserId',
        'createdBy',
        'updatedBy',
    ];
}
