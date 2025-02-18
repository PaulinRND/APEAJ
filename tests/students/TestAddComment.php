<?php

use Config\Database;
use PHPUnit\Framework\TestCase;
use App\Class\UploadImg;
use App\Controllers\StudentController;

final class TestAddComment extends TestCase {
    private $controller;
    private $uploadMock;


    public function setUp(): void {
        $_SESSION["role"] = "student";
        $_SESSION["training"] = 1;
        $_SESSION["id"] = 1;
        $this->uploadMock = $this->createMock(UploadImg::class);
        $this->uploadMock->method('upload')->willReturn(true);
        $this->controller = new StudentController($this->uploadMock);
        
        $_FILES = ["picture" => ["name" => "leNom"]];
        define('PHPUNIT_RUNNING', true);
        


    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM CommentForm WHERE idStudent = 2 AND numero = 10 AND text = 'Texte Commentaire 999999 fiche'");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);

    }

    public function testSuccess() {
        $_POST = [
            "wording" => "Commentaire fiche 999999",
            "text" => "Texte commentaire 999999 fiche",
            "audio" => "null",
            "admin" => "false",
            "idStudent" => "2",
            "numero" => "10",
            "note" => "15"
        ];
        $this->controller->add_comment();
        $res = Database::getInstance()->query("SELECT * FROM CommentForm WHERE idStudent = 2 AND numero = 10 AND text = 'Texte Commentaire 999999 fiche'");
        $this->assertEquals(1,$res->rowCount());
    }

    public function testInvalidParams() {
        $_POST = [
            "wording" => "Commentaire fiche 999999",
            "textsdqsd" => "Texte commentaire 999999 fiche",
            "audio" => "null",
            "admin" => "false",
            "idStudent" => "2",
            "numero" => "10",
            "note" => "15"
        ];
        $this->controller->add_comment();
        $res = Database::getInstance()->query("SELECT * FROM CommentForm WHERE idStudent = 2 AND numero = 10 AND text = 'Texte Commentaire 999999 fiche'");
        $this->assertEquals(0,$res->rowCount());
    }

    public function testFormNotExist() {
        $_POST = [
            "wording" => "Commentaire fiche 999999",
            "text" => "Texte commentaire 999999 fiche",
            "audio" => "null",
            "admin" => "false",
            "idStudent" => "2",
            "numero" => "10000",
            "note" => "15"
        ];
        $this->controller->add_comment();
        $res = Database::getInstance()->query("SELECT * FROM CommentForm WHERE idStudent = 2 AND numero = 10000 AND text = 'Texte Commentaire 999999 fiche'");
        $this->assertEquals(0,$res->rowCount());
    }

}
