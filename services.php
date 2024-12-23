<?php

//UserClass
class User
{

    private string $password;
    public array $stats;
    public array $target;
    public function __construct(private int $id, private string $username, private string $email, string $password, array $stats = [], array $target = [])
    {
        $this->password = defaultHash($password);
        $this->stats = $stats;
        $this->target = $target;
    }


    //!Getting User As Associative Array 

    function getArray(): array
    {

        return ['id' => $this->id, 'name' => $this->username, 'email' => $this->email, 'password' => $this->password, 'stats' => $this->stats, 'target' => $this->target];
    }
    //?Getting User as JSON
    function getJson(): string
    {
        return json_encode($this->getArray());
    }
    public static function toArray(int $id, string $userName, string $email, string $password, array $stats = [], array $target = []): array
    {
        return [
            'id' => $id,
            'name' => $userName,
            'email' => $email,
            'password' => defaultHash($password),
            'stats' => $stats,
            'target' => $target
        ];
    }
    public static function toJson(int $id, string $userName, string $email, string $password, array $stats = [], array $target = []): string
    {
        return json_encode(self::toArray($id, $userName, $email, $password, $stats, $target));
    }
}

//?Default hash function
function defaultHash(string $password): string
{
    return hash('sha256', $password);
}

class Services
{
    function __construct(private Database $database)
    {
    }
    public function CreateUser(string $name, string $email, string $password, string $username, string $dateOfBirth, string $location, string $bio, string $profilePicture, string $profession): void
    {

        //Already Exists
        if ($this->database->IfUserExists($username)) {
            throw new Exception("User Already Exists", 400);
        }

        //Create User
        $userId = $this->UserNameToUserID($username);
        $createdOn = date("Y-m-d H:i:s");
        $lastLogin = date("Y-m-d H:i:s");
        $this->database->AddToAuthTable($userId, $username, defaultHash($password), $createdOn, $lastLogin, $email);
        $this->database->AddToInfoTable($userId, $username, $name, $dateOfBirth, $profilePicture, $bio, $location, $profession);
        echo json_encode("User Created Successfully with id " . $userId);
        echo json_encode($this->database->GetUserInfo($userId));

    }
    public function AuthenticateUser(string $userName, string $password): void
    {
        if (!$this->database->IfUserExists($userName)) {
            throw new Exception("Invalid Credentials:NO USER FOUND", 400);
        }
        $authInfo = $this->database->GetUserAuthInfo($this->UserNameToUserID($userName));
        if ($authInfo === null) {
            throw new Exception("Invalid Credentials:NO USER FOUND", 400);
        }
        if ($authInfo['_Password'] !== defaultHash($password)) {
            throw new Exception("Invalid Credentials:WRONG PASSWORD", 400);
        }
        $this->database->successfulLogin($authInfo['_UserId']);
        echo json_encode("User Authenticated Successfully");
        echo json_encode($this->database->GetUserInfo($authInfo['_UserId']));

    }

    public function isValidUser(int $id, string $password): bool
    {
        $authInfo = $this->database->GetUserAuthInfo($id);

        if ($authInfo === null) {
            return false;
        }

        if ($authInfo['_Password'] !== defaultHash($password)) {
            return false;
        }
        return true;
    }
    public function UpdateUser(string $UserID, string $email, $oldPassword, string $password): void
    {
        if (!$this->isValidUser($UserID, $oldPassword)) {
            throw new Exception("Invalid Credentials:WRONG PASSWORD", 400);
        }

        $this->database->UpdateUser($UserID, $email, defaultHash($password));
        echo json_encode("User Updated Successfully");
        echo json_encode($this->database->GetUserAuthInfo($UserID));
        echo json_encode($this->database->GetUserInfo($UserID));

    }
    public function DeleteUser(int $id): void
    {
        $this->database->DeleteUser($id);
        echo json_encode("User Deleted Successfully : User ID " . $id);
    }
    public function UpdateStats(int $id, string $stats): void
    {
        $this->database->SetStats($id, $stats);
        echo json_encode("Stats Updated Successfully");
        echo json_encode($this->database->GetUserInfo($id));
    }
    public function UpdateFriends(int $id, string $friends): void
    {
        $this->database->SetFriends($id, $friends);
        echo json_encode("Friends Updated Successfully");
        echo json_encode($this->database->GetUserInfo($id));
    }
    public function UpdateTarget(int $id, string $target): void
    {
        $this->database->SetTargets($id, $target);
        echo json_encode("Target Updated Successfully");
        echo json_encode($this->database->GetUserInfo($id));
    }

    public function GetUser(int $id): void
    {
        echo json_encode($this->database->GetUserInfo($id));

    }
    public function GetLeaderBoard(string $category = "default"): void
    {
        echo json_encode(" LeaderBoard Fetched Successfully");
        echo json_encode($this->database->GetLeaderBoard($category));

    }

    function UserNameToUserID($string): int
    {
        $map = array_flip(range('a', 'z')); // Map 'a' => 0, 'b' => 1, ..., 'z' => 25
        $result = 0;
        foreach (str_split(strtolower($string)) as $char) {
            $result = $result * 26 + (is_numeric($char) ? (int) $char : $map[$char]);
        }
        return $result;
    }

}