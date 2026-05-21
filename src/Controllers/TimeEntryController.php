<?php

class TimeEntryController extends BaseController
{
    protected $resource = 'time-entries';

    protected function filterIndex(array $items, array $query)
    {
        $filtered = $items;

        foreach (array('user_id', 'project_id', 'task_id', 'date') as $field) {
            if (!isset($query[$field]) || $query[$field] === '') {
                continue;
            }

            $value = $query[$field];
            $filtered = array_values(array_filter($filtered, function ($item) use ($field, $value) {
                return isset($item[$field]) && (string) $item[$field] === (string) $value;
            }));
        }

        return $filtered;
    }

    protected function validateStore(array $input)
    {
        $errors = array();

        foreach (array('user_id', 'project_id', 'task_id', 'date', 'duration_minutes') as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        if (isset($input['duration_minutes']) && (int) $input['duration_minutes'] < 0) {
            $errors['duration_minutes'] = 'Duration must be zero or greater';
        }

        return array('valid' => empty($errors), 'errors' => $errors);
    }

    protected function validateUpdate(array $input, array $existing)
    {
        return $this->validateStore($input + $existing);
    }

    protected function prepareForCreate(array $input)
    {
        return array(
            'user_id' => (int) $input['user_id'],
            'project_id' => (int) $input['project_id'],
            'task_id' => (int) $input['task_id'],
            'date' => $input['date'],
            'duration_minutes' => (int) $input['duration_minutes'],
            'notes' => isset($input['notes']) ? $input['notes'] : null,
            'created_at' => date('c'),
            'updated_at' => date('c'),
        );
    }

    protected function prepareForUpdate(array $existing, array $input)
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

        return $record;
    }
}