<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestStep extends Model
{
    use HasFactory;
    protected $table='test_steps';
    protected $primaryKey = 'testStepId';
    protected $fillable=[
        'testStepId',
        'description',
        'expected',
        'testCaseId',
        'deleted',
    ];
    public function testCase(){
        return $this->belongsTo(TestCase::class, 'testCaseId');
    }

}
