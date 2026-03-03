<?php

namespace App\Http\Controllers\Api\V1\Comment;

use App\Contracts\Actions\Comment\CreatesCommentAction;
use App\Contracts\Actions\Comment\DeletesCommentAction;
use App\Contracts\Actions\Comment\UpdatesCommentAction;
use App\Data\CommentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function index(Organization $org, Project $project, Task $task)
    {
        $this->authorize('viewAny', [Comment::class, $task]);

        $comments = $task->comments()
            ->orderBy('created_at')
            ->get();

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Organization $org, Project $project, Task $task, CreatesCommentAction $action)
    {
        $comment = $action->create(
            actor: $request->user(),
            task: $task,
            data: new CommentData(
                body: $request->string('body')
            )
        );

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateCommentRequest $request, Organization $org, Project $project, Task $task, Comment $comment, UpdatesCommentAction $action)
    {
        $updated = $action->update(
            actor: $request->user(),
            comment: $comment,
            data: new CommentData(
                body: $request->string('body')
            )
        );

        return new CommentResource($updated);
    }

    public function destroy(Organization $org, Project $project, Task $task, Comment $comment, DeletesCommentAction $action)
    {
        $action->delete(
            actor: request()->user(),
            comment: $comment,
        );

        return response()->noContent();
    }
}
