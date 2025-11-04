<?php

namespace App\Console\Commands;

use App\Services\Api\V1\GroupService;
use Illuminate\Console\Command;

class QuestionGroupNumberCommand extends Command
{

    protected $signature = 'app:question-group-number-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(GroupService $groupService)
    {


        $groups = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        foreach ($groups as $group) {
            $questions = $groupService->getQuestionsByGroupIdClean($group);

            $questions->map(function ($question, $index) {
                $question->group_number = $index + 1;
                $question->save();
         });
        }
    }
}
