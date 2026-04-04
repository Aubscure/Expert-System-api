<?php

namespace App\Providers;

use App\Interface\Repository\ChoiceRepositoryInterface;
use App\Interface\Repository\ExpertInvitationRepositoryInterface;
use App\Interface\Repository\ExpertRepositoryInterface;
use App\Interface\Repository\QuestionnaireRepositoryInterface;
use App\Interface\Repository\QuestionRepositoryInterface;
use App\Interface\Repository\QuizSessionRepositoryInterface;
use App\Interface\Repository\SeverityLevelRepositoryInterface;
use App\Interface\Service\AdminServiceInterface;
use App\Interface\Service\AuthServiceInterface;
use App\Interface\Service\ChoiceServiceInterface;
use App\Interface\Service\QuestionnaireServiceInterface;
use App\Interface\Service\QuestionServiceInterface;
use App\Interface\Service\QuizSessionServiceInterface;
use App\Interface\Service\SeverityLevelServiceInterface;
use App\Repository\ChoiceRepository;
use App\Repository\ExpertInvitationRepository;
use App\Repository\ExpertRepository;
use App\Repository\QuestionnaireRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizSessionRepository;
use App\Repository\SeverityLevelRepository;
use App\Service\AdminService;
use App\Service\AuthService;
use App\Service\ChoiceService;
use App\Service\QuestionnaireService;
use App\Service\QuestionService;
use App\Service\QuizSessionService;
use App\Service\SeverityLevelService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositories
        $this->app->bind(QuestionnaireRepositoryInterface::class,    QuestionnaireRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class,         QuestionRepository::class);
        $this->app->bind(ChoiceRepositoryInterface::class,           ChoiceRepository::class);
        $this->app->bind(SeverityLevelRepositoryInterface::class,    SeverityLevelRepository::class);
        $this->app->bind(QuizSessionRepositoryInterface::class,      QuizSessionRepository::class);
        $this->app->bind(ExpertInvitationRepositoryInterface::class, ExpertInvitationRepository::class);
        $this->app->bind(ExpertRepositoryInterface::class,           ExpertRepository::class);

        // Services
        $this->app->bind(AuthServiceInterface::class,             AuthService::class);
        $this->app->bind(QuestionnaireServiceInterface::class,    QuestionnaireService::class);
        $this->app->bind(QuestionServiceInterface::class,         QuestionService::class);
        $this->app->bind(ChoiceServiceInterface::class,           ChoiceService::class);
        $this->app->bind(SeverityLevelServiceInterface::class,    SeverityLevelService::class);
        $this->app->bind(QuizSessionServiceInterface::class,      QuizSessionService::class);
        $this->app->bind(AdminServiceInterface::class,            AdminService::class);

        // AiAnalysisService and PdfService are concrete — no interface needed
        // since they have no alternate implementations in this project
        $this->app->singleton(\App\Service\AiAnalysisService::class);
        $this->app->singleton(\App\Service\PdfService::class);
    }

    public function boot(): void
    {
        //
    }
}
