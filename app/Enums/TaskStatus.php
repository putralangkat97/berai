<?php

namespace App\Enums;

enum TaskStatus: int
{
    case TODO = 1;
    case IN_PROGRESS = 2;
    case COMPLETED = 3;

    public function message(): string
    {
        return match ($this) {
            self::TODO => 'To-Do',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }

    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            array_push($array, [
                'value' => $case->value,
                'label' => $case->message(),
            ]);
        }

        return $array;
    }
}
