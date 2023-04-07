<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportedTestCase extends Model
{
    use HasFactory;
    protected $table='imported_test_cases';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable=[
        'testCaseId',
        'importedTestCaseId',
        'importOrder',
    ];
}
