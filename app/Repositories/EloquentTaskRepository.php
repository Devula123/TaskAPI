<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Task::query()->orderByDesc('created_at')->paginate($perPage);
    }

    public function find(int $id): ?Task
    {
        return Task::query()->find($id);
    }

    public function create(array $data): Task
    {
        return Task::query()->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->fill($data);
        $task->save();

        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}

