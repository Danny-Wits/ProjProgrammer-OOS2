<?php
class Database
{
    function __construct(private string $host, private string $username, private string $password, private string $database)
    {

    }
    public function connect(): PDO
    {
        $conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->username, $this->password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
    public function query(string $query): PDOStatement
    {
        $conn = $this->connect();
        return $conn->query($query);
    }
    public function prepare(string $query): PDOStatement
    {
        $conn = $this->connect();
        return $conn->prepare($query);
    }
    public function IfUserExists(string $username): bool
    {
        $query = "SELECT * FROM AuthTable WHERE _UserName = :username";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':username', $username, PDO::PARAM_STR);
        $preparedStatement->execute();
        $result = $preparedStatement;
        return $result->rowCount() > 0;
    }
    public function AddToAuthTable($UserId, $UserName, $Password, $CreatedOn, $LastLogin, $Email): void
    {
        $query = "INSERT INTO AuthTable (_UserId, _UserName, _Password, _CreatedOn, _LastLogin, _Email) VALUES (:UserId, :UserName, :Password, :CreatedOn, :LastLogin, :Email)";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':UserId', $UserId, PDO::PARAM_INT);
        $preparedStatement->bindParam(':UserName', $UserName, PDO::PARAM_STR);
        $preparedStatement->bindParam(':Password', $Password, PDO::PARAM_STR);
        $preparedStatement->bindParam(':CreatedOn', $CreatedOn, PDO::PARAM_STR);
        $preparedStatement->bindParam(':LastLogin', $LastLogin, PDO::PARAM_STR);
        $preparedStatement->bindParam(':Email', $Email, PDO::PARAM_STR);
        $preparedStatement->execute();
    }


    public function AddToInfoTable($UserId, $UserName, $Name, $DateOfBirth, $ProfilePictureUrl, $Bio, $Location, $Profession): void
    {
        $query = "INSERT INTO InfoTable (_UserId,_UserName, _Name, _DateOfBirth, _ProfilePictureUrl, _Bio, _Location, _Profession) VALUES (:UserId,:UserName, :Name, :DateOfBirth, :ProfilePictureUrl, :Bio, :Location, :Profession)";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':UserId', $UserId, PDO::PARAM_INT);
        $preparedStatement->bindParam(':UserName', $UserName, PDO::PARAM_STR);
        $preparedStatement->bindParam(':Name', $Name, PDO::PARAM_STR);
        $preparedStatement->bindParam(':DateOfBirth', $DateOfBirth, PDO::PARAM_STR);
        $preparedStatement->bindParam(':ProfilePictureUrl', $ProfilePictureUrl, PDO::PARAM_STR);
        $preparedStatement->bindParam(':Bio', $Bio, PDO::PARAM_STR);
        $preparedStatement->bindParam(':Location', $Location, PDO::PARAM_STR);
        $preparedStatement->bindParam(':Profession', $Profession, PDO::PARAM_STR);
        $preparedStatement->execute();
    }

    public function GetUserInfo(int $userId): array
    {
        $query = "SELECT * FROM InfoTable WHERE _UserId = :userId";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $preparedStatement->execute();
        $result = $preparedStatement->fetch(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    public function GetUserAuthInfo(int $userId): array
    {
        $query = "SELECT * FROM AuthTable WHERE _UserId = :userId";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $preparedStatement->execute();
        $result = $preparedStatement->fetch(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    public function successfulLogin(int $userId): void
    {
        $query = "UPDATE AuthTable SET _LastLogin = :lastLogin WHERE _UserId = :userId";
        $lastLogin = date("Y-m-d H:i:s");
        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $preparedStatement->bindParam(':lastLogin', $lastLogin, PDO::PARAM_STR);
        $preparedStatement->execute();
    }
    public function deleteUser(int $userId): void
    {
        $query = "SET FOREIGN_KEY_CHECKS = 0;
        DELETE FROM AuthTable WHERE _UserId = :userId; 
        SET FOREIGN_KEY_CHECKS = 1;";

        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $preparedStatement->execute();
        $query = "DELETE FROM InfoTable WHERE _UserId = :userId";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $preparedStatement->execute();

    }
    public function SetStats(int $userId, string $stats): void
    {
        $query = "Update InfoTable SET _Stats = :stats WHERE _UserId = :userId;";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $preparedStatement->bindParam(':stats', $stats, PDO::PARAM_STR);
        $preparedStatement->execute();
    }
    //update user in Authtable
    public function UpdateUser(int $userId, string $email, string $password): void
    {
        $query = "update AuthTable SET _Email = :email, _Password = :password WHERE _UserId = :userId;";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $preparedStatement->bindParam(':email', $email, PDO::PARAM_STR);
        $preparedStatement->bindParam(':password', $password, PDO::PARAM_STR);
        $preparedStatement->execute();
    }
    public function SetFriends(int $userId, string $friends): void
    {
        $query = "update InfoTable SET _Friends = :friends WHERE _UserId = :userId;";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $preparedStatement->bindParam(':friends', $friends, PDO::PARAM_STR);
        $preparedStatement->execute();
    }
    public function SetTargets(int $userId, string $targets): void
    {
        $query = "update InfoTable SET _Targets = :targets WHERE _UserId = :userId;";

        $preparedStatement = $this->prepare($query);
        $preparedStatement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $preparedStatement->bindParam(':targets', $targets, PDO::PARAM_STR);
        $preparedStatement->execute();
    }
    public function GetLeaderBoard(string $category = "default"): array
    {
        $query = "SELECT _UserName,_Score,RANK() OVER (ORDER BY _Score DESC) AS _Rank FROM InfoTable ";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->execute();
        $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    public function GetUserList(): array
    {
        $query = "SELECT _UserName FROM AuthTable;";
        $preparedStatement = $this->prepare($query);
        $preparedStatement->execute();
        $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $users[] = $row['_UserName'];
        }
        return $users ?: [];

    }
}