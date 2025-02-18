<?php

use Config\Database;
use PHPUnit\Framework\TestCase;
use App\Class\UploadImg;
use App\Controllers\AdminController;

final class TestAddCommentStudent extends TestCase {
    private $controller;
    private $uploadMock;


    public function setUp(): void {
        $_SESSION["role"] = "educator-admin";
        $_SESSION["training"] = 1;
        $_SESSION["id"] = 10;
        $this->uploadMock = $this->createMock(UploadImg::class);
        $this->uploadMock->method('upload')->willReturn(true);
        $this->controller = new AdminController($this->uploadMock);
        $_FILES = ["picture" => ["name" => "leNom"]];
        define('PHPUNIT_RUNNING', true);
        


    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM CommentStudent WHERE text = 'Texte commentaire etu 985' AND idStudent = '2'");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);

    }

    public function testSuccess() {
        $_POST = [
            "idStudent" => 2,
            "idEducator" => 10,
            "text" => "Texte commentaire etu 985",
            "lastModif" => "2024-01-10T010:30"
        ];
        $this->controller->add_commentStudent();
        $res = Database::getInstance()->query("SELECT * FROM CommentStudent WHERE text = 'Texte commentaire etu 985' AND idStudent = '2'");
        $this->assertEquals(1,$res->rowCount());
    }

    public function testInvalidParams() {
        $_POST = [
            "idStudent" => 2,
            "idEducator" => 10,
            "textqsdqsdqsd" => "Texte commentaire etu 985", // error here
            "lastModif" => "2024-01-10T010:30"
        ];
        $this->controller->add_commentStudent();
        $res = Database::getInstance()->query("SELECT * FROM CommentStudent WHERE text = 'Texte commentaire etu 985' AND idStudent = '2'");
        $this->assertEquals(0,$res->rowCount());
    }

    public function testStudentNotExist() {
        $_POST = [
            "idStudent" => 100000, //error here
            "idEducator" => 10,
            "text" => "Texte commentaire etu 985",
            "lastModif" => "2024-01-10T010:30"
        ];
        $this->controller->add_commentStudent();
        $res = Database::getInstance()->query("SELECT * FROM CommentStudent WHERE text = 'Texte commentaire etu 985' AND idStudent = '100000'");
        $this->assertEquals(0,$res->rowCount());
    }

    public function testRoleUser(){
        $_SESSION["role"] = "student"; //error here
        $_POST= [
            "idStudent" => 2,
            "idEducator" => 10,
            "text" => "Texte commentaire etu 985",
            "lastModif" => "2024-01-10T010:30"
        ];
        $this->expectException(\Exception::class);
        $this->controller = new AdminController(null);

    }
}
