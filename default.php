<?php
// Set response headers
header("Content-Type: application/json; charset=UTF-8");

// Allow only specific request methods
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = explode("/", trim($_SERVER['REQUEST_URI'], "/"));
$resource = $requestUri[0] ?? ''; // Example: api/resource_name

// Helper function to send JSON responses
function sendResponse($statusCode, $data)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Routing based on HTTP methods
switch ($method) {
    case 'GET':
        handleGet($resource, $_GET);
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        handlePost($resource, $data);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        handlePut($resource, $data);
        break;
    case 'DELETE':
        handleDelete($resource);
        break;
    default:
        sendResponse(405, ["error" => "Method not allowed"]);
}

// Define handler functions
function handleGet($resource, $queryParams)
{
    if ($resource === 'example') {
        // Example GET logic
        sendResponse(200, ["message" => "GET request received", "params" => $queryParams]);
    } else {
        sendResponse(404, ["error" => "Resource not found"]);
    }
}

function handlePost($resource, $data)
{
    if ($resource === 'example') {
        // Example POST logic
        sendResponse(201, ["message" => "POST request received", "data" => $data]);
    } else {
        sendResponse(404, ["error" => "Resource not found"]);
    }
}

function handlePut($resource, $data)
{
    if ($resource === 'example') {
        // Example PUT logic
        sendResponse(200, ["message" => "PUT request received", "data" => $data]);
    } else {
        sendResponse(404, ["error" => "Resource not found"]);
    }
}

function handleDelete($resource)
{
    if ($resource === 'example') {
        // Example DELETE logic
        sendResponse(200, ["message" => "DELETE request received"]);
    } else {
        sendResponse(404, ["error" => "Resource not found"]);
    }
}
