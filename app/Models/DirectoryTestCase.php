<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectoryTestCase extends Model
{
    use HasFactory;
    protected $table='directory_test_cases';
    protected $primaryKey = 'directoryTestCaseId';
    public $timestamps = false;
    protected $fillable=[
        'directoryTestCaseId',
        'directoryId',
        'testCaseId'
    ];
}
