<?php
class Partida {
    private $conn;
    private $table_name = "partidas";

    public $id;
    public $time_casa_id;
    public $time_fora_id;
    public $data_jogo;
    public $gols_casa;
    public $gols_fora;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        // Validar se times são diferentes
        if($this->time_casa_id == $this->time_fora_id) {
            return "O time mandante não pode ser igual ao time visitante.";
        }

        // Validar gols
        if($this->gols_casa < 0 || $this->gols_fora < 0) {
            return "Os gols não podem ser negativos.";
        }

        $query = "INSERT INTO " . $this->table_name . " 
                 SET time_casa_id=:time_casa_id, time_fora_id=:time_fora_id, 
                     data_jogo=:data_jogo, gols_casa=:gols_casa, gols_fora=:gols_fora";
        $stmt = $this->conn->prepare($query);

        $this->time_casa_id = htmlspecialchars(strip_tags($this->time_casa_id));
        $this->time_fora_id = htmlspecialchars(strip_tags($this->time_fora_id));
        $this->data_jogo = htmlspecialchars(strip_tags($this->data_jogo));
        $this->gols_casa = htmlspecialchars(strip_tags($this->gols_casa));
        $this->gols_fora = htmlspecialchars(strip_tags($this->gols_fora));

        $stmt->bindParam(":time_casa_id", $this->time_casa_id);
        $stmt->bindParam(":time_fora_id", $this->time_fora_id);
        $stmt->bindParam(":data_jogo", $this->data_jogo);
        $stmt->bindParam(":gols_casa", $this->gols_casa);
        $stmt->bindParam(":gols_fora", $this->gols_fora);

        if($stmt->execute()) {
            return true;
        }
        return "Erro ao criar partida.";
    }

    public function readAll($page = 1, $records_per_page = 10, $filters = []) {
        $offset = ($page - 1) * $records_per_page;
        $where = "WHERE 1=1";
        $params = [];

        if(!empty($filters['time_id'])) {
            $where .= " AND (p.time_casa_id = :time_id OR p.time_fora_id = :time_id)";
            $params[':time_id'] = $filters['time_id'];
        }

        if(!empty($filters['data_inicio'])) {
            $where .= " AND p.data_jogo >= :data_inicio";
            $params[':data_inicio'] = $filters['data_inicio'];
        }

        if(!empty($filters['data_fim'])) {
            $where .= " AND p.data_jogo <= :data_fim";
            $params[':data_fim'] = $filters['data_fim'];
        }

        if(!empty($filters['resultado'])) {
            if($filters['resultado'] == 'vitoria_casa') {
                $where .= " AND p.gols_casa > p.gols_fora";
            } elseif($filters['resultado'] == 'vitoria_fora') {
                $where .= " AND p.gols_casa < p.gols_fora";
            } elseif($filters['resultado'] == 'empate') {
                $where .= " AND p.gols_casa = p.gols_fora";
            }
        }

        $query = "SELECT p.*, 
                 tc.nome as time_casa_nome, 
                 tf.nome as time_fora_nome 
                 FROM " . $this->table_name . " p 
                 LEFT JOIN times tc ON p.time_casa_id = tc.id 
                 LEFT JOIN times tf ON p.time_fora_id = tf.id 
                 " . $where . " 
                 ORDER BY p.data_jogo DESC 
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

        if(!empty($filters['time_id'])) {
            $where .= " AND (time_casa_id = :time_id OR time_fora_id = :time_id)";
            $params[':time_id'] = $filters['time_id'];
        }

        if(!empty($filters['data_inicio'])) {
            $where .= " AND data_jogo >= :data_inicio";
            $params[':data_inicio'] = $filters['data_inicio'];
        }

        if(!empty($filters['data_fim'])) {
            $where .= " AND data_jogo <= :data_fim";
            $params[':data_fim'] = $filters['data_fim'];
        }

        if(!empty($filters['resultado'])) {
            if($filters['resultado'] == 'vitoria_casa') {
                $where .= " AND gols_casa > gols_fora";
            } elseif($filters['resultado'] == 'vitoria_fora') {
                $where .= " AND gols_casa < gols_fora";
            } elseif($filters['resultado'] == 'empate') {
                $where .= " AND gols_casa = gols_fora";
            }
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
        $query = "SELECT p.*, 
                 tc.nome as time_casa_nome, 
                 tf.nome as time_fora_nome 
                 FROM " . $this->table_name . " p 
                 LEFT JOIN times tc ON p.time_casa_id = tc.id 
                 LEFT JOIN times tf ON p.time_fora_id = tf.id 
                 WHERE p.id = ? 
                 LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->time_casa_id = $row['time_casa_id'];
            $this->time_fora_id = $row['time_fora_id'];
            $this->data_jogo = $row['data_jogo'];
            $this->gols_casa = $row['gols_casa'];
            $this->gols_fora = $row['gols_fora'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function update() {
        // Validar se times são diferentes
        if($this->time_casa_id == $this->time_fora_id) {
            return "O time mandante não pode ser igual ao time visitante.";
        }

        // Validar gols
        if($this->gols_casa < 0 || $this->gols_fora < 0) {
            return "Os gols não podem ser negativos.";
        }

        $query = "UPDATE " . $this->table_name . " 
                 SET time_casa_id=:time_casa_id, time_fora_id=:time_fora_id, 
                     data_jogo=:data_jogo, gols_casa=:gols_casa, gols_fora=:gols_fora 
                 WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->time_casa_id = htmlspecialchars(strip_tags($this->time_casa_id));
        $this->time_fora_id = htmlspecialchars(strip_tags($this->time_fora_id));
        $this->data_jogo = htmlspecialchars(strip_tags($this->data_jogo));
        $this->gols_casa = htmlspecialchars(strip_tags($this->gols_casa));
        $this->gols_fora = htmlspecialchars(strip_tags($this->gols_fora));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":time_casa_id", $this->time_casa_id);
        $stmt->bindParam(":time_fora_id", $this->time_fora_id);
        $stmt->bindParam(":data_jogo", $this->data_jogo);
        $stmt->bindParam(":gols_casa", $this->gols_casa);
        $stmt->bindParam(":gols_fora", $this->gols_fora);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return "Erro ao atualizar partida.";
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return "Erro ao excluir partida.";
    }

    public function getResultadosOptions() {
        return array(
            '' => 'Todos os resultados',
            'vitoria_casa' => 'Vitória do mandante',
            'vitoria_fora' => 'Vitória do visitante',
            'empate' => 'Empate'
        );
    }
}
?>
