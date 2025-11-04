<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExamTestQuestion extends Model
{
    protected $fillable = ['user_test_id', 'test_question_id', 'test_answer_id', 'is_right'];

    public function userExamTest()
    {
        return $this->belongsTo(UserExamTest::class);
    }

    public function question()
    {
        return $this->belongsTo(TestQuestion::class, 'test_question_id');
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class, 'test_answer_id');
    }
}
