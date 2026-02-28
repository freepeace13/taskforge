<?php

namespace App\Http\Controllers\Api\V1\Comment;

use App\Actions\Comment\CreateCommentAction;
use App\Actions\Comment\DeleteCommentAction;
use App\Actions\Comment\UpdateCommentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function index(Project $project, Task $task)
    {
        $this->ensureTaskBelongsToProjectAndTenant($project, $task);

        $comments = Comment::query()
            ->where('task_id', $task->id)
            ->orderBy('created_at')
            ->get();

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Project $project, Task $task, CreateCommentAction $action)
    {
        $this->ensureTaskBelongsToProjectAndTenant($project, $task);

        $comment = $action->create(
            task: $task,
            user: $request->user(),
            body: $request->string('body')->toString(),
        );

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateCommentRequest $request, Project $project, Task $task, Comment $comment, UpdateCommentAction $action)
    {
        $this->ensureCommentBelongsToTaskProjectAndTenant($project, $task, $comment);

        $updated = $action->update(
            comment: $comment,
            actor: $request->user(),
            body: $request->string('body')->toString(),
        );

        return new CommentResource($updated);
    }

    public function destroy(Project $project, Task $task, Comment $comment, DeleteCommentAction $action)
    {
        $this->ensureCommentBelongsToTaskProjectAndTenant($project, $task, $comment);

        $action->delete(
            comment: $comment,
            actor: request()->user(),
        );

        return response()->noContent();
    }

    protected function ensureTaskBelongsToProjectAndTenant(Project $project, Task $task): void
    {
        abort_if($project->organization_id !== tenant()->organizationId, Response::HTTP_NOT_FOUND);
        abort_if($task->project_id !== $project->id, Response::HTTP_NOT_FOUND);
    }

    protected function ensureCommentBelongsToTaskProjectAndTenant(Project $project, Task $task, Comment $comment): void
    {
        $this->ensureTaskBelongsToProjectAndTenant($project, $task);

        abort_if($comment->task_id !== $task->id, Response::HTTP_NOT_FOUND);
    }
}
