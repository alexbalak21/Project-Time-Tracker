<?php

require __DIR__ . '/../src/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

if ($uri === '') {
    $uri = '/';
}

if (strpos($uri, '/api') === 0) {
    $resource = trim(str_replace('/api', '', $uri), '/');
    $segments = $resource === '' ? array() : explode('/', $resource);
    $method = $_SERVER['REQUEST_METHOD'];

    $controllers = array(
        'users' => new UserController(),
        'projects' => new ProjectController(),
        'tasks' => new TaskController(),
        'time-entries' => new TimeEntryController(),
    );

    if (count($segments) === 0) {
        ApiResponder::json(array(
            'message' => 'Time Tracker API',
            'resources' => array_keys($controllers),
        ));
        exit;
    }

    $resourceName = $segments[0];

    if (!isset($controllers[$resourceName])) {
        ApiResponder::json(array('error' => 'API endpoint not found'), 404);
        exit;
    }

    $controller = $controllers[$resourceName];
    $id = isset($segments[1]) ? (int) $segments[1] : null;

    if ($method === 'GET' && $id === null) {
        $controller->index($_GET);
        exit;
    }

    if ($method === 'GET' && $id !== null) {
        $controller->show($id);
        exit;
    }

    if ($method === 'POST' && $id === null) {
        $controller->store();
        exit;
    }

    if (in_array($method, array('PUT', 'PATCH'), true) && $id !== null) {
        $controller->update($id);
        exit;
    }

    if ($method === 'DELETE' && $id !== null) {
        $controller->destroy($id);
        exit;
    }

    ApiResponder::json(array('error' => 'Method not allowed'), 405);
    exit;
}

require __DIR__ . '/app/index.php';
exit;
