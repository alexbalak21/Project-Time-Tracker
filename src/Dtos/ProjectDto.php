<?php

class ProjectDto extends BaseDto
{
    public static function fromCreate(array $input)
    {
        return new self(array(
            'name' => isset($input['name']) ? trim($input['name']) : null,
            'code' => isset($input['code']) ? trim($input['code']) : null,
            'description' => isset($input['description']) ? $input['description'] : null,
            'status' => isset($input['status']) ? $input['status'] : 'active',
            'start_date' => isset($input['start_date']) ? $input['start_date'] : null,
            'end_date' => isset($input['end_date']) ? $input['end_date'] : null,
            'owner_user_id' => isset($input['owner_user_id']) ? (int) $input['owner_user_id'] : null,
            'client_name' => isset($input['client_name']) ? $input['client_name'] : null,
            'color' => isset($input['color']) ? $input['color'] : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }

    public static function fromUpdate(array $existing, array $input)
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

        $record['updated_at'] = date('Y-m-d H:i:s');

        return new self($record);
    }
}