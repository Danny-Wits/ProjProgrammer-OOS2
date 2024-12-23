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
    return password_hash($password, PASSWORD_DEFAULT);
}

class Services
{
    public function CreateUser(string $name, string $email, string $password): void
    {

        $id = 1; //TODO : get it from DB later
        echo json_encode("User Created Successfully");
        echo User::toJson($id, $name, $email, $password);

    }
    public function AuthenticateUser(string $userName, string $password): void
    {

        echo json_encode("User Authenticated Successfully");

    }
    public function UpdateUser(int $id, string $name, string $email, string $password): void
    {
        echo json_encode(["id" => $id, "name" => $name, "email" => $email, "password" => $password]);
        echo json_encode("User Updated Successfully");

    }
    public function DeleteUser(int $id): void
    {

        echo json_encode("User Deleted Successfully");

    }
    public function UpdateStats(int $id, array $stats): void
    {
        echo json_encode(["id" => $id, "stats" => $stats]);
        echo json_encode("Stats Updated Successfully");

    }

    public function GetUser(int $id): void
    {
        echo json_encode("User Fetched Successfully");

    }
    public function GetLeaderBoard(string $category = "default"): void
    {

        echo json_encode($category . " LeaderBoard Fetched Successfully");

    }

}