<?php

abstract class BaseRepository
{
    protected $storage;

    public function __construct(PdoStorage $storage = null)
    {
        $this->storage = $storage ?: new PdoStorage();
    }

    public function all()
    {
        return $this->storage->all($this->resource());
    }

    public function find($id)
    {
        return $this->storage->find($this->resource(), $id);
    }

    public function create(array $record)
    {
        return $this->storage->create($this->resource(), $record);
    }

    public function update($id, array $record)
    {
        return $this->storage->update($this->resource(), $id, $record);
    }

    public function delete($id)
    {
        return $this->storage->delete($this->resource(), $id);
    }

    abstract protected function resource();
}