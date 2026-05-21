<?php

class UserDto extends BaseDto
{
    public static function fromCreate(array $input)
    {
        return new self(array(
            'name' => isset($input['name']) ? trim($input['name']) : null,
            'email' => isset($input['email']) ? trim($input['email']) : null,
            'role' => isset($input['role']) ? trim($input['role']) : null,
            'is_active' => isset($input['is_active']) ? (bool) $input['is_active'] : true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }

    public static function fromUpdate(array $existing, array $input)
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

        $record['updated_at'] = date('Y-m-d H:i:s');

        return new self($record);
    }
}