<?php

namespace Rafael\Orm\MapingEntitys\Interfaces;


interface EntityInterface
{

    public function create(array $data);


    public function update(array $data, int $id);


    public function delete(int $id);


    public function findById(int $id);

    public function all();
}

?>