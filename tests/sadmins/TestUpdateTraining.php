<?php

use PHPUnit\Framework\TestCase;
use Config\Database;
use App\Class\UploadImg;
use App\Controllers\SAdminController;

final class TestUpdateTraining extends TestCase {

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
        define('PHPUNIT_RUNNING', true);
        DataBase::getInstance()->exec("INSERT INTO Training (idTraining, wording, description, qualifLevel)
        VALUES (5, 'UnTestQuiTest', 'azertyuiop qsdfghjklm wxcvbn', 'CAP')");

    }

    public function tearDown(): void {
        Database::getInstance()->exec("DELETE FROM Training WHERE idTraining = 5");
        $_POST = [];
        define('PHPUNIT_RUNNING', false);

    }


    public function testSuccess() {
        $_POST= [
            "idTraining" => "5", 
            "wording"  => "UnTestQuiTest", 
            "description"  => "nbvcxw mlkjhgfdsq poiuytreza", //modif here
            "qualifLevel"  => "BUT"  //modif here
        ];
        $this->controller->update_training();
        $res = Database::getInstance()->query("SELECT * FROM Training WHERE idTraining = 5");
        $training = $res->fetch();
        $this->assertEquals("nbvcxw mlkjhgfdsq poiuytreza",$training->description);
        $this->assertEquals("BUT",$training->qualifLevel);
    }

    public function testInvalidParams(){
        $_POST= [
            "idTraining" => "5", 
            "wordingDFGH"  => "UnTestQuiTest", //error here
            "description"  => "nbvcxw mlkjhgfdsq poiuytreza", //modif here 
            "qualifLevel"  => "BUT" //modif here 
        ];
        $this->controller->update_training();
        $res = Database::getInstance()->query("SELECT * FROM Training WHERE idTraining = 5");
        $training = $res->fetch();
        $this->assertEquals("azertyuiop qsdfghjklm wxcvbn",$training->description);
        $this->assertEquals("CAP",$training->qualifLevel);
    }

    public function testAddTrainingRoleAdmin(){
        $_SESSION["role"]="educator";
        $_POST= [
            "idTraining" => "5", 
            "wording"  => "UnTestQuiTest", 
            "description"  => "nbvcxw mlkjhgfdsq poiuytreza", //modif here 
            "qualifLevel"  => "BUT" //modif here   
        ];
        $this->expectException(\Exception::class);
        $this->controller = new SAdminController(null);
    }

    public function testTrainingNotExist(){
        $_POST= [
            "idTraining" => "500", //error here
            "wordingDFGH"  => "UnTestQuiTest", 
            "description"  => "nbvcxw mlkjhgfdsq poiuytreza", //modif here 
            "qualifLevel"  => "BUT" //modif here   
        ];
        $this->controller->update_training();
        $res = Database::getInstance()->query("SELECT * FROM Training WHERE idTraining = 5");
        $training = $res->fetch();
        $this->assertEquals("azertyuiop qsdfghjklm wxcvbn",$training->description);
        $this->assertEquals("CAP",$training->qualifLevel);
    }
    
    

}
?>