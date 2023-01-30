<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TestCaseTestStepOrder;

class TestCase extends Model
{
    use HasFactory;
    protected $table='test_cases';
    protected $primaryKey = 'testCaseId';
    protected $fillable=[
        'testCaseId',
        'title',
        'createdBy',
        'deleted',
    ];
    public function testStepsOrder(){
        return $this->hasMany(TestCaseTestStepOrder::class, 'testCaseId', 'testCaseId');
    }
}