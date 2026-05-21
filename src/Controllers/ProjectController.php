<?php

class ProjectController extends BaseController
{
    protected $resource = 'projects';

    protected function validateStore(array $input)
    {
        $errors = array();

        if (empty($input['name'])) {
            $errors['name'] = 'Name is required';
        }

        if (!empty($input['status']) && !in_array($input['status'], array('active', 'on_hold', 'completed'), true)) {
            $errors['status'] = 'Status must be active, on_hold, or completed';
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
            'name' => trim($input['name']),
            'code' => isset($input['code']) ? trim($input['code']) : null,
            'description' => isset($input['description']) ? $input['description'] : null,
            'status' => isset($input['status']) ? $input['status'] : 'active',
            'start_date' => isset($input['start_date']) ? $input['start_date'] : null,
            'end_date' => isset($input['end_date']) ? $input['end_date'] : null,
            'owner_user_id' => isset($input['owner_user_id']) ? (int) $input['owner_user_id'] : null,
            'client_name' => isset($input['client_name']) ? $input['client_name'] : null,
            'color' => isset($input['color']) ? $input['color'] : null,
            'created_at' => date('c'),
            'updated_at' => date('c'),
        );
    }

    protected function prepareForUpdate(array $existing, array $input)
    {
        $record = $existing;

        foreach (array('name', 'code', 'description', 'status', 'start_date', 'end_date', 'client_name', 'color') as $field) {
            if (array_key_exists($field, $input)) {
                $record[$field] = $input[$field];
            }
        }

        if (array_key_exists('owner_user_id', $input)) {
            $record['owner_user_id'] = $input['owner_user_id'] === null ? null : (int) $input['owner_user_id'];
        }

        return $record;
    }
}