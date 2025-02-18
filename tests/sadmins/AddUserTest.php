<?php

use PHPUnit\Framework\TestCase;
use Config\Database;
use App\Class\UploadImg;
use App\Controllers\SAdminController;

final class AddUserTest extends TestCase {

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
    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM Users WHERE lastName = 'Loc' AND firstName = 'Fred' AND login = 'fred.loc'");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);
    }

    public function testSuccess() {
        $_POST= [
            "login"  => "fred.loc", 
            "lastName"  => "Loc", 
            "firstName"  => "Fred",  
            "typePwd"  => "2", 
            "pwd"  => "123456",
            "verifPwd" => "123456", 
            "role"  => "student", 
            "idTraining"  => "1"
        ];
        $this->controller->add_user();
        $res = Database::getInstance()->query("SELECT * FROM Users WHERE lastName = 'Loc' AND firstName = 'Fred' AND login = 'fred.loc'");
        $this->assertEquals(1,$res->rowCount());
    }

    

    public function testInvalidParams() {
        $_POST= [
            "login"  => "fred.loc", 
            "lastNamezzz"  => "Loc", //error here
            "firstName"  => "Fred",  
            "typePwd"  => "2", 
            "pwd"  => "123456",
            "verifPwd" => "123456", 
            "role"  => "student", 
            "idTraining"  => "1"
        ];
        $this->controller->add_user();
        $res = Database::getInstance()->query("SELECT * FROM Users WHERE lastName = 'Loc' AND firstName = 'Fred' AND login = 'fred.loc'");
        $this->assertEquals(0,$res->rowCount());
    }

    public function testTrainingNotExist() {
        $_POST= [
            "login"  => "fred.loc", 
            "lastName"  => "Loc", 
            "firstName"  => "Fred",  
            "typePwd"  => "2", 
            "pwd"  => "123456",
            "verifPwd" => "123456",
            "role"  => "student", 
            "idTraining"  => "500" //error here
        ];
        $this->controller->add_user();
        $res = Database::getInstance()->query("SELECT * FROM Users WHERE lastName = 'Loc' AND firstName = 'Fred' AND login = 'fred.loc'");
        $this->assertEquals(0,$res->rowCount());
    }

    public function testAddUserRoleAdmin(){
        $_SESSION["role"] = "educator";  //error here
        $_POST= [
            "login"  => "fred.loc", 
            "lastName"  => "Loc", 
            "firstName"  => "Fred",  
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