<?php

abstract class BaseService
{
    protected $repository;

    public function __construct(BaseRepository $repository = null)
    {
        $this->repository = $repository ?: $this->makeRepository();
    }

    public function all(array $query = array())
    {
        return $this->filterCollection($this->repository->all(), $query);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $input)
    {
        $dto = $this->createDto($input);
        $data = $dto->toArray();
        $validation = $this->validateCreate($data);

        if (!$validation['valid']) {
            throw new ValidationException('Validation failed', $validation['errors']);
        }

        return $this->repository->create($data);
    }

    public function update($id, array $input)
    {
        $existing = $this->repository->find($id);

        if ($existing === null) {
            throw new ResourceNotFoundException($this->label() . ' not found');
        }

        $dto = $this->updateDto($existing, $input);
        $data = $dto->toArray();
        $validation = $this->validateUpdate($data, $existing);

        if (!$validation['valid']) {
            throw new ValidationException('Validation failed', $validation['errors']);
        }

        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function label()
    {
        return $this->labelValue();
    }

    protected function filterCollection(array $items, array $query)
    {
        return $items;
    }

    protected function labelValue()
    {
        return 'Resource';
    }

    abstract protected function makeRepository();

    abstract protected function createDto(array $input);

    abstract protected function updateDto(array $existing, array $input);

    abstract protected function validateCreate(array $data);

    abstract protected function validateUpdate(array $data, array $existing);
}