<?php

use Config\Database;
use PHPUnit\Framework\TestCase;
use App\Class\UploadImg;
use App\Controllers\StudentController;

final class TestDeleteComment extends TestCase {
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
        
        DataBase::getInstance()->exec("INSERT INTO CommentForm (idCommentForm, wording, text, audio, admin, idAuthor, idStudent, numero, lastModif, note)
        VALUES (5000, 'Commentaire 5000', 'Texte du commentaire 5000', null, 'false', 1, 1, 1, '2024-01-10T010:30', 15 )");

    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM CommentForm WHERE idCommentForm = 5000");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);

    }

    public function testSuccess() {
        $_POST = [
            "idCommentForm" => 5000,
            "idAuthor" => 10,
            "idStudent" => 1,
            "numero" => 1
        ];
        $this->controller->delete_comment();
        $res = Database::getInstance()->query("SELECT * FROM CommentForm WHERE idCommentForm = 5000");
        $this->assertEquals(0,$res->rowCount());
    }

    public function testInvalidParams() {
        $_POST = [
            "idCommentFormkfkgdjg" => 5000,
        ];
        $this->controller->delete_comment();
        $res = Database::getInstance()->query("SELECT * FROM CommentForm WHERE idCommentForm = 5000");
        $this->assertEquals(1,$res->rowCount());
    }

}
