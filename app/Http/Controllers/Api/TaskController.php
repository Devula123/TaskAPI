<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $service,
    ) {
    }


    public function index(): ResourceCollection
    {
        $tasks = $this->service->list();

        return TaskResource::collection($tasks);
    }


    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->service->create($request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }


    public function show(int $id): TaskResource|JsonResponse
    {
        $task = Task::query()->find($id);

        if (! $task) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, int $id): TaskResource|JsonResponse
    {
        $task = Task::query()->find($id);

        if (! $task) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        $updated = $this->service->update($task, $request->validated());

        return new TaskResource($updated);
    }


    public function destroy(int $id): JsonResponse
    {
        $task = Task::query()->find($id);

        if (! $task) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        $this->service->delete($task);

        return response()->json(null, 204);
    }
}
