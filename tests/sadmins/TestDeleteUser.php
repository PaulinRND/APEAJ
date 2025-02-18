<?php

use PHPUnit\Framework\TestCase;
use Config\Database;
use App\Class\UploadImg;
use App\Controllers\SAdminController;

final class TestDeleteUser extends TestCase {

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

    public function testDeleteRoleEducator(){
        $_SESSION["role"] = "educator";
        $_POST= [
            "idUser" => "4000"
        ];
        $this->expectException(\Exception::class);
        $this->controller = new SAdminController(null);
    }
    



    public function testSuccess() {
        $_POST= [
            "idUser" => "4000"
        ];
        $this->controller->delete_user();
        $res = Database::getInstance()->query("SELECT * FROM Users WHERE idUser = 4000");
        $this->assertEquals(0, $res->rowCount());     
    }

    

    public function testIssetParams() {
        $_POST= [];
        $this->controller->delete_user();
        $res = Database::getInstance()->query("SELECT * FROM Users WHERE idUser = 4000");
        $this->assertEquals(1, $res->rowCount());     

    }

}
?>