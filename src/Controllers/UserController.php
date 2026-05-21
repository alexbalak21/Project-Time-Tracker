<?php

class UserController extends BaseController
{
    protected $resource = 'users';

    protected function validateStore(array $input)
    {
        $errors = array();

        if (empty($input['name'])) {
            $errors['name'] = 'Name is required';
        }

        if (empty($input['email'])) {
            $errors['email'] = 'Email is required';
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
            'email' => trim($input['email']),
            'role' => isset($input['role']) ? trim($input['role']) : null,
            'is_active' => isset($input['is_active']) ? (bool) $input['is_active'] : true,
            'created_at' => date('c'),
            'updated_at' => date('c'),
        );
    }

    protected function prepareForUpdate(array $existing, array $input)
    {
        $record = $existing;

        if (isset($input['name'])) {
            $record['name'] = trim($input['name']);
        }

        if (isset($input['email'])) {
            $record['email'] = trim($input['email']);
        }

        if (array_key_exists('role', $input)) {
            $record['role'] = $input['role'] === null ? null : trim($input['role']);
        }

        if (array_key_exists('is_active', $input)) {
            $record['is_active'] = (bool) $input['is_active'];
        }

        return $record;
    }
}