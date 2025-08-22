1. config/database.php
# Projeto CRUD em PHP + MySQL

## 🚀 Como rodar no XAMPP

1. **Iniciar o XAMPP**  
   - Ative **Apache** e **MySQL** no painel de controle.  

2. **Configurar o projeto**  
   - Coloque a pasta do projeto em:  
     ```
     C:\xampp\htdocs\nome_do_projeto
     ```
   - Acesse no navegador:  
     ```
     http://localhost/nome_do_projeto
     ```

3. **Banco de Dados**  
   - Abra [phpMyAdmin](http://localhost/phpmyadmin).  
   - Crie um banco de dados (ex: `meu_banco`).  
   - Importe o script SQL que está em:  
     ```
     /database/script.sql
     ```

4. **Configuração de conexão**  
   - No arquivo `conexao.php`, ajuste os dados:  
     ```php
     $host = "localhost";
     $user = "root";     
     $pass = "root";         
     $db   = "meu_banco";
     ```

5. **Testando o CRUD**  
   - **Create** → Cadastrar registros.  
   - **Read** → Listar registros.  
   - **Update** → Editar registros.  
   - **Delete** → Excluir registros.  

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



 O que é o PDO?

PDO (PHP Data Objects) é uma biblioteca do PHP que serve para conectar e trabalhar com bancos de dados.
Ele é tipo um tradutor universal:

Você escreve código em PHP.

O PDO entende esse código.

O PDO manda os comandos para o banco (MySQL, PostgreSQL, Oracle, etc).

Ou seja: você usa sempre a mesma sintaxe, não importa o banco.

📝 Explicando o código linha por linha
Declaração da classe
class Database {


Aqui foi criada uma classe chamada Database. Ela serve para organizar toda a lógica da conexão em um único lugar.

Variáveis de configuração
private $host = "localhost";
private $db_name = "futebol_db";
private $username = "root";
private $password = "";
public $conn;


host → onde está o banco de dados (no caso, na própria máquina = localhost).

db_name → nome do banco (aqui é futebol_db).

username → usuário do banco (normalmente root em ambiente local).

password → senha do usuário (em branco, pois no XAMPP/WAMP muitas vezes não tem senha).

conn → a variável que vai guardar a conexão ativa com o banco.

Função de conexão
public function getConnection(){
    $this->conn = null;


A função getConnection() é usada quando você quer se conectar ao banco.

Primeiro, define $this->conn = null; (garante que não tinha nenhuma conexão anterior ativa).

Tentando a conexão com PDO
try {
    $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                           $this->username, 
                           $this->password);


new PDO(...) → cria uma nova conexão.

"mysql:host=...;dbname=..." → aqui você fala qual banco está usando (MySQL), onde ele está (host) e qual banco dentro dele (dbname).

$this->username e $this->password → enviam o login e senha.

Se der certo → $this->conn vai guardar a conexão.

Ajustando a codificação
$this->conn->exec("set names utf8");


Isso garante que os dados (como nomes de jogadores com acentos) serão salvos e lidos corretamente em UTF-8.

Capturando erros
} catch(PDOException $exception) {
    echo "Erro de conexão: " . $exception->getMessage();
}


Se a conexão falhar (senha errada, banco não existe, etc), o catch captura o erro.

PDOException → é o tipo de erro que o PDO retorna.

getMessage() → mostra qual foi o erro.

Retorno da conexão
return $this->conn;


No final, a função devolve a conexão pronta para ser usada em outras partes do código.

 Por que usar PDO é uma boa prática?

Suporta vários bancos (MySQL, SQLite, PostgreSQL, etc).

Mais seguro → permite usar prepared statements, que protegem contra ataques de SQL Injection.

Mais organizado → separa bem a lógica de conexão da lógica do sistema.

Portabilidade → se mudar de MySQL para PostgreSQL, muda só a string da conexão, e o resto do código continua funcionando.

 Exemplo de uso em outra parte do projeto:

$database = new Database();
$db = $database->getConnection();


Aqui ele chama a classe Database e pega a conexão $db.
Depois, pode usar $db->query(...) ou $db->prepare(...) para executar comandos SQL.






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




 O que é uma “action” em PHP?

Imagine o seu sistema como uma fábrica de ações.
Cada vez que alguém clica em um botão ou acessa um link, uma ação acontece.

Em PHP, uma action é simplesmente o que o código faz quando é chamado. Pode ser:

Mostrar dados (ler times ou jogadores)

Salvar algo no banco (inserir um novo jogador)

Deletar ou atualizar algo

No seu projeto, todas as actions estão relacionadas aos arquivos PHP e métodos que você criou.

2️⃣ index.php → menu do sistema
echo "<li><a href='times.php'>Times</a></li>";


Aqui o link é uma ação do usuário: “mostrar todos os times”.

Quando você clica, o navegador pede para o PHP executar o arquivo times.php.

O PHP então faz a action: ler os times do banco e mostrar na tela.

📌 Resumo: cada link do menu é um gatilho de action.

3️⃣ config/database.php → conectar ao banco
$database = new Database();
$db = $database->getConnection();


$database->getConnection() → ação de se conectar ao banco.

Resultado: você tem um objeto $db que permite consultar, inserir, atualizar ou deletar dados.

Sem essa action, você não consegue acessar o banco.

4️⃣ models/Time.php → representar e ler dados
public function read(){
    $query = "SELECT * FROM " . $this->table_name;
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
}


read() → ação de buscar todos os registros da tabela times.

$stmt->fetch(PDO::FETCH_ASSOC) → ação de percorrer os resultados do banco, linha por linha.

O extract($row) transforma os nomes das colunas em variáveis PHP.

Depois, você mostra os dados na tela.

📌 Resumo: o método read() é uma action do sistema para ler dados do banco e exibir.

5️⃣ times.php → executar a action de ler e mostrar
$time = new Time($db); // cria o objeto
$stmt = $time->read();  // ação: ler todos os times

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    echo "ID: $id - Nome: $nome - Cidade: $cidade<br>";
}


Passo a passo da action:

Cria objeto da classe Time → prepara o “ator” que vai executar a ação.

Chama $time->read() → o ator vai ao banco buscar os dados.

Loop while → percorre os dados que o ator trouxe.

echo → exibe os dados na tela.

✅ Resumo: cada linha dentro do while faz parte da action de ler e mostrar os times.

6️⃣ Teste da conexão: test_db.php
if($db){
    echo "Conexão bem-sucedida!";
} else {
    echo "Erro na conexão.";
}


Aqui a action é: testar a conexão com o banco e exibir o resultado.

Resultado: você sabe se o sistema consegue acessar o banco antes de qualquer outra ação.

7️⃣ Resumindo todas as actions do sistema
Arquivo	Ação (Action) executada
index.php	Menu → clicar em links para abrir outros arquivos
times.php	Ler todos os times do banco e mostrar na tela
jogadores.php	Ler todos os jogadores e mostrar
partidas.php	Ler todas as partidas e mostrar
config/database.php	Conectar ao banco de dados
models/*.php	Métodos read() → buscar dados do banco
test_db.php	Testar se a conexão com o banco funciona

Cada “action” é uma função que executa algo específico no sistema. Mesmo sem formular <form>, todas as ações são disparadas quando você acessa os arquivos ou chama métodos.