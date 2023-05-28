<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCaseExecution extends Model
{
    use HasFactory;
    protected $table='test_case_executions';
    protected $primaryKey = 'testCaseExecutionId';
    protected $fillable=[
        'testCaseExecutionId',
        'testCaseId',
        'statusId',
        'resultId',
        'executedBy',
    ];    
}
