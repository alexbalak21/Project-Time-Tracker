<?php

class TaskDto extends BaseDto
{
    public static function fromCreate(array $input)
    {
        $isGeneric = isset($input['is_generic']) ? (bool) $input['is_generic'] : false;

        return new self(array(
            'name' => isset($input['name']) ? trim($input['name']) : null,
            'description' => isset($input['description']) ? $input['description'] : null,
            'user_id' => isset($input['user_id']) ? (int) $input['user_id'] : null,
            'project_id' => isset($input['project_id']) && $input['project_id'] !== '' ? (int) $input['project_id'] : null,
            'is_generic' => $isGeneric,
            'is_active' => isset($input['is_active']) ? (bool) $input['is_active'] : true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }

    public static function fromUpdate(array $existing, array $input)
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

        $record['updated_at'] = date('Y-m-d H:i:s');

        return new self($record);
    }
}