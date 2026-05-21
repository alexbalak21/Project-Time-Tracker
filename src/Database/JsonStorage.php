<?php

class JsonStorage
{
    private $basePath;

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ?: dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data';
    }

    public function all($resource)
    {
        $data = $this->read($resource);
        return $data['items'];
    }

    public function find($resource, $id)
    {
        $items = $this->all($resource);

        foreach ($items as $item) {
            if ((int) $item['id'] === (int) $id) {
                return $item;
            }
        }

        return null;
    }

    public function create($resource, array $record)
    {
        $data = $this->read($resource);
        $record['id'] = $data['next_id'];
        $data['next_id']++;
        $data['items'][] = $record;
        $this->write($resource, $data);

        return $record;
    }

    public function update($resource, $id, array $attributes)
    {
        $data = $this->read($resource);

        foreach ($data['items'] as $index => $item) {
            if ((int) $item['id'] === (int) $id) {
                $data['items'][$index] = array_merge($item, $attributes, array('id' => (int) $id));
                $this->write($resource, $data);
                return $data['items'][$index];
            }
        }

        return null;
    }

    public function delete($resource, $id)
    {
        $data = $this->read($resource);
        $deleted = false;

        foreach ($data['items'] as $index => $item) {
            if ((int) $item['id'] === (int) $id) {
                unset($data['items'][$index]);
                $deleted = true;
                break;
            }
        }

        if ($deleted) {
            $data['items'] = array_values($data['items']);
            $this->write($resource, $data);
        }

        return $deleted;
    }

    private function read($resource)
    {
        $file = $this->pathFor($resource);

        if (!file_exists($file)) {
            $data = array('next_id' => 1, 'items' => array());
            $this->write($resource, $data);
            return $data;
        }

        $contents = file_get_contents($file);
        $decoded = json_decode($contents, true);

        if (!is_array($decoded) || !isset($decoded['next_id']) || !isset($decoded['items'])) {
            $decoded = array('next_id' => 1, 'items' => array());
            $this->write($resource, $decoded);
        }

        return $decoded;
    }

    private function write($resource, array $data)
    {
        $directory = $this->basePath;

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($this->pathFor($resource), json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    }

    private function pathFor($resource)
    {
        return $this->basePath . DIRECTORY_SEPARATOR . $resource . '.json';
    }
}