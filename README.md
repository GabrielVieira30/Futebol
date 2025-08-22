1. config/database.php

Este arquivo é responsável pela conexão com o banco de dados.

<?php
class Database {
    private $host = "localhost";
    private $db_name = "futebol_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection(){
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
            $this->username, $this->password);
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }

        return $this->conn;
    }
}


 Explicação linha a linha:

class Database  → define uma classe chamada Database, usada para organizar o código da conexão.

private $host = "localhost"; → endereço do servidor MySQL (aqui o banco está rodando localmente).

private $db_name = "futebol_db"; → nome do banco de dados que será usado.

private $username = "root"; → usuário do banco de dados (padrão do MySQL local).

private $password = ""; → senha do banco (vazio aqui).

public $conn; → variável que vai guardar a conexão ativa.

public function getConnection(){ ... } → método que cria a conexão com o banco.

try { ... } catch(PDOException $exception) { ... } → tenta conectar; se der erro, mostra a mensagem.

new PDO("mysql:host=...") → cria a conexão PDO, que é a forma segura e moderna de se conectar ao MySQL em PHP.

 Esse arquivo é importado em outros lugares, sempre que for necessário acessar o banco.

2. db/futebol_db.sql

Arquivo SQL usado para criar o banco e as tabelas.

CREATE DATABASE futebol_db;
USE futebol_db;

CREATE TABLE times (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL
);

CREATE TABLE jogadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    posicao VARCHAR(30) NOT NULL,
    numero_camisa INT NOT NULL,
    time_id INT,
    FOREIGN KEY (time_id) REFERENCES times(id)
);

CREATE TABLE partidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    time_casa_id INT,
    time_fora_id INT,
    data DATE,
    local VARCHAR(100),
    FOREIGN KEY (time_casa_id) REFERENCES times(id),
    FOREIGN KEY (time_fora_id) REFERENCES times(id)
);

 Explicação:

CREATE DATABASE futebol_db; → cria o banco com esse nome.

USE futebol_db; → seleciona esse banco para os próximos comandos.

CREATE TABLE times (...) → cria tabela dos times (id, nome, cidade).

CREATE TABLE jogadores (...) → cria tabela dos jogadores, vinculando cada jogador a um time (FOREIGN KEY (time_id)).

CREATE TABLE partidas (...) → cria tabela das partidas, com times da casa e fora, data e local.

 Essas tabelas formam o coração do sistema.

3. models/Time.php

Classe que representa um Time.

<?php
class Time {
    private $conn;
    private $table_name = "times";

    public $id;
    public $nome;
    public $cidade;

    public function __construct($db){
        $this->conn = $db;
    }

    public function read(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}


 Explicação:

class Time { → define a classe para lidar com times.

private $conn; → armazena a conexão com o banco.

private $table_name = "times"; → define o nome da tabela usada.

public $id; public $nome; public $cidade; → representam as colunas do banco.

__construct($db) → o construtor recebe a conexão com o banco.

read() → busca todos os registros da tabela times.

4. models/Jogador.php

Classe que representa um Jogador.

<?php
class Jogador {
    private $conn;
    private $table_name = "jogadores";

    public $id;
    public $nome;
    public $posicao;
    public $numero_camisa;
    public $time_id;

    public function __construct($db){
        $this->conn = $db;
    }

    public function read(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}


 Explicação:

Estrutura idêntica ao Time.php, mas adaptada para a tabela jogadores.

Possui atributos extras como posicao e numero_camisa.

O método read() retorna todos os jogadores cadastrados.

5. models/Partida.php

Classe que representa uma Partida.

<?php
class Partida {
    private $conn;
    private $table_name = "partidas";

    public $id;
    public $time_casa_id;
    public $time_fora_id;
    public $data;
    public $local;

    public function __construct($db){
        $this->conn = $db;
    }

    public function read(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}


 Explicação:

Mesma estrutura, mas aplicada à tabela partidas.

Cada partida tem time_casa_id, time_fora_id, data e local.

O read() busca todas as partidas.

6. index.php

Arquivo principal da aplicação.

<?php
echo "<h1>Sistema de Futebol</h1>";
echo "<ul>";
echo "<li><a href='times.php'>Times</a></li>";
echo "<li><a href='jogadores.php'>Jogadores</a></li>";
echo "<li><a href='partidas.php'>Partidas</a></li>";
echo "</ul>";


 Explicação:

echo "<h1>Sistema de Futebol</h1>"; → mostra o título.

Cria uma lista de links para navegar entre Times, Jogadores e Partidas.

É como a “página inicial” ou “menu principal” do sistema.

7. times.php
<?php
include_once 'config/database.php';
include_once 'models/Time.php';

$database = new Database();
$db = $database->getConnection();

$time = new Time($db);
$stmt = $time->read();

echo "<h1>Times</h1>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    echo "ID: $id - Nome: $nome - Cidade: $cidade<br>";
}


 Explicação passo a passo:

include_once 'config/database.php'; → importa a conexão com o banco.

include_once 'models/Time.php'; → importa a classe Time.

$database = new Database(); → cria o objeto de conexão.

$db = $database->getConnection(); → abre a conexão.

$time = new Time($db); → cria um objeto da classe Time.

$stmt = $time->read(); → busca todos os times do banco.

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ... } → percorre os resultados.

extract($row); → transforma colunas do banco em variáveis PHP.

echo "ID: $id - Nome: $nome - Cidade: $cidade<br>"; → exibe cada time.

 Esse padrão se repete em jogadores.php e partidas.php.

8. test_db.php
<?php
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if($db){
    echo "Conexão bem-sucedida!";
} else {
    echo "Erro na conexão.";
}


 Explicação:

Importa a classe Database.

Tenta conectar ao banco.

Se a conexão existir, mostra “Conexão bem-sucedida!”.

Caso contrário, mostra erro.

 Serve para testar rapidamente se o banco está configurado certo.