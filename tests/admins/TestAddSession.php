<?php

use PHPUnit\Framework\TestCase;
use Config\Database;
use App\Class\UploadImg;
use App\Controllers\AdminController;

final class TestAddSession extends TestCase {

    private $controller;
    private $uploadMock;


    public function setUp(): void {
        $_SESSION["role"] = "educator-admin";
        $_SESSION["training"] = "1";
        $_SESSION["id"] = 17;
        $this->uploadMock = $this->createMock(UploadImg::class);
        $this->uploadMock->method('upload')->willReturn(true);
        $this->controller = new AdminController($this->uploadMock);
        
        $_FILES = ["picture" => ["name" => "leNom"]];
        define('PHPUNIT_RUNNING', true);

        

    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM Session WHERE wording = 'Wording session' AND theme = 'Serrurerie' and description = 'azertyuiop qsdfghjklm wxcvbn'");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);

    }

    public function testSuccess(){
        $_POST= [
            "wording"  => "Wording session",
            "theme" => "Serrurerie",
            "description"  => "azertyuiop qsdfghjklm wxcvbn", 
            "timeBegin" => "2023-10-28T08:00",
            "idTraining" => "1"
        ];
        $this->controller->add_session();
        $res = Database::getInstance()->query("SELECT * FROM Session WHERE wording = 'Wording session' AND theme = 'Serrurerie' and description = 'azertyuiop qsdfghjklm wxcvbn'");
        $this->assertEquals(1,$res->rowCount());
    }

    public function testInvalidParamsUsers(){
        $_POST= [
            "wordingfghj"  => "Wording session", //error here
            "theme" => "Serrurerie",
            "description"  => "azertyuiop qsdfghjklm wxcvbn", 
            "timeBegin" => "2023-10-28T08:00",
            "idTraining" => "1"
  
        ];
        $this->controller->add_session();
        $res = Database::getInstance()->query("SELECT * FROM Session WHERE wording = 'Wording session' AND theme = 'Serrurerie' and description = 'azertyuiop qsdfghjklm wxcvbn'");
        $this->assertEquals(0,$res->rowCount());
    }


    public function testAddSessionExist(){
        DataBase::getInstance()->exec("INSERT INTO Session (idSession, wording, theme, description, timeBegin, timeend, idTraining)
        VALUES (1500, 'Wording session', 'Serrurerie', 'azertyuiop qsdfghjklm wxcvbn', '2023-10-28T08:00', '2023-10-30T09:30', 1)");
        $_POST= [
            "idSession" => "1500", //error here
            "wording"  => "Wording session",
            "theme" => "Serrurerie",
            "description"  => "azertyuiop qsdfghjklm wxcvbn", 
            "timeBegin" => "2023-10-28T08:00",
            "timeend" => "2023-10-30T09:30",
            "idTraining" => "10000"
        ];
        $this->controller->add_session();
        $res = Database::getInstance()->query("SELECT * FROM Session WHERE wording = 'Wording session' AND theme = 'Serrurerie' and description = 'azertyuiop qsdfghjklm wxcvbn'");
        $this->assertEquals(1,$res->rowCount());
    }


    public function testRoleUser(){
        $_SESSION["role"] = "student"; //error here
        $_POST= [
            "idSessiond" => "1500", 
            "wording"  => "Wording session",
            "theme" => "Serrurerie",
            "description"  => "azertyuiop qsdfghjklm wxcvbn", 
            "timeBegin" => "2023-10-28T08:00",
            "idTraining" => "1"
  
        ];
        $this->expectException(\Exception::class);
        $this->controller = new AdminController(null);

    }

}
?>