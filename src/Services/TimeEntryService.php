<?php

class TimeEntryService extends BaseService
{
    protected function makeRepository()
    {
        return new TimeEntryRepository();
    }

    protected function createDto(array $input)
    {
        return TimeEntryDto::fromCreate($input);
    }

    protected function updateDto(array $existing, array $input)
    {
        return TimeEntryDto::fromUpdate($existing, $input);
    }

    protected function validateCreate(array $data)
    {
        $errors = array();

        foreach (array('user_id', 'project_id', 'task_id', 'date', 'duration_minutes') as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        if (isset($data['duration_minutes']) && (int) $data['duration_minutes'] < 0) {
            $errors['duration_minutes'] = 'Duration must be zero or greater';
        }

        return array('valid' => empty($errors), 'errors' => $errors);
    }

    protected function validateUpdate(array $data, array $existing)
    {
        return $this->validateCreate($data);
    }

    protected function filterCollection(array $items, array $query)
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

    protected function labelValue()
    {
        return 'Time entry';
    }
}