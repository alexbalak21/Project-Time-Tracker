<?php

abstract class BaseController
{
    protected $serviceClass;
    protected $service;

    public function __construct()
    {
        if (!empty($this->serviceClass)) {
            $serviceClass = $this->serviceClass;
            $this->service = new $serviceClass();
        }
    }

    public function index($query = array())
    {
        ApiResponder::json(array('data' => $this->service->all($query)));
    }

    public function show($id)
    {
        $item = $this->service->find($id);

        if ($item === null) {
            ApiResponder::error($this->service->label() . ' not found', 404);
            return;
        }

        ApiResponder::json(array('data' => $item));
    }

    public function store()
    {
        try {
            $created = $this->service->create($this->input());
            ApiResponder::created(array('data' => $created));
        } catch (ValidationException $exception) {
            ApiResponder::error('Validation failed', 422, $exception->getErrors());
        } catch (Exception $exception) {
            ApiResponder::error($exception->getMessage(), 500);
        }
    }

    public function update($id)
    {
        try {
            $updated = $this->service->update($id, $this->input());
            ApiResponder::json(array('data' => $updated));
        } catch (ResourceNotFoundException $exception) {
            ApiResponder::error($exception->getMessage(), 404);
        } catch (ValidationException $exception) {
            ApiResponder::error('Validation failed', 422, $exception->getErrors());
        } catch (Exception $exception) {
            ApiResponder::error($exception->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            ApiResponder::error($this->service->label() . ' not found', 404);
            return;
        }

        ApiResponder::json(array('message' => $this->service->label() . ' deleted'));
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
}