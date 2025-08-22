<?php
class Jogador {
    private $conn;
    private $table_name = "jogadores";

    public $id;
    public $nome;
    public $posicao;
    public $numero_camisa;
    public $time_id;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        // Validar número da camisa
        if($this->numero_camisa < 1 || $this->numero_camisa > 99) {
            return "Número da camisa deve estar entre 1 e 99.";
        }

        // Verificar se número já existe no time
        $check_query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE time_id = ? AND numero_camisa = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $this->time_id);
        $check_stmt->bindParam(2, $this->numero_camisa);
        $check_stmt->execute();
        $row = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if($row['total'] > 0) {
            return "Já existe um jogador com este número no time selecionado.";
        }

        $query = "INSERT INTO " . $this->table_name . " SET nome=:nome, posicao=:posicao, numero_camisa=:numero_camisa, time_id=:time_id";
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->posicao = htmlspecialchars(strip_tags($this->posicao));
        $this->numero_camisa = htmlspecialchars(strip_tags($this->numero_camisa));
        $this->time_id = htmlspecialchars(strip_tags($this->time_id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":posicao", $this->posicao);
        $stmt->bindParam(":numero_camisa", $this->numero_camisa);
        $stmt->bindParam(":time_id", $this->time_id);

        if($stmt->execute()) {
            return true;
        }
        return "Erro ao criar jogador.";
    }

    public function readAll($page = 1, $records_per_page = 10, $filters = []) {
        $offset = ($page - 1) * $records_per_page;
        $where = "WHERE 1=1";
        $params = [];

        if(!empty($filters['nome'])) {
            $where .= " AND j.nome LIKE :nome";
            $params[':nome'] = "%{$filters['nome']}%";
        }

        if(!empty($filters['posicao'])) {
            $where .= " AND j.posicao = :posicao";
            $params[':posicao'] = $filters['posicao'];
        }

        if(!empty($filters['time_id'])) {
            $where .= " AND j.time_id = :time_id";
            $params[':time_id'] = $filters['time_id'];
        }

        $query = "SELECT j.*, t.nome as time_nome 
                 FROM " . $this->table_name . " j 
                 LEFT JOIN times t ON j.time_id = t.id 
                 " . $where . " 
                 ORDER BY j.nome 
                 LIMIT :offset, :records_per_page";

        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindParam(":records_per_page", $records_per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }

    public function count($filters = []) {
        $where = "WHERE 1=1";
        $params = [];

        if(!empty($filters['nome'])) {
            $where .= " AND nome LIKE :nome";
            $params[':nome'] = "%{$filters['nome']}%";
        }

        if(!empty($filters['posicao'])) {
            $where .= " AND posicao = :posicao";
            $params[':posicao'] = $filters['posicao'];
        }

        if(!empty($filters['time_id'])) {
            $where .= " AND time_id = :time_id";
            $params[':time_id'] = $filters['time_id'];
        }

        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " " . $where;
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }

    public function readOne() {
        $query = "SELECT j.*, t.nome as time_nome 
                 FROM " . $this->table_name . " j 
                 LEFT JOIN times t ON j.time_id = t.id 
                 WHERE j.id = ? 
                 LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->nome = $row['nome'];
            $this->posicao = $row['posicao'];
            $this->numero_camisa = $row['numero_camisa'];
            $this->time_id = $row['time_id'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function update() {
        // Validar número da camisa
        if($this->numero_camisa < 1 || $this->numero_camisa > 99) {
            return "Número da camisa deve estar entre 1 e 99.";
        }

        // Verificar se número já existe no time (excluindo o próprio jogador)
        $check_query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE time_id = ? AND numero_camisa = ? AND id != ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $this->time_id);
        $check_stmt->bindParam(2, $this->numero_camisa);
        $check_stmt->bindParam(3, $this->id);
        $check_stmt->execute();
        $row = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if($row['total'] > 0) {
            return "Já existe um jogador com este número no time selecionado.";
        }

        $query = "UPDATE " . $this->table_name . " SET nome=:nome, posicao=:posicao, numero_camisa=:numero_camisa, time_id=:time_id WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->posicao = htmlspecialchars(strip_tags($this->posicao));
        $this->numero_camisa = htmlspecialchars(strip_tags($this->numero_camisa));
        $this->time_id = htmlspecialchars(strip_tags($this->time_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":posicao", $this->posicao);
        $stmt->bindParam(":numero_camisa", $this->numero_camisa);
        $stmt->bindParam(":time_id", $this->time_id);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return "Erro ao atualizar jogador.";
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return "Erro ao excluir jogador.";
    }

    public function getPosicoes() {
        return array('GOL', 'ZAG', 'LD', 'LE', 'VOL', 'MEI', 'ATA');
    }
}
?>
