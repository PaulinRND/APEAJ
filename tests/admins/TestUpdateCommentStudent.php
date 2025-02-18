<?php

use Config\Database;
use PHPUnit\Framework\TestCase;
use App\Class\UploadImg;
use App\Controllers\AdminController;

final class TestUpdateCommentStudent extends TestCase {
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
        DataBase::getInstance()->exec("INSERT INTO CommentStudent (idStudent, idEducator, text, lastModif)
        VALUES (2, 10, 'Texte commentaire etu 985', '2024-01-10T010:30')");


    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM CommentStudent WHERE idStudent = '2'");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);

    }
    
    public function testSuccess() {
        $_POST = [
            "idStudent" => 2,
            "idEducator" => 10,
            "text" => "Texte commentaire etu 985 avec modif", //modif here
            "lastModif" => "2024-01-10T010:30"
        ];
        $this->controller->update_commentStudent();
        $res = Database::getInstance()->query("SELECT * FROM CommentStudent WHERE idStudent = '2'");
        $commentStudent = $res->fetch();
        $this->assertEquals("Texte commentaire etu 985 avec modif",$commentStudent->text);
    }
    
    public function testInvalidParams() {
        $_POST = [
            "idStudent" => 2,
            "idEducator" => 10,
            "textqsdsqd" => "Texte commentaire etu 985 avec modif", //modif here //ERROR HERE
            "lastModif" => "2024-01-10T010:30"
        ];
        $this->controller->update_commentStudent();
        $res = Database::getInstance()->query("SELECT * FROM CommentStudent WHERE idStudent = '2'");
        $commentStudent = $res->fetch();
        $this->assertEquals("Texte commentaire etu 985",$commentStudent->text);
    }   

    public function testNotExistComment() {
        $_POST = [
            "idStudent" => 15000, //ERROR HERE
            "idEducator" => 10,
            "text" => "Texte commentaire etu 985 avec modif", //modif here
            "lastModif" => "2024-01-10T010:30"
        ];
        $this->controller->update_commentStudent();
        $res = Database::getInstance()->query("SELECT * FROM CommentStudent WHERE text = 'Texte commentaire etu 985' AND idStudent = '15000'");
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
