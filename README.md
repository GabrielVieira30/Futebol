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

