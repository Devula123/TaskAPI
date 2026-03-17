<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->tasks->paginate($perPage);
    }

    public function create(array $data): Task
    {
        $data['status'] = TaskStatus::Todo->value;

        return $this->tasks->create($data);
    }

    /**
     * @throws ValidationException
     */
    public function update(Task $task, array $data): Task
    {
        if (array_key_exists('status', $data)) {
            $newStatus = $data['status'];

            if (! TaskStatus::isValid($newStatus)) {
                throw ValidationException::withMessages([
                    'status' => ['Invalid status value.'],
                ]);
            }

            $current = $task->status;

            if ($newStatus !== $current) {
                $allowed = TaskStatus::allowedTransitions()[$current] ?? [];

                if (! in_array($newStatus, $allowed, true)) {
                    throw ValidationException::withMessages([
                        'status' => ["Status transition from {$current} to {$newStatus} is not allowed."],
                    ]);
                }
            }
        }

        return $this->tasks->update($task, $data);
    }

    public function delete(Task $task): void
    {
        $this->tasks->delete($task);
    }
}

