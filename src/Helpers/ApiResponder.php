<?php

class ApiResponder
{
    public static function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public static function created($data)
    {
        self::json($data, 201);
    }

    public static function error($message, $statusCode = 400, $details = array())
    {
        $payload = array('error' => $message);

        if (!empty($details)) {
            $payload['details'] = $details;
        }

        self::json($payload, $statusCode);
    }
}