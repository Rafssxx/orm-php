<?php 


namespace Rafael\Orm\MapingEntitys\Maping;

use Rafael\Orm\MapingEntitys\Maping\Connection;

use Rafael\Orm\MapingEntitys\Interfaces\EntityInterface;



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
        
        public function create(array $data)
        {
                $columnsKeys = array_flip($this->colums);

                $filteredColumns = array_intersect_key($data, $columnsKeys);
                
                $columns = implode(', ', array_keys($filteredColumns));

                $placeholders = implode(", ", array_fill(0, count($filteredColumns), '?'));

                $query = sprintf(
                        "INSERT INTO %s (%s) VALUES(%s)",
                         $this->table,
                         $columns,
                         $placeholders
                );

                return $this->executeQuery($query);
        }

        public function update(array $data)
        {
            
        }

        public function delete(int $id)
        {
            
        }

        public function findById(int $id)
        {
            
        }

        public function all()
        {
                
        }

        public function executeQuery(string $query)
        {
                try {
                        $stmt = $this->conn->prepare($query);
                        $stmt->execute();
                        return $stmt;
                } catch (\Throwable $th) {
                        throw $th;
                }
        }

        public function getColumns()
                {
                        return $this->colums;
                }
}
?>