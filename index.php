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
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTION");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Max-Age", "3600");
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
//!Constants 
$Database = new Database(host: $config['db_host'], username: $config['db_user'], password: $config['db_pass'], database: $config['db_name']);

class Controller
{
    function __construct(private array $requestArgs, private Database $Database)
    {
    }
    // GET END POINTS 

    // Leaderboard Endpoint
    public function leaderboardGet(): void
    {
        $category = $this->requestArgs[0] ?? "default";
        $category = strtolower($category);
        $category = explode("?", $category)[0];
        //Gets the leaderboard


        $services = new Services($this->Database);
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
        $services = new Services($this->Database);
        $services->GetUser($id);
    }

    //!POST END POINTS 

    // Register Endpoint
    public function registerPost(): void
    {


        // New User Endpoint
        //Getting the Request Arguments from RequestBody
        $requestArgs = (array) json_decode(file_get_contents(filename: "php://input"), true);
        $userName = $requestArgs['username'] ?? null;
        $name = $requestArgs['name'] ?? null;
        $email = $requestArgs['email'] ?? null;
        $password = $requestArgs['password'] ?? null;
        $dateOfBirth = $requestArgs['dateOfBirth'] ?? null;
        $location = $requestArgs['location'] ?? null;
        $bio = $requestArgs['bio'] ?? null;
        $profilePicture = $requestArgs['profilePicture'] ?? null;
        $profession = $requestArgs['profession'] ?? null;
        throw new Exception("Valid Profession is required", 500);
        //Input Checks
        if (!isset($name) || trim($name) == "") {
            throw new Exception("Valid Name is required", 400);
        } elseif (!isset($userName) || trim($userName) == "" || strlen($userName) < 4) {
            throw new Exception("Valid Username is required", 400);
        } elseif (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Valid Email is required", 400);
        } elseif (!isset($password) || strlen($password) < 8) {
            throw new Exception("Valid Password is required", 400);
        } elseif (!isset($dateOfBirth)) {
            throw new Exception("Valid Date of Birth is required", 400);
        } elseif (!isset($location)) {
            throw new Exception("Valid Location is required", 400);
        } elseif (!isset($bio)) {
            throw new Exception("Valid Bio is required", 400);
        } elseif (!isset($profilePicture)) {
            throw new Exception("Valid Profile Picture is required", 400);
        } elseif (!isset($profession)) {
            throw new Exception("Valid Profession is required", 400);
        }



        $services = new Services($this->Database);
        $services->CreateUser(name: $name, email: $email, password: $password, username: $userName, dateOfBirth: $dateOfBirth, location: $location, bio: $bio, profilePicture: $profilePicture, profession: $profession);

    }
    //Login Endpoint
    public function loginPost(): void
    {
        // Handles info
        // New User Endpoint
        //Getting the Request Arguments from RequestBody
        $requestArgs = (array) json_decode(file_get_contents(filename: "php://input"), true);
        $username = $requestArgs['username'] ?? null;
        $password = $requestArgs['password'] ?? null;
        //Input Checks
        if (!isset($username) || trim($username) == "") {
            throw new Exception("Valid username is required", 400);
        } elseif (!isset($password) || strlen($password) < 8) {
            throw new Exception("Valid Password is required", 400);
        }
        $services = new Services($this->Database);
        $services->AuthenticateUser($username, $password);
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
        $email = $requestArgs['email'] ?? null;
        $oldPassword = $requestArgs['old_password'] ?? null;
        $password = $requestArgs['password'] ?? null;

        //Input Checks
        if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Valid email is required", 400);
        } elseif (!isset($password) || strlen($password) < 8) {
            throw new Exception("Valid password is required", 400);
        } elseif (!isset($oldPassword) || strlen($oldPassword) < 8) {
            throw new Exception("Valid old_password is required", 400);
        }

        $services = new Services($this->Database);
        $services->UpdateUser($id, $email, $oldPassword, $password);

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
        $stats = json_encode($requestArgs['stats'] ?? "");


        //Input Checks
        if (!isset($stats)) {
            throw new Exception("Valid stats is required", 400);
        }
        if (!isValidJson($stats)) {
            throw new Exception("Stats are not in JSON FORMAT", 400);
        }
        $services = new Services($this->Database);
        $services->UpdateStats(id: $id, stats: $stats);
    }

    public function friendsPut(): void
    {
        // Handles friends
        $id = $this->requestArgs[0] ?? null;
        if (!isset($id) || !is_numeric($id)) {
            throw new Exception("Valid ID is required", 400);
        }
        $id = (int) $id;
        $requestArgs = (array) json_decode(file_get_contents(filename: "php://input"), true);
        $friends = json_encode($requestArgs['friends'] ?? "");

        //Input Checks
        if (!isset($friends)) {
            throw new Exception("Valid friends is required", 400);
        }
        if (!isValidJson($friends)) {
            throw new Exception("Friends are not in JSON FORMAT", 400);
        }

        $services = new Services($this->Database);
        $services->UpdateFriends(id: $id, friends: $friends);
    }
    public function targetsPut(): void
    {
        // Handles target
        $id = $this->requestArgs[0] ?? null;
        if (!isset($id) || !is_numeric($id)) {
            throw new Exception("Valid ID is required", 400);
        }
        $id = (int) $id;
        $requestArgs = (array) json_decode(file_get_contents(filename: "php://input"), true);
        $target = json_encode($requestArgs['target'] ?? "");

        //Input Checks
        if (!isset($target)) {
            throw new Exception("Valid target is required", 400);
        }
        if (!isValidJson($target)) {
            throw new Exception("Target is not in JSON FORMAT", 400);
        }

        $services = new Services($this->Database);
        $services->UpdateTarget(id: $id, target: $target);
    }


    //!DELETE END POINTS
    public function usersDelete(): void
    {
        $id = $this->requestArgs[0] ?? null;
        if (!isset($id) || !is_numeric($id)) {
            throw new Exception("Valid ID is required", 400);
        }
        $id = (int) $id;
        $services = new Services($this->Database);
        $services->DeleteUser($id);
    }

}

//!Routing
//Getting the Method and URI 
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = explode("/", trim($requestUri, "/"));
if (isHosting()) {
    //fixing a indexing for hosting 
    $request = $requestUri[0] ?? "none";
    $requestUri = [$requestUri[1] ?? null];
    $controller = new Controller($requestUri, $Database);
} else {
    $request = $requestUri[1] ?? "none";
    $requestUri = [$requestUri[2] ?? null];
    $controller = new Controller($requestUri, $Database);
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
    case "OPTIONS":
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
        } elseif ($request == "friends") {
            $controller->friendsPut();
        } elseif ($request == "targets") {
            $controller->targetsPut();
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
        Header("Allow: GET,PUT,POST,DELETE,OPTION");
        throw new Exception("Invalid Request Method", 405);

}
function not_valid_method(): never
{
    $allowedRequests = "GET: users/,/leaderboard" . " | " .
        "Post:/register,/login" . "| " .
        "PUT:/users/,/stats,/friends,/targets" . " | " .
        "DELETE:/users/ ";
    throw new Exception("Invalid Request Type : ONLY ALLOWED : $allowedRequests", 405);

}
function isHosting(): bool
{
    return $_SERVER['SERVER_NAME'] !== "localhost";
}
function isValidJson($string)
{

    if (empty($string))
        return false;
    json_decode($string);

    return (json_last_error() === JSON_ERROR_NONE);
}
?>