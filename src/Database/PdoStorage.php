<?php

class PdoStorage
{
    private $pdo;
    private $tableMap;

    public function __construct(PDO $pdo = null)
    {
        $this->pdo = $pdo ?: Database::connection();
        $this->tableMap = array(
            'users' => 'users',
            'projects' => 'projects',
            'tasks' => 'tasks',
            'time-entries' => 'time_entries',
        );
    }

    public function all($resource)
    {
        $statement = $this->pdo->prepare('SELECT * FROM ' . $this->table($resource) . ' ORDER BY id DESC');
        $statement->execute();

        return array_map(array($this, 'normalizeRow'), $statement->fetchAll());
    }

    public function find($resource, $id)
    {
        $statement = $this->pdo->prepare('SELECT * FROM ' . $this->table($resource) . ' WHERE id = :id LIMIT 1');
        $statement->execute(array('id' => (int) $id));
        $row = $statement->fetch();

        return $row ? $this->normalizeRow($row) : null;
    }

    public function create($resource, array $record)
    {
        $record = $this->normalizeForWrite($resource, $record);
        $columns = array_keys($record);
        $placeholders = array_map(function ($column) {
            return ':' . $column;
        }, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table($resource),
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $statement = $this->pdo->prepare($sql);
        $statement->execute($record);

        return $this->find($resource, (int) $this->pdo->lastInsertId());
    }

    public function update($resource, $id, array $attributes)
    {
        $attributes = $this->normalizeForWrite($resource, $attributes);
        unset($attributes['id']);

        if (empty($attributes)) {
            return $this->find($resource, $id);
        }

        $assignments = array();
        foreach (array_keys($attributes) as $column) {
            $assignments[] = $column . ' = :' . $column;
        }

        $attributes['id'] = (int) $id;
        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            $this->table($resource),
            implode(', ', $assignments)
        );

        $statement = $this->pdo->prepare($sql);
        $statement->execute($attributes);

        return $this->find($resource, $id);
    }

    public function delete($resource, $id)
    {
        $statement = $this->pdo->prepare('DELETE FROM ' . $this->table($resource) . ' WHERE id = :id');
        $statement->execute(array('id' => (int) $id));

        return $statement->rowCount() > 0;
    }

    private function table($resource)
    {
        if (!isset($this->tableMap[$resource])) {
            throw new InvalidArgumentException('Unknown resource: ' . $resource);
        }

        return $this->tableMap[$resource];
    }

    private function normalizeRow(array $row)
    {
        foreach (array('is_active', 'is_generic') as $booleanField) {
            if (array_key_exists($booleanField, $row)) {
                $row[$booleanField] = (bool) $row[$booleanField];
            }
        }

        foreach (array('owner_user_id', 'user_id', 'project_id', 'task_id', 'duration_minutes') as $integerField) {
            if (array_key_exists($integerField, $row) && $row[$integerField] !== null) {
                $row[$integerField] = (int) $row[$integerField];
            }
        }

        foreach (array('created_at', 'updated_at') as $dateTimeField) {
            if (array_key_exists($dateTimeField, $row) && $row[$dateTimeField]) {
                $row[$dateTimeField] = date('c', strtotime($row[$dateTimeField]));
            }
        }

        return $row;
    }

    private function normalizeForWrite($resource, array $record)
    {
        $normalized = $record;

        foreach (array('is_active', 'is_generic') as $booleanField) {
            if (array_key_exists($booleanField, $normalized)) {
                $normalized[$booleanField] = (int) (bool) $normalized[$booleanField];
            }
        }

        foreach (array('owner_user_id', 'user_id', 'project_id', 'task_id', 'duration_minutes') as $integerField) {
            if (array_key_exists($integerField, $normalized) && $normalized[$integerField] !== null && $normalized[$integerField] !== '') {
                $normalized[$integerField] = (int) $normalized[$integerField];
            }
        }

        foreach (array('created_at', 'updated_at') as $dateTimeField) {
            if (array_key_exists($dateTimeField, $normalized) && $normalized[$dateTimeField]) {
                $normalized[$dateTimeField] = date('Y-m-d H:i:s', strtotime($normalized[$dateTimeField]));
            }
        }

        if ($resource === 'users' && !array_key_exists('is_active', $normalized)) {
            $normalized['is_active'] = 1;
        }

        return $normalized;
    }
}