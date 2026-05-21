<?php

class TimeEntryDto extends BaseDto
{
    public static function fromCreate(array $input)
    {
        return new self(array(
            'user_id' => isset($input['user_id']) ? (int) $input['user_id'] : null,
            'project_id' => isset($input['project_id']) ? (int) $input['project_id'] : null,
            'task_id' => isset($input['task_id']) ? (int) $input['task_id'] : null,
            'date' => isset($input['date']) ? $input['date'] : null,
            'duration_minutes' => isset($input['duration_minutes']) ? (int) $input['duration_minutes'] : null,
            'notes' => isset($input['notes']) ? $input['notes'] : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }

    public static function fromUpdate(array $existing, array $input)
    {
        $record = $existing;

        foreach (array('user_id', 'project_id', 'task_id', 'date', 'duration_minutes', 'notes') as $field) {
            if (!array_key_exists($field, $input)) {
                continue;
            }

            if (in_array($field, array('user_id', 'project_id', 'task_id', 'duration_minutes'), true)) {
                $record[$field] = (int) $input[$field];
            } else {
                $record[$field] = $input[$field];
            }
        }

        $record['updated_at'] = date('Y-m-d H:i:s');

        return new self($record);
    }
}