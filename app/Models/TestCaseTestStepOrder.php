<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCaseTestStepOrder extends Model
{
    use HasFactory;
    protected $table='test_case_test_step_orders';
    protected $fillable=[
        'TestCaseId',
        'TestStepId',
        'Order',
    ];
    public function testStep(){
        return $this->hasOne(TestStep::class, 'TestStepId', 'TestStepId');
    }
}
