<?php

// Get the requested path (without query string)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize (remove trailing slash except root)
$uri = rtrim($uri, '/');
if ($uri === '') $uri = '/';

// --- API ROUTES -------------------------------------------------------------

if (strpos($uri, '/api') === 0) {

    header('Content-Type: application/json');

    // Example: /api/projects
    if ($uri === '/api/projects') {
        require __DIR__ . '/../src/Controllers/ProjectController.php';
        (new ProjectController)->index();
        exit;
    }

    // Example: /api/tasks
    if ($uri === '/api/tasks') {
        require __DIR__ . '/../src/Controllers/TaskController.php';
        (new TaskController)->index();
        exit;
    }

    // Example: /api/time-entries
    if ($uri === '/api/time-entries') {
        require __DIR__ . '/../src/Controllers/TimeEntryController.php';
        (new TimeEntryController)->index();
        exit;
    }

    // Default API 404
    http_response_code(404);
    echo json_encode(["error" => "API endpoint not found"]);
    exit;
}

// --- FRONT-END ROUTE --------------------------------------------------------

// Anything that is NOT /api goes to your front-end
require __DIR__ . '/app/index.php';
exit;
