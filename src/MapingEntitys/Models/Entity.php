<?php 
namespace Maping\Entity;

use Maping\EntityInterface\EntityInterface;
use Maping\Models\Conection;
use Maping\Models\Connection;

class Entity implements EntityInterface
{

        protected $conn;

        protected string $table;

        protected array $colums;

        public function __construct( string $table, array $colums)
        {
                $this->table = $table;
                $this->colums = $colums;
                $this->conn = Connection::getInstance();
        }


        public function create()
        {
            
        }

        public function update()
        {
            
        }

        public function delete()
        {
            
        }

        public function findById()
        {
            
        }

        public function all()
        {
            
        }


        public function executeQuery(array $params, string $query)
        {
                try {
                        $stmt = $this->conn->prepare($query);
                        $stmt->execute($params);
                        return $stmt;
                } catch (\Throwable $th) {
                        throw $th;
                }
        }
}


?>