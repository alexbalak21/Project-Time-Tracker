<?php

class UserService extends BaseService
{
    protected function makeRepository()
    {
        return new UserRepository();
    }

    protected function createDto(array $input)
    {
        return UserDto::fromCreate($input);
    }

    protected function updateDto(array $existing, array $input)
    {
        return UserDto::fromUpdate($existing, $input);
    }

    protected function validateCreate(array $data)
    {
        $errors = array();

        if (trim((string) $data['name']) === '') {
            $errors['name'] = 'Name is required';
        }

        if (trim((string) $data['email']) === '') {
            $errors['email'] = 'Email is required';
        }

        return array('valid' => empty($errors), 'errors' => $errors);
    }

    protected function validateUpdate(array $data, array $existing)
    {
        return $this->validateCreate($data);
    }

    protected function labelValue()
    {
        return 'User';
    }
}