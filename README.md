# Sistema de Gerenciamento de Futebol

Sistema CRUD completo para gerenciamento de times, jogadores e partidas de futebol desenvolvido em PHP com MySQL.

## 📋 Requisitos Funcionais

### Módulo Times
- ✅ RF1: Cadastrar time com nome obrigatório
- ✅ RF2: Listar times com paginação e filtro por nome
- ✅ RF3: Editar dados de um time
- ✅ RF4: Excluir time (bloquear se houver dependências)

### Módulo Jogadores
- ✅ RF5: Cadastrar jogador com nome, posição e time obrigatórios
- ✅ RF6: Validar número da camisa como inteiro entre 1 e 99
- ✅ RF7: Listar jogadores com paginação e filtros
- ✅ RF8: Editar dados do jogador
- ✅ RF9: Excluir jogador

### Módulo Partidas
- ✅ RF10: Cadastrar partida com times, data/hora e placar
- ✅ RF11: Impedir cadastramento quando mandante = visitante
- ✅ RF12: Listar partidas com paginação e filtros
- ✅ RF13: Editar dados da partida
- ✅ RF14: Excluir partida

### Regras Gerais
- ✅ RF15: Todas as listagens com paginação (10 itens/página)
- ✅ RF16: Mensagens de validação e confirmação de exclusão
- ✅ RF17: Tratamento de erros de conexão/SQL

## 🚀 Como Executar

### Pré-requisitos
- XAMPP instalado (Apache + MySQL + PHP)
- PHP 7.4 ou superior
- MySQL 5.7 ou superior

### Passo a Passo

1. **Iniciar o XAMPP**
   - Abra o XAMPP Control Panel
   - Inicie os serviços Apache e MySQL

2. **Importar o Banco de Dados**
   - Acesse o phpMyAdmin (http://localhost/phpmyadmin)
   - Crie um novo banco de dados chamado `futebol_db`
   - Importe o arquivo `db/futebol_db.sql`
   - Ou execute o script SQL diretamente no phpMyAdmin

3. **Configurar a Aplicação**
   - Copie a pasta do projeto para o diretório do XAMPP (normalmente `C:\xampp\htdocs\`)
   - Verifique as configurações de banco no arquivo `config/database.php`:
     ```php
     private $host = "localhost";
     private $db_name = "futebol_db";
     private $username = "root";
     private $password = "";
     ```
   - Ajuste se necessário (senha do MySQL, etc.)

4. **Acessar a Aplicação**
   - Abra o navegador e acesse: `http://localhost/nome-da-pasta-do-projeto/`
   - Ou se colocou diretamente em htdocs: `http://localhost/`

## 🗂️ Estrutura do Projeto

```
├── config/
│   └── database.php          # Configuração da conexão com o banco
├── models/
│   ├── Time.php             # Model para operações com times
│   ├── Jogador.php          # Model para operações com jogadores
│   └── Partida.php          # Model para operações com partidas
├── db/
│   └── futebol_db.sql       # Script SQL do banco de dados
├── index.php               # Dashboard principal
├── times.php              # Gerenciamento de times
├── jogadores.php          # Gerenciamento de jogadores
├── partidas.php           # Gerenciamento de partidas
└── README.md              # Este arquivo
```

## 🎯 Funcionalidades

### Dashboard
- Visão geral com estatísticas (total de times, jogadores, partidas)
- Navegação rápida para os módulos

### Gerenciamento de Times
- CRUD completo de times
- Validação de exclusão (impede exclusão se houver jogadores ou partidas associadas)
- Paginação e busca por nome/cidade

### Gerenciamento de Jogadores
- CRUD completo de jogadores
- Validação de número de camisa (1-99)
- Validação de posição (GOL, ZAG, LD, LE, VOL, MEI, ATA)
- Filtros por nome, posição e time
- Paginação

### Gerenciamento de Partidas
- CRUD completo de partidas
- Validação de times diferentes
- Validação de gols não negativos
- Filtros por time, período e resultado
- Exibição de resultado (vitória/empate)
- Paginação

## 🔧 Tecnologias Utilizadas

- **PHP 7.4+** - Linguagem de programação
- **MySQL** - Banco de dados
- **Bootstrap 5** - Framework CSS
- **Font Awesome** - Ícones
- **PDO** - Conexão com banco de dados
- **XAMPP** - Ambiente de desenvolvimento

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
1. Verifique se o MySQL está rodando no XAMPP
2. Confirme as credenciais no `config/database.php`
3. Certifique-se que o banco `futebol_db` existe

### Página Não Encontrada
1. Verifique se o Apache está rodando
2. Confirme que os arquivos estão na pasta correta do XAMPP

### Permissões
- Certifique-se que o Apache tem permissão para ler/gravar nos arquivos

## 📝 Script do Banco de Dados

O arquivo `db/futebol_db.sql` contém:
- Criação do banco e tabelas
- Inserção de 20 times da Série A 2025
- Inserção de jogadores exemplo para cada time
- Constraints e validações

## 👨‍💻 Testando o CRUD

### Teste de Times
1. Acesse "Times" no menu
2. Adicione um novo time
3. Edite um time existente
4. Tente excluir um time com jogadores (deve mostrar aviso)

### Teste de Jogadores
1. Acesse "Jogadores" no menu
2. Adicione um jogador com número válido
3. Tente adicionar com número inválido (deve mostrar erro)
4. Teste os filtros

### Teste de Partidas
1. Acesse "Partidas" no menu
2. Adicione uma partida com times diferentes
3. Tente adicionar com times iguais (deve mostrar erro)
4. Teste os filtros por data e resultado

## 📞 Suporte

Em caso de problemas:
1. Verifique se todos os serviços do XAMPP estão rodando
2. Confirme a configuração do banco de dados
3. Verifique os logs do Apache para erros PHP

## 📄 Licença

Este projeto é para fins educacionais.
