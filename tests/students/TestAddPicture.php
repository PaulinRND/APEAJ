<?php

use Config\Database;
use PHPUnit\Framework\TestCase;
use App\Class\UploadImg;
use App\Controllers\StudentController;

final class TestAddPicture extends TestCase {
    private $controller;
    private $uploadMock;


    public function setUp(): void {
        $_SESSION["role"] = "student";
        $_SESSION["training"] = "1";
        $_SESSION["id"] = 1;
        $this->uploadMock = $this->createMock(UploadImg::class);
        $this->uploadMock->method('upload')->willReturn(true);
        $this->controller = new StudentController($this->uploadMock);
        $_FILES = ["picture" => ["name" => "leNom"]];
        define('PHPUNIT_RUNNING', true);
    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM Picture WHERE title = 'Test title picture' AND numero = 10");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);

    }

    public function testSuccess() {
        $_POST = [
            "title" => "Test title picture",
            "numero" => 10,
            "idStudent" => 1   

        ];
        $this->controller->add_picture();
        $res = Database::getInstance()->query("SELECT * FROM Picture WHERE title = 'Test title picture' AND numero = 10");
        $this->assertEquals(1,$res->rowCount());
    }

    public function testInvalidParams() {
        $_POST = [
            "titlesdsf" => "Test title picture", //ERROR HERE
            "numero" => 10,
            "idStudent" => 1   

        ];
        $this->controller->add_picture();
        $res = Database::getInstance()->query("SELECT * FROM Picture WHERE title = 'Test title picture' AND numero = 10");
        $this->assertEquals(0,$res->rowCount());
    }
    
    public function testNoImage() {
        unset($_FILES); //ERROR HERE
        $_POST = [
            "title" => "Test title picture",
            "numero" => 10,
            "idStudent" => 1   
        ];
        $this->controller->add_picture();
        $res = Database::getInstance()->query("SELECT * FROM Picture WHERE title = 'Test title picture' AND numero = 10");
        $this->assertEquals(0,$res->rowCount());
    }
}
