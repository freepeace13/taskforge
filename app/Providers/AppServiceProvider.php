<?php

namespace App\Providers;

use App\Actions\Comment\CreateCommentAction;
use App\Actions\Comment\DeleteCommentAction;
use App\Actions\Comment\UpdateCommentAction;
use App\Actions\Organization\AcceptInvitationAction;
use App\Actions\Organization\CancelInvitationAction;
use App\Actions\Organization\CreateOrganizationAction;
use App\Actions\Organization\DeleteOrganizationAction;
use App\Actions\Organization\InviteUserAction;
use App\Actions\Organization\RemoveMemberAction;
use App\Actions\Organization\UpdateMemberRoleAction;
use App\Actions\Project\ArchiveProjectAction;
use App\Actions\Project\CreateProjectAction;
use App\Actions\Project\DeleteProjectAction;
use App\Actions\Project\RestoreProjectAction;
use App\Actions\Project\UpdateProjectAction;
use App\Actions\Task\AssignTaskAction;
use App\Actions\Task\CompleteTaskAction;
use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\ReopenTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\Contracts\Actions\Comment\CreatesCommentAction as CreatesCommentContract;
use App\Contracts\Actions\Comment\DeletesCommentAction as DeletesCommentContract;
use App\Contracts\Actions\Comment\UpdatesCommentAction as UpdatesCommentContract;
use App\Contracts\Actions\Organization\AcceptsInvitationAction;
use App\Contracts\Actions\Organization\CancelsInvitationAction as CancelsInvitationContract;
use App\Contracts\Actions\Organization\CreatesOrganizationAction as CreatesOrganizationContract;
use App\Contracts\Actions\Organization\DeletesOrganizationAction as DeletesOrganizationContract;
use App\Contracts\Actions\Organization\InvitesUserAction;
use App\Contracts\Actions\Organization\RemovesMemberAction as RemovesMemberContract;
use App\Contracts\Actions\Organization\UpdatesMemberRoleAction as UpdatesMemberRoleContract;
use App\Contracts\Actions\Project\ArchivesProjectAction as ArchivesProjectContract;
use App\Contracts\Actions\Project\CreatesProjectAction as CreatesProjectContract;
use App\Contracts\Actions\Project\DeletesProjectAction as DeletesProjectContract;
use App\Contracts\Actions\Project\RestoresProjectAction as RestoresProjectContract;
use App\Contracts\Actions\Project\UpdatesProjectAction as UpdatesProjectContract;
use App\Contracts\Actions\Task\AssignsTaskAction as AssignsTaskContract;
use App\Contracts\Actions\Task\CompletesTaskAction as CompletesTaskContract;
use App\Contracts\Actions\Task\CreatesTaskAction as CreatesTaskContract;
use App\Contracts\Actions\Task\DeletesTaskAction as DeletesTaskContract;
use App\Contracts\Actions\Task\ReopensTaskAction as ReopensTaskContract;
use App\Contracts\Actions\Task\UpdatesTaskAction as UpdatesTaskContract;
use App\Data\TenantContext;
use App\Models\Comment;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InvitesUserAction::class, InviteUserAction::class);
        $this->app->bind(AcceptsInvitationAction::class, AcceptInvitationAction::class);
        $this->app->bind(CancelsInvitationContract::class, CancelInvitationAction::class);

        $this->app->bind(CreatesProjectContract::class, CreateProjectAction::class);
        $this->app->bind(UpdatesProjectContract::class, UpdateProjectAction::class);
        $this->app->bind(DeletesProjectContract::class, DeleteProjectAction::class);
        $this->app->bind(ArchivesProjectContract::class, ArchiveProjectAction::class);
        $this->app->bind(RestoresProjectContract::class, RestoreProjectAction::class);

        $this->app->bind(CreatesTaskContract::class, CreateTaskAction::class);
        $this->app->bind(UpdatesTaskContract::class, UpdateTaskAction::class);
        $this->app->bind(DeletesTaskContract::class, DeleteTaskAction::class);
        $this->app->bind(AssignsTaskContract::class, AssignTaskAction::class);
        $this->app->bind(CompletesTaskContract::class, CompleteTaskAction::class);
        $this->app->bind(ReopensTaskContract::class, ReopenTaskAction::class);

        $this->app->bind(UpdatesMemberRoleContract::class, UpdateMemberRoleAction::class);
        $this->app->bind(RemovesMemberContract::class, RemoveMemberAction::class);
        $this->app->bind(CreatesOrganizationContract::class, CreateOrganizationAction::class);
        $this->app->bind(DeletesOrganizationContract::class, DeleteOrganizationAction::class);

        $this->app->bind(CreatesCommentContract::class, CreateCommentAction::class);
        $this->app->bind(UpdatesCommentContract::class, UpdateCommentAction::class);
        $this->app->bind(DeletesCommentContract::class, DeleteCommentAction::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Route::bind('project', function ($project, $route) {
            $org = $route->parameter('org')
                ?? (app()->bound(TenantContext::class) ? app(TenantContext::class)->organization : null);

            $orgConstraint = $org instanceof Organization
                ? fn ($q) => $q->whereKey($org->id)
                : fn ($q) => $q->where('slug', $org);

            return Project::query()
                ->whereKey($project)
                ->whereHas('organization', $orgConstraint)
                ->firstOrFail();
        });

        Route::bind('invite', function ($invite, $route) {
            $org = $route->parameter('org')
                ?? (app()->bound(TenantContext::class) ? app(TenantContext::class)->organization : null);

            $orgConstraint = $org instanceof Organization
                ? fn ($q) => $q->whereKey($org->id)
                : fn ($q) => $q->where('slug', $org);

            return OrganizationInvite::query()
                ->whereKey($invite)
                ->whereHas('organization', $orgConstraint)
                ->firstOrFail();
        });

        Route::bind('task', function ($task, $route) {
            $org = $route->parameter('org')
                ?? (app()->bound(TenantContext::class) ? app(TenantContext::class)->organization : null);

            $orgConstraint = $org instanceof Organization
                ? fn ($q) => $q->whereKey($org->id)
                : fn ($q) => $q->where('slug', $org);

            $projectParam = $route->parameter('project');
            $projectId = $projectParam instanceof Project ? $projectParam->id : $projectParam;

            return Task::query()
                ->whereKey($task)
                ->whereHas('project', function ($q) use ($orgConstraint, $projectId) {
                    $q->whereKey($projectId)->whereHas('organization', $orgConstraint);
                })
                ->firstOrFail();
        });

        Route::bind('comment', function ($comment, $route) {
            $org = $route->parameter('org')
                ?? (app()->bound(TenantContext::class) ? app(TenantContext::class)->organization : null);
            $project = $route->parameter('project');
            $task = $route->parameter('task');

            $orgSlug = $org instanceof Organization ? $org->slug : $org;
            $projectId = $project instanceof Project ? $project->getKey() : $project;
            $taskId = $task instanceof Task ? $task->getKey() : $task;

            return Comment::query()
                ->whereKey($comment)
                ->whereHas('task', function ($q) use ($orgSlug, $projectId, $taskId) {
                    $q->whereKey($taskId)
                        ->whereHas('project', function ($q) use ($orgSlug, $projectId) {
                            $q->whereKey($projectId)
                                ->whereHas('organization', fn ($q) => $q->where('slug', $orgSlug));
                        });
                })
                ->firstOrFail();
        });
    }
}
