<?php

abstract class BaseController
{
    protected $storage;
    protected $resource;

    public function __construct()
    {
        $this->storage = new JsonStorage();
    }

    public function index($query = array())
    {
        $items = $this->storage->all($this->resource);
        ApiResponder::json(array('data' => $this->filterIndex($items, $query)));
    }

    public function show($id)
    {
        $item = $this->storage->find($this->resource, $id);

        if ($item === null) {
            ApiResponder::error(ucfirst($this->resource) . ' not found', 404);
            return;
        }

        ApiResponder::json(array('data' => $item));
    }

    public function store()
    {
        $input = $this->input();
        $validation = $this->validateStore($input);

        if (!$validation['valid']) {
            ApiResponder::error('Validation failed', 422, $validation['errors']);
            return;
        }

        $record = $this->prepareForCreate($input);

        if (!isset($record['created_at'])) {
            $record['created_at'] = date('c');
        }

        if (!isset($record['updated_at'])) {
            $record['updated_at'] = date('c');
        }

        $created = $this->storage->create($this->resource, $record);
        ApiResponder::created(array('data' => $created));
    }

    public function update($id)
    {
        $existing = $this->storage->find($this->resource, $id);

        if ($existing === null) {
            ApiResponder::error(ucfirst($this->resource) . ' not found', 404);
            return;
        }

        $input = $this->input();
        $merged = array_merge($existing, $input);
        $validation = $this->validateUpdate($merged, $existing);

        if (!$validation['valid']) {
            ApiResponder::error('Validation failed', 422, $validation['errors']);
            return;
        }

        $record = $this->prepareForUpdate($existing, $input);
        $record['updated_at'] = date('c');
        $updated = $this->storage->update($this->resource, $id, $record);
        ApiResponder::json(array('data' => $updated));
    }

    public function destroy($id)
    {
        $deleted = $this->storage->delete($this->resource, $id);

        if (!$deleted) {
            ApiResponder::error(ucfirst($this->resource) . ' not found', 404);
            return;
        }

        ApiResponder::json(array('message' => ucfirst($this->resource) . ' deleted'));
    }

    protected function input()
    {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        if (!empty($_POST)) {
            return $_POST;
        }

        return array();
    }

    protected function filterIndex(array $items, array $query)
    {
        return $items;
    }

    protected function validateStore(array $input)
    {
        return array('valid' => true, 'errors' => array());
    }

    protected function validateUpdate(array $input, array $existing)
    {
        return array('valid' => true, 'errors' => array());
    }

    protected function prepareForCreate(array $input)
    {
        return $input;
    }

    protected function prepareForUpdate(array $existing, array $input)
    {
        return array_merge($existing, $input);
    }
}