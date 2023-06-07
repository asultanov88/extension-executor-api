<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTestCase extends Model
{
    use HasFactory;
    protected $table='event_test_cases';
    protected $primaryKey = 'eventTestCaseId';
    public $timestamps = false;
    protected $fillable=[
        'eventTestCaseId',
        'eventId',
        'testCaseId',
    ];
}
