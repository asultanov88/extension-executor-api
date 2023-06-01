<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestStepExecution extends Model
{
    use HasFactory;
    protected $table='test_step_executions';
    protected $primaryKey = 'testStepExecutionId';
    protected $fillable=[
        'testStepExecutionId',
        'testCaseExecutionId',
        'testStepId',
        'resultId',
        'sequence',
        'actualResult',
    ];
}
