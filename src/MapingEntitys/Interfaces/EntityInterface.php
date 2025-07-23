<?php
namespace Maping\EntityInterface;


interface EntityInterface
{

    public function create();


    public function update();


    public function delete();


    public function findById();

    public function all();
}

?>