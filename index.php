<?php
//set false in production
$dev = true;
// Include the Controller, Database and Services
require("database.php");
require("services.php");
require("errorHandlers.php");

//This must be updated manually on the server  as it contains sensitive information so this cannot be uploaded to github  
$config = require("config.php");

//Setting up the header
header("Content-Type: application/json");

//Setting up custom error handler and exception handler
set_error_handler("CustomErrorHandler::errorHandler", E_ALL);
set_exception_handler("CustomErrorHandler::exceptionHandler");

//Checking for authentication
if (!$dev) {

    $authorization = getallheaders()['Authorization'] ?? null;
    if (!isset($authorization) || $authorization !== $config['api_key']) {
        throw new Exception("Unauthorized", 401);
    }
}


class Controller
{
    function __construct(private array $requestArgs)
    {
    }
    //! GET END POINTS 

    // Leaderboard Endpoint
    public function leaderboardGet(): void
    {
        $category = $this->requestArgs[0] ?? "default";
        $category = strtolower($category);
        $category = explode("?", $category)[0];
        //Gets the leaderboard
        $services = new Services();
        $services->GetLeaderboard($category);
    }
    //User Endpoint
    public function usersGet(): void
    {
        //Gets  stats and targets  for a user
        $id = $this->requestArgs[0] ?? null;
        if (isset($id) && is_numeric($id)) {
            $id = (int) $id;
        } else {
            throw new Exception("Valid ID is required", 400);
        }
        $services = new Services();
        $services->GetUser($id);
    }

    //!POST END POINTS 

    // Register Endpoint
    public function registerPost(): void
    {

        // New User Endpoint
        //Getting the Request Arguments from RequestBody
        $requestArgs = (array) json_decode(file_get_contents(filename: "php://input"), true);
        $name = $requestArgs['name'] ?? null;
        $email = $requestArgs['email'] ?? null;
        $password = $requestArgs['password'] ?? null;

        //Input Checks
        if (!isset($name) || trim($name) == "") {
            throw new Exception("Valid Name is required", 400);
        } elseif (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Valid Email is required", 400);
        } elseif (!isset($password) || strlen($password) < 8) {
            throw new Exception("Valid Password is required", 400);
        }

        $services = new Services();
        $services->CreateUser($name, $email, $password);

    }
    //Login Endpoint
    public function loginPost(): void
    {
        // Handles info
        // New User Endpoint
        //Getting the Request Arguments from RequestBody
        $requestArgs = (array) json_decode(file_get_contents(filename: "php://input"), true);
        $name = $requestArgs['name'] ?? null;
        $password = $requestArgs['password'] ?? null;
        //Input Checks
        if (!isset($name) || trim($name) == "") {
            throw new Exception("Valid Name is required", 400);
        } elseif (!isset($password) || strlen($password) < 8) {
            throw new Exception("Valid Password is required", 400);
        }
        $services = new Services();
        $services->AuthenticateUser($name, $password);
    }

    //!PUT END POINTS
    public function usersPut(): void
    {
        $id = $this->requestArgs[0] ?? null;
        if (!isset($id) || !is_numeric($id)) {
            throw new Exception("Valid ID is required", 400);
        }
        $id = (int) $id;
        $requestArgs = (array) json_decode(file_get_contents(filename: "php://input"), true);
        $name = $requestArgs['name'] ?? null;
        $email = $requestArgs['email'] ?? null;
        $password = $requestArgs['password'] ?? null;

        //Input Checks
        if (!isset($name) || trim($name) == "") {
            throw new Exception("Valid Name is required", 400);
        } elseif (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Valid Email is required", 400);
        } elseif (!isset($password) || strlen($password) < 8) {
            throw new Exception("Valid Password is required", 400);
        }

        $services = new Services();
        $services->UpdateUser($id, $name, $email, $password);

    }
    public function statsPut(): void
    {
        // Handles stats
        $id = $this->requestArgs[0] ?? null;
        if (!isset($id) || !is_numeric($id)) {
            throw new Exception("Valid ID is required", 400);
        }
        $id = (int) $id;
        $requestArgs = (array) json_decode(file_get_contents(filename: "php://input"), true);
        $stats = $requestArgs['stats'] ?? null;

        //Input Checks
        if (!isset($stats)) {
            throw new Exception("Valid stats is required", 400);
        }

        $services = new Services();
        $services->UpdateStats(id: $id, stats: $stats, );
    }
    //!DELETE END POINTS
    public function usersDelete(): void
    {
        $id = $this->requestArgs[0] ?? null;
        if (!isset($id) || !is_numeric($id)) {
            throw new Exception("Valid ID is required", 400);
        }
        $id = (int) $id;
        $services = new Services();
        $services->DeleteUser($id);
    }

}
//!Constants 

//!Routing
//Getting the Method and URI 
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = explode("/", trim($requestUri, "/"));
if (isHosting()) {
    //fixing a indexing for hosting 
    $request = $requestUri[0] ?? "none";
    $requestUri = [$requestUri[1]];
    $controller = new Controller($requestUri);
} else {
    $request = $requestUri[1] ?? "none";
    $requestUri = [$requestUri[2]];
    $controller = new Controller($requestUri);
}


switch ($requestMethod) {
    case "GET":
        if ($request == "users") {
            $controller->usersGet();
        } elseif ($request == "leaderboard") {
            $controller->leaderboardGet();
        } else {
            not_valid_method();
        }
        break;
    case "POST":
        if ($request == "register") {
            $controller->registerPost();
        } elseif ($request == "login") {
            $controller->loginPost();
        } else {
            not_valid_method();
        }
        break;
    case "PUT":
        if ($request == "users") {
            $controller->usersPut();
        } elseif ($request == "stats") {
            $controller->statsPut();
        } else {
            not_valid_method();
        }
        break;
    case "DELETE":
        if ($request == "users") {
            $controller->usersDelete();
        } else {
            not_valid_method();
        }
        break;
    default:
        Header("HTTP/1.1 405 Method Not Allowed");
        Header("Allow: GET,PUT,POST,DELETE");
        throw new Exception("Invalid Request Method");

}
function not_valid_method(): never
{
    $allowedRequests = "/users,/leaderboard,/login,/register,/stats";
    throw new Exception("Invalid Request Type : ONLY ALLOWED : $allowedRequests", 405);

}
function isHosting(): bool
{
    return $_SERVER['SERVER_NAME'] !== "localhost";
}
?>