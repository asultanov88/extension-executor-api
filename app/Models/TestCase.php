<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{
    use HasFactory;
    protected $table='test_cases';
    protected $primaryKey = 'TestCaseId';
    protected $fillable=[
        'TestCaseId',
        'Title',
        'CreatedBy',
        'Deleted',
    ];
    public function testSteps(){
        return $this->hasMany(TestStep::class, 'TestCaseId', 'TestCaseId');
    }
}
