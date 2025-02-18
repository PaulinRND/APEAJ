<?php

use Config\Database;
use PHPUnit\Framework\TestCase;
use App\Class\UploadImg;
use App\Controllers\StudentController;

final class TestUpdateComment extends TestCase {
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

        DataBase::getInstance()->exec("INSERT INTO CommentForm (idCommentForm, wording, text, audio, admin, idAuthor, idStudent, numero, lastModif, note)
        VALUES (999999, 'Commentaire fiche 999999', 'Texte commentaire 999999 fiche', null , 'false', 10, 2, 10, '2024-01-10T010:30', 15)");


    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM CommentForm WHERE idCommentForm = 999999");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);

    }

    public function testSuccess() {
        $_POST = [
            "idCommentForm" => 999999,
            "wording" => "Commentaire fiche 999999 modifier",
            "text" => "Texte commentaire 999999 fiche modifier",
            "audio" => "null",
            "admin" => "false",
            "idAuthor" => "11",
            "idStudent" => "2",
            "numero" => "10",
            "lastModif" => "2024-01-10T010:30",
            "note" => "15"
        ];
        $this->controller->update_comment();
        $res = Database::getInstance()->query("SELECT * FROM CommentForm WHERE idCommentForm = 999999");
        $commentFormStudent = $res->fetch();
        $this->assertEquals("Commentaire fiche 999999 modifier",$commentFormStudent->wording);
        $this->assertEquals("Texte commentaire 999999 fiche modifier",$commentFormStudent->text);

    }

    public function testInvalidParams() {
        $_POST = [
            "idCommentForm" => 999999,
            "wording" => "Commentaire fiche 999999 modifier",
            "texqsdsqdqsdt" => "Texte commentaire 999999 fiche modifier",
            "audio" => "null",
            "admin" => "false",
            "idAuthor" => "11",
            "idStudent" => "2",
            "numero" => "10",
            "lastModif" => "2024-01-10T010:30",
            "note" => "15"
        ];
        $this->controller->update_comment();
        $res = Database::getInstance()->query("SELECT * FROM CommentForm WHERE idCommentForm = 999999");
        $commentFormStudent = $res->fetch();
        $this->assertEquals("Commentaire fiche 999999",$commentFormStudent->wording);
        $this->assertEquals("Texte commentaire 999999 fiche",$commentFormStudent->text);

    }

    public function testFormNotExist() {
        $_POST = [
            "idCommentForm" => 999999999,
            "wording" => "Commentaire fiche 999999 modifier",
            "texqsdsqdqsdt" => "Texte commentaire 999999 fiche modifier",
            "audio" => "null",
            "admin" => "false",
            "idAuthor" => "11",
            "idStudent" => "2",
            "numero" => "10",
            "lastModif" => "2024-01-10T010:30",
            "note" => "15"
        ];
        $this->controller->update_comment();
        $res = Database::getInstance()->query("SELECT * FROM CommentForm WHERE idCommentForm = 999999999");
        $commentFormStudent = $res->fetch();
        $this->assertNotEquals("Commentaire fiche 999999",$commentFormStudent->wording);
        $this->assertNotEquals("Texte commentaire 999999 fiche",$commentFormStudent->text);

    }

}