<?php

use PHPUnit\Framework\TestCase;
use Config\Database;
use App\Class\UploadImg;
use App\Controllers\SAdminController;

final class TestDeleteTraining extends TestCase {

    private $controller;
    private $uploadMock;

    public function setUp(): void {
        $_SESSION["role"]="super-admin";
        $_SESSION["training"] = "1";
        $_SESSION["id"] = 17;
        $this->uploadMock = $this->createMock(UploadImg::class);
        $this->uploadMock->method('upload')->willReturn(true);
        $this->controller = new SAdminController($this->uploadMock);
        $_FILES = ["picture" => ["name" => "leNom"]];
        DataBase::getInstance()->exec("INSERT INTO Training (idTraining, wording, description, qualifLevel)
        VALUES (5, 'UnTestQuiTest', 'azertyuiop qsdfghjklm wxcvbn', 'CAP')");

    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM Training WHERE idTraining = 5");
        $_POST = [];
    }


    public function testSuccess() {
        $_POST= [
            "idTraining" => "5", 
        ];
        $this->controller->delete_training();
        $res = Database::getInstance()->query("SELECT * FROM Training WHERE idTraining = 5");
        $this->assertEquals(0, $res->rowCount());

    }

    public function testInvalidParams(){
        $_POST= [
            "idTrainingggg" => "5", 
           
        ];
        $this->controller->delete_training();
        $res = Database::getInstance()->query("SELECT * FROM Training WHERE idTraining = 5");
        $this->assertEquals(1, $res->rowCount());

    }

    public function testTrainingNotExist(){
        $_POST= [] ;
        $this->controller->delete_training();
        $res = Database::getInstance()->query("SELECT * FROM Training WHERE idTraining = 5");
        $this->assertEquals(0, $res->rowCount());
    }

    
    

}
?>