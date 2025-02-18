<?php

use PHPUnit\Framework\TestCase;
use Config\Database;
use App\Class\UploadImg;
use App\Controllers\SAdminController;

final class TestUpdateUser extends TestCase {

    private $controller;
    private $uploadMock;

    public function setUp(): void {
        $_SESSION["role"] = "super-admin";
        $_SESSION["training"] = "1";
        $_SESSION["id"] = 17;
        $this->uploadMock = $this->createMock(UploadImg::class);
        $this->uploadMock->method('upload')->willReturn(true);
        $this->controller = new SAdminController($this->uploadMock);
        $_FILES = ["picture" => ["name" => "leNom"]];
        define('PHPUNIT_RUNNING', true);
        DataBase::getInstance()->exec("INSERT INTO Users (idUser, login, firstName, lastName, typePwd, pwd, role, idTraining)
                                    VALUES (4000, 'fred.loc', 'Fred', 'Loc', 2, '123456', 'student',1)");
    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM Users WHERE idUser = 4000");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);
    }

    public function testSuccess() {
        $_POST= [
            "idUser" => "4000", 
            "login"  => "fred.loc", //modif login
            "lastName"  => "Pierre", //modif lastName
            "firstName"  => "Robert",  //modif firstName
            "typePwd"  => "2", 
            "pwd"  => "123456",
            "verifPwd" => "123456", 
            "role"  => "student", 
            "idTraining"  => "1" 
        ];
        $this->controller->update_user();
        $res = Database::getInstance()->query("SELECT * FROM Users WHERE idUser = 4000");
        $user = $res->fetch();
        $this->assertEquals("Pierre",$user->lastName);
        $this->assertEquals("Robert",$user->firstName);
        
    }

    

    public function testInvalidParams() {
        $_POST= [
            "idUser" => "4000", 
            "login"  => "robert.loc", //modif login
            "lastNamezzz"  => "Loc", //error here
            "firstName"  => "Robert",  //modif firstName  
            "typePwd"  => "2", 
            "pwd"  => "123456",
            "verifPwd" => "123456", 
            "role"  => "student", 
            "idTraining"  => "1" 
        ];
        $this->controller->update_user();
        $res = Database::getInstance()->query("SELECT * FROM Users WHERE idUser = 4000");
        $user = $res->fetch();
        $this->assertEquals("fred.loc",$user->login);
        $this->assertEquals("Fred",$user->firstName);
    
    }

    public function testInvalidPWD() {
        $_POST= [
            "idUser" => "4000", 
            "login"  => "robert.pierre", //modif login
            "lastName"  => "Pierre", //modif lastName
            "firstName"  => "Robert",  //modif firstName
            "typePwd"  => "2", 
            "pwd"  => "123456",
            "verifPwd" => "122456", //error here
            "role"  => "student", 
            "idTraining"  => "1" 
        ];
        $this->controller->update_user();
        $res = Database::getInstance()->query("SELECT * FROM Users WHERE idUser = 4000");
        $user = $res->fetch();
        $this->assertEquals("fred.loc",$user->login);
        $this->assertEquals("Loc",$user->lastName);
        $this->assertEquals("Fred",$user->firstName);
    
    }

    public function testUserNotExist() {
        $_POST= [
            "idUser" => "4001", //error here
            "login"  => "robert.pierre",
            "lastName"  => "Pierre", //modif lastName
            "firstName"  => "Robert",  //modif firstName 
            "typePwd"  => "2", 
            "pwd"  => "123456",
            "verifPwd" => "123456",
            "role"  => "student", 
            "idTraining"  => "1"
        ];
        $this->controller->update_user();
        $res = Database::getInstance()->query("SELECT * FROM Users WHERE idUser = 4000");
        $user = $res->fetch();
        $this->assertEquals("fred.loc",$user->login);
        $this->assertEquals("Loc",$user->lastName);
        $this->assertEquals("Fred",$user->firstName);
    
    }

    public function testUpdateWithEducator(){
        $_SESSION["role"] = "educator";
        $_POST= [
            "idUser" => "4000", 
            "login"  => "fred.loc",
            "lastName"  => "Pierre", //modif lastName
            "firstName"  => "Robert",  //modif firstName
            "typePwd"  => "2", 
            "pwd"  => "123456",
            "verifPwd" => "123456", 
            "role"  => "student", 
            "idTraining"  => "1" 
        ];
        $this->expectException(\Exception::class);
        $this->controller = new SAdminController(null);
    }

}
?>