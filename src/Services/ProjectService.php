<?php

class ProjectService extends BaseService
{
    protected function makeRepository()
    {
        return new ProjectRepository();
    }

    protected function createDto(array $input)
    {
        return ProjectDto::fromCreate($input);
    }

    protected function updateDto(array $existing, array $input)
    {
        return ProjectDto::fromUpdate($existing, $input);
    }

    protected function validateCreate(array $data)
    {
        $errors = array();

        if (trim((string) $data['name']) === '') {
            $errors['name'] = 'Name is required';
        }

        if (!empty($data['status']) && !in_array($data['status'], array('active', 'on_hold', 'completed'), true)) {
            $errors['status'] = 'Status must be active, on_hold, or completed';
        }

        return array('valid' => empty($errors), 'errors' => $errors);
    }

    protected function validateUpdate(array $data, array $existing)
    {
        return $this->validateCreate($data);
    }

    protected function labelValue()
    {
        return 'Project';
    }
}