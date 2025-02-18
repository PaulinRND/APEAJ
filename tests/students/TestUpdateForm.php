<?php

use Config\Database;
use PHPUnit\Framework\TestCase;
use App\Class\UploadImg;
use App\Controllers\StudentController;

final class TestUpdateForm extends TestCase {
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
        DataBase::getInstance()->exec("INSERT INTO Form (idSession, idStudent, idEducator, numero, finish, creationDate, bgColor, studentLastName, studentFirstName, applicantName, applicationDate, location, description, urgencyDegree, interventionDate, interventionDuration, interventionValidation, maintenanceType, interventionNature, workDone, workNotDone, newIntervention)
        VALUES (1,1,10,150,false,'2023-12-28','#F6E38F','Albert','Paul','Olivier','2024-01-04','Toulouse','Test pour une description','Pas urgent','2024-01-10','01h15',true,3,3,'La réparation a été effectuée',null,false)");
    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM Form WHERE idSession = 1 AND numero = 150 AND idStudent = 1 AND idEducator = 10");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);

    }

    public function testSuccess() {
        $_POST = [
            "studentLastName" => "Albert",
            "studentFirstName" => "Paul",
            "applicantName" => "Olivier",
            "applicationDate" => "2024-01-04",
            "location" => "Toulouse",
            "description" => "Test pour une description modifier",
            "urgencyDegree" => "Pas urgent",
            "interventionDate" => "2024-01-10",
            "interventionDuration" => "01h15",
            "interventionValidation" => "true",
            "maintenanceType" => 3,
            "interventionNature" => 3,
            "workDone" => "La réparation a été effectuée",
            "workNotDone" => "null",
            "newIntervention" => "False"

        ];
        $this->controller->update_form(150);
        $res = Database::getInstance()->query("SELECT * FROM Form WHERE idSession = 1 AND numero = 150 AND idStudent = 1 AND idEducator = 10");
        $form = $res->fetch();
        $this->assertEquals("Test pour une description modifier",$form->description);

    }

    public function testInvalidNumero() {
        $_POST = [
            "studentLastName" => "Albert",
            "studentFirstName" => "Paul",
            "applicantName" => "Olivier",
            "applicationDate" => "2024-01-04",
            "location" => "Toulouse",
            "description" => "Test pour une description modifier",
            "urgencyDegree" => "Pas urgent",
            "interventionDate" => "2024-01-10",
            "interventionDuration" => "01h15",
            "interventionValidation" => "true",
            "maintenanceType" => "3",
            "interventionNature" => "3",
            "workDone" => "La réparation a été effectuée",
            "workNotDone" => "null",
            "newIntervention" => "False"

        ];
        $this->controller->update_form(150);
        $res = Database::getInstance()->query("SELECT * FROM Form WHERE idSession = 1 AND numero = 555555 AND idStudent = 1 AND idEducator = 10");
        $this->assertEquals(0,$res->rowCount());


    }

    public function testIssetFormUpdate() {
        $_POST = [
            "studentLastName" => "Albert",
            "applicantName" => "Olivier",
            "applicationDate" => "2024-01-04",
            "description" => "Test pour une description modifier",
            "urgencyDegree" => "Pas urgent",
            "interventionDate" => "2024-01-10",
            "interventionDuration" => "01h15",
            "interventionValidation" => "true",
            "maintenanceType" => "3",
            "interventionNature" => "3",
            "workDone" => "La réparation a été effectuée",
            "workNotDone" => "null",
            "newIntervention" => "False"

        ];
        $this->controller->update_form(150);
        $res = Database::getInstance()->query("SELECT * FROM Form WHERE idSession = 1 AND numero = 150 AND idStudent = 1 AND idEducator = 10");
        $form = $res->fetch();
        $this->assertEquals("Test pour une description",$form->description);


    }
}
