<?php

namespace App\Providers;

use App\Repositories\Admin\Groups\AdminAdminGroupRepository;
use App\Repositories\Admin\Groups\AdminGroupRepositoryInterface;
use App\Repositories\Admin\Questions\QuestionRepository;
use App\Repositories\Admin\Questions\QuestionRepositoryInterFace;
use App\Repositories\Admin\RoadSignGroups\AdminSignGroupRepository;
use App\Repositories\Admin\RoadSignGroups\AdminSignRepositoryInterface;
use App\Repositories\Admin\Tests\AdminTestRepository;
use App\Repositories\Admin\Tests\AdminTestRepositoryInterFace;
use App\Repositories\Admin\UsersGroups\UserGroupRepository;
use App\Repositories\Admin\Users\UserRepository;
use App\Repositories\Admin\Users\UserRepositoryInterface;
use App\Repositories\Admin\UsersGroups\UserGroupRepositoryInterface;
use App\Repositories\Api\V1\ExamTestRepository;
use App\Repositories\Api\V1\ExamTestRepositoryInterface;
use App\Repositories\Api\V1\FavoriteQuestionRepository;
use App\Repositories\Api\V1\FavoriteQuestionInterface;
use App\Repositories\Api\V1\GroupRepository;
use App\Repositories\Api\V1\GroupRepositoryInterface;
use App\Repositories\Api\V1\UserRepository as UserRepo;
use App\Repositories\Api\V1\UserRepositoryInterface as UserRepoInterface;
use App\Repositories\PasswordResetRepository;
use App\Repositories\PasswordResetRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Admin\RoadSign\RoadSignRepositoryInterFace as AdminRoadSignRepositoryInterFace;
use App\Repositories\Admin\RoadSign\RoadSignRepository as AdminRoadSignRepository;
use App\Repositories\Api\V1\RoadSignRepositoryInterface;
use App\Repositories\Api\V1\RoadSignRepository;
use App\Repositories\Api\V1\RoadRulesRepository;
use App\Repositories\Api\V1\RoadRulesRepositoryInterface;
use App\Repositories\Api\V1\StatisticRepository;
use App\Repositories\Api\V1\StatisticRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ExamTestRepositoryInterface::class, ExamTestRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(AdminGroupRepositoryInterface::class, AdminAdminGroupRepository::class);
        $this->app->bind(AdminTestRepositoryInterFace::class, AdminTestRepository::class);
        $this->app->bind(QuestionRepositoryInterFace::class, QuestionRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserRepoInterface::class, UserRepo::class);
        $this->app->bind(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);
        $this->app->bind(UserGroupRepositoryInterface::class, UserGroupRepository::class);
        $this->app->bind(AdminSignRepositoryInterface::class, AdminSignGroupRepository::class);
        $this->app->bind(AdminRoadSignRepositoryInterFace::class, AdminRoadSignRepository::class);
        $this->app->bind(RoadSignRepositoryInterface::class, RoadSignRepository::class);
        $this->app->bind(RoadRulesRepositoryInterface::class, RoadRulesRepository::class);
        $this->app->bind(StatisticRepositoryInterface::class, StatisticRepository::class);
        $this->app->bind(FavoriteQuestionInterface::class, FavoriteQuestionRepository::class);
    }

    public function boot(): void
    {

    }
}
