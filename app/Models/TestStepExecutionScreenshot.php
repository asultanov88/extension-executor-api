<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestStepExecutionScreenshot extends Model
{
    use HasFactory;
    protected $table='test_step_execution_screenshots';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable=[
        'testStepExecutionId',
        'screenshotId',
        'screenshotUuid',
    ];
}
