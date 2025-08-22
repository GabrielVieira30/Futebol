<?php
class Time {
    private $conn;
    private $table_name = "times";

    public $id;
    public $nome;
    public $cidade;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nome=:nome, cidade=:cidade";
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->cidade = htmlspecialchars(strip_tags($this->cidade));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":cidade", $this->cidade);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll($page = 1, $records_per_page = 10, $search = '') {
        $offset = ($page - 1) * $records_per_page;
        $where = '';
        
        if(!empty($search)) {
            $where = "WHERE nome LIKE :search OR cidade LIKE :search";
        }

        $query = "SELECT * FROM " . $this->table_name . " " . $where . " ORDER BY nome LIMIT :offset, :records_per_page";
        $stmt = $this->conn->prepare($query);
        
        if(!empty($search)) {
            $search_term = "%{$search}%";
            $stmt->bindParam(":search", $search_term);
        }
        
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindParam(":records_per_page", $records_per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }

    public function count($search = '') {
        $where = '';
        if(!empty($search)) {
            $where = "WHERE nome LIKE :search OR cidade LIKE :search";
        }

        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " " . $where;
        $stmt = $this->conn->prepare($query);
        
        if(!empty($search)) {
            $search_term = "%{$search}%";
            $stmt->bindParam(":search", $search_term);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->nome = $row['nome'];
            $this->cidade = $row['cidade'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nome=:nome, cidade=:cidade WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->cidade = htmlspecialchars(strip_tags($this->cidade));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":cidade", $this->cidade);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        // Verificar se há jogadores associados
        $check_query = "SELECT COUNT(*) as total FROM jogadores WHERE time_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $this->id);
        $check_stmt->execute();
        $row = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if($row['total'] > 0) {
            return "Não é possível excluir o time pois existem jogadores associados.";
        }

        // Verificar se há partidas associadas
        $check_query = "SELECT COUNT(*) as total FROM partidas WHERE time_casa_id = ? OR time_fora_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $this->id);
        $check_stmt->bindParam(2, $this->id);
        $check_stmt->execute();
        $row = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if($row['total'] > 0) {
            return "Não é possível excluir o time pois existem partidas associadas.";
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return "Erro ao excluir o time.";
    }

    public function getAll() {
        $query = "SELECT id, nome FROM " . $this->table_name . " ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
