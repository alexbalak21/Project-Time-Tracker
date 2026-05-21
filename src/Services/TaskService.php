<?php

class TaskService extends BaseService
{
    protected function makeRepository()
    {
        return new TaskRepository();
    }

    protected function createDto(array $input)
    {
        return TaskDto::fromCreate($input);
    }

    protected function updateDto(array $existing, array $input)
    {
        return TaskDto::fromUpdate($existing, $input);
    }

    protected function validateCreate(array $data)
    {
        $errors = array();

        if (trim((string) $data['name']) === '') {
            $errors['name'] = 'Name is required';
        }

        $isGeneric = !empty($data['is_generic']);
        $projectId = isset($data['project_id']) && $data['project_id'] !== null ? (int) $data['project_id'] : null;

        if ($isGeneric && $projectId !== null) {
            $errors['project_id'] = 'Generic tasks cannot belong to a project';
        }

        if (!$isGeneric && $projectId === null) {
            $errors['project_id'] = 'Project-specific tasks require a project_id';
        }

        return array('valid' => empty($errors), 'errors' => $errors);
    }

    protected function validateUpdate(array $data, array $existing)
    {
        return $this->validateCreate($data);
    }

    protected function filterCollection(array $items, array $query)
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

    protected function labelValue()
    {
        return 'Task';
    }
}