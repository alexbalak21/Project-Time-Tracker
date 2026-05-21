<?php

class TaskController extends BaseController
{
    protected $resource = 'tasks';

    protected function filterIndex(array $items, array $query)
    {
        if (!isset($query['project_id']) || $query['project_id'] === '') {
            return $items;
        }

        $projectId = (int) $query['project_id'];
        $filtered = array();

        foreach ($items as $item) {
            if (!empty($item['is_generic'])) {
                $filtered[] = $item;
                continue;
            }

            if (isset($item['project_id']) && (int) $item['project_id'] === $projectId) {
                $filtered[] = $item;
            }
        }

        return array_values($filtered);
    }

    protected function validateStore(array $input)
    {
        $errors = array();

        if (empty($input['name'])) {
            $errors['name'] = 'Name is required';
        }

        $isGeneric = isset($input['is_generic']) ? (bool) $input['is_generic'] : false;
        $projectId = isset($input['project_id']) && $input['project_id'] !== '' ? (int) $input['project_id'] : null;

        if ($isGeneric && $projectId !== null) {
            $errors['project_id'] = 'Generic tasks cannot belong to a project';
        }

        if (!$isGeneric && $projectId === null) {
            $errors['project_id'] = 'Project-specific tasks require a project_id';
        }

        return array('valid' => empty($errors), 'errors' => $errors);
    }

    protected function validateUpdate(array $input, array $existing)
    {
        return $this->validateStore($input + $existing);
    }

    protected function prepareForCreate(array $input)
    {
        $isGeneric = isset($input['is_generic']) ? (bool) $input['is_generic'] : false;

        return array(
            'name' => trim($input['name']),
            'description' => isset($input['description']) ? $input['description'] : null,
            'user_id' => isset($input['user_id']) ? (int) $input['user_id'] : null,
            'project_id' => isset($input['project_id']) && $input['project_id'] !== '' ? (int) $input['project_id'] : null,
            'is_generic' => $isGeneric,
            'is_active' => isset($input['is_active']) ? (bool) $input['is_active'] : true,
            'created_at' => date('c'),
            'updated_at' => date('c'),
        );
    }

    protected function prepareForUpdate(array $existing, array $input)
    {
        $record = $existing;

        foreach (array('name', 'description') as $field) {
            if (array_key_exists($field, $input)) {
                $record[$field] = $input[$field];
            }
        }

        if (array_key_exists('user_id', $input)) {
            $record['user_id'] = $input['user_id'] === null ? null : (int) $input['user_id'];
        }

        if (array_key_exists('project_id', $input)) {
            $record['project_id'] = $input['project_id'] === '' || $input['project_id'] === null ? null : (int) $input['project_id'];
        }

        if (array_key_exists('is_generic', $input)) {
            $record['is_generic'] = (bool) $input['is_generic'];
        }

        if (array_key_exists('is_active', $input)) {
            $record['is_active'] = (bool) $input['is_active'];
        }

        return $record;
    }
}