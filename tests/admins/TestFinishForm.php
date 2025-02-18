<?php

use Config\Database;
use PHPUnit\Framework\TestCase;
use App\Class\UploadImg;
use App\Controllers\AdminController;

final class TestFinishForm extends TestCase {
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
        
        DataBase::getInstance()->exec("INSERT INTO Form (idSession, idStudent, idEducator, numero, finish, creationDate, bgColor, studentLastName, studentFirstName, applicantName, applicationDate, location, description, urgencyDegree, interventionDate, interventionDuration, interventionValidation, maintenanceType, interventionNature, workDone, workNotDone, newIntervention)
        VALUES (10,1,17,150000,false,'2024-02-02','#E8A5B9','Frederic','Mathieu','Lefevre','2024-01-24','Toulouse','Une description permettant un test unitaire','Très urgent','2024-02-04',null,null,null,null,null,null,null)");

    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM Form WHERE idSession = 10 AND idStudent = 1  AND idEducator = 17  AND numero = 150000 AND description = 'Une description permettant un test unitaire'");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);

    }

    
    public function testSuccess() {
        $_POST = [
            "finish" => 'true'
        ];
        $this->controller->finishForm();
        $res = Database::getInstance()->query("SELECT * FROM Form WHERE idSession = 10 AND idStudent = 1  AND idEducator = 17  AND numero = 150000 AND description = 'Une description permettant un test unitaire' AND finish = 'true'");
        $this->assertEquals(1,$res->rowCount());
    }

    public function testInvalideRole() {
        $_SESSION["role"] = "student"; // error here
        $_POST = [
            "finish" => 'true' //modif here
        ];
        $this->controller->finishForm();
        $this->expectException(\Exception::class);
        $this->controller = new AdminController(null);
    }

    public function testInvalidNumero() {
        $_POST = [
            "numero" => 159999, //error here
            "finish" => 'true' //modif here
        ];
        $this->controller->finishForm();
        $res = Database::getInstance()->query("SELECT * FROM Form WHERE idSession = 10 AND idStudent = 1  AND idEducator = 17  AND numero = 159999 AND description = 'Une description permettant un test unitaire' AND finish = 'true'");
        $this->assertEquals(0,$res->rowCount());
    }


    public function testInvalidStudent() {
        $_POST = [
            "idStudent" => '15000', //error here
            "finish" => 'true'
        ];
        $this->controller->finishForm();
        $res = Database::getInstance()->query("SELECT * FROM Form WHERE idSession = 10 AND idStudent = 15000  AND idEducator = 17  AND numero = 150000 AND description = 'Une description permettant un test unitaire' AND finish = 'true'");
        $this->assertEquals(0,$res->rowCount());
    }


}
