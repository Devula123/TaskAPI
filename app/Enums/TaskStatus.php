<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';

    /**
     * @return array<string, string[]>
     */
    public static function allowedTransitions(): array
    {
        return [
            self::Todo->value => [self::InProgress->value],
            self::InProgress->value => [self::Done->value],
            self::Done->value => [],
        ];
    }

    public static function isValid(string $status): bool
    {
        return in_array($status, array_column(self::cases(), 'value'), true);
    }
}
