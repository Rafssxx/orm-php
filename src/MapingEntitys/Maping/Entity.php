<?php

// Define o namespace da classe
namespace Rafael\Orm\MapingEntitys\Maping;

// Importa a classe de conexão e a interface da entidade
use Rafael\Orm\MapingEntitys\Maping\Connection;
use Rafael\Orm\MapingEntitys\Interfaces\EntityInterface;

/**
 * Classe base para representar uma entidade do banco de dados
 * Implementa operações básicas de CRUD.
 */
class Entity implements EntityInterface
{
    // Conexão PDO com o banco de dados
    protected $conn;

    // Nome da tabela relacionada a esta entidade
    protected string $table;

    // Lista de colunas válidas para esta entidade
    protected array $colums;

    /**
     * Construtor da entidade.
     * Define a tabela, colunas permitidas e instancia a conexão com o banco.
     */
    public function __construct(string $table, array $colums)
    {
        $this->table = $table;
        $this->colums = $colums;
        $this->conn = Connection::getInstance(); // Singleton de conexão
    }

    /**
     * Cria um novo registro na tabela.
     * Apenas colunas válidas são utilizadas.
     */
    public function create(array $data)
    {
        // Gera um array com as colunas válidas como chave
        $columnsKeys = array_flip($this->colums);

        // Filtra os dados de entrada para aceitar apenas colunas válidas
        $filteredColumns = array_intersect_key($data, $columnsKeys);

        // Cria string com os nomes das colunas e placeholders
        $columns = implode(', ', array_keys($filteredColumns));
        $placeholders = implode(", ", array_fill(0, count($filteredColumns), '?'));

        // Monta a query de inserção
        $query = sprintf(
            "INSERT INTO %s (%s) VALUES(%s)",
            $this->table,
            $columns,
            $placeholders
        );

        // Executa a query com os valores filtrados
        $this->executeQuery($query, array_values($filteredColumns));
    }

    /**
     * Atualiza um registro existente com base no ID.
     */
    public function update(array $data, int $id): bool
    {
        // Filtra as colunas permitidas
        $columnsKeys = array_flip($this->colums);
        $filteredColumns = array_intersect_key($data, $columnsKeys);

        $setParts = []; // Array com as partes do SET
        $values = [];   // Array com os valores dos campos

        // Monta a parte do SET da query e o array de valores
        foreach ($filteredColumns as $column => $value) {
            $setParts[] = "$column = ?";
            $values[] = $value;
        }

        // Adiciona o ID no final do array de parâmetros
        $values[] = $id;

        // Monta a query de UPDATE
        $query = sprintf(
            "UPDATE %s SET %s WHERE id = ?",
            $this->table,
            implode(', ', $setParts)
        );

        // Executa a query
        $stmt = $this->executeQuery($query, $values);

        // Retorna true se pelo menos uma linha foi afetada
        return $stmt->rowCount() > 0;
    }

    /**
     * Deleta um registro com base no ID.
     */
    public function delete(int $id)
    {
        // Monta a query de DELETE
        $query = sprintf("DELETE FROM %s WHERE id = %s", $this->table, $id);
        return $this->executeQuery($query);
    }

    /**
     * Busca um registro pelo ID.
     */
    public function findById(int $id)
    {
        // Monta a query de SELECT com WHERE id
        $query = sprintf("SELECT * FROM %s WHERE id = %s", $this->table, $id);
        $stmt = $this->executeQuery($query);

        // Retorna o resultado como array associativo
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Retorna todos os registros da tabela.
     */
    public function all(): array
    {
        $query = sprintf("SELECT * FROM %s", $this->table);
        $stmt = $this->executeQuery($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Executa uma query SQL com parâmetros.
     */
    public function executeQuery(string $query, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->conn->prepare($query); // Prepara a query

            if ($stmt === false) {
                throw new \RuntimeException("Failed to prepare query");
            }

            // Associa os valores aos placeholders
            foreach ($params as $key => $value) {
                $paramType = is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
                $stmt->bindValue(is_int($key) ? $key + 1 : $key, $value, $paramType);
            }

            // Executa a query
            if (!$stmt->execute()) {
                throw new \RuntimeException("Query execution failed");
            }

            return $stmt;
        } catch (\PDOException $e) {
            // Log do erro no servidor
            error_log("Database error: " . $e->getMessage() . " in query: " . $query);
            throw $e; // Propaga exceção para tratamento externo
        }
    }

    /**
     * Retorna as colunas válidas da entidade.
     */
    public function getColumns()
    {
        return $this->colums;
    }
}
