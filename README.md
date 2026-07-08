# Univille Bank

Mini painel de conciliacao financeira com backend em Laravel e frontend em Vue 3.

## Objetivo do projeto

Este projeto implementa o teste pratico proposto pela Univille: uma API em Laravel 9+ e uma interface em Vue 3 capazes de importar transacoes de uma fonte externa simulada, processa-las de forma assincrona com filas e disponibilizar um dashboard autenticado para consulta, acompanhamento e filtragem.

## Estrutura

- `backend/`: API Laravel com autenticacao via Sanctum, ingestao assincrona, jobs em fila e SQLite.
- `frontend/`: SPA em Vue 3 com Composition API, dashboard autenticado, filtros, paginacao e short polling.

## Tecnologias

- PHP 8.3
- Laravel 13
- Laravel Sanctum
- SQLite
- Queues com driver `database`
- Vue 3
- Vite
- Tailwind CSS 4 integrado ao Vite

## Arquitetura do backend

O backend foi organizado em camadas para evidenciar separacao de responsabilidades:

- `app/Domain/Transactions`: enums, contratos, DTOs, value objects e excecoes do dominio.
- `app/Application/Transactions`: use cases e coordenacao do fluxo de importacao/processamento.
- `app/Infrastructure`: cliente de origem externa e repositorio de consulta com Eloquent.
- `app/Http`: controllers, requests e resources.

### Fluxo de ingestao

1. Um comando Artisan ou endpoint autenticado inicia a importacao.
2. O cliente `LocalJsonExternalTransactionsClient` le o arquivo mock em `backend/storage/app/mock-transactions.json`.
3. Cada item recebido gera um registro `pending` em `transactions` e um `ProcessTransactionJob` independente.
4. O job processa o payload com retry (`tries = 3`, `backoff = [5, 15, 30]`), marca itens invalidos/corrompidos e registra falhas finais.
5. Quando a origem externa fica indisponivel ou ilegivel, a API responde de forma controlada e o lote e marcado como `failed`.
6. O lote de importacao consolida contadores de pendentes, processadas, falhas e invalidas.

## Instalacao e execucao

### Fluxo rapido de validacao

1. Suba o backend com `php artisan serve` em `backend/`.
2. Suba o worker com `php artisan queue:work` em `backend/`.
3. Suba o frontend com `npm run dev` em `frontend/`.
4. Acesse `http://127.0.0.1:5173`.
5. Crie um usuario, faca login e clique em `Importar transacoes`.
6. Verifique os indicadores, filtros e a listagem paginada no dashboard.

### Backend

1. Entre em `backend/`.
2. Execute `composer install`.
3. Garanta que o arquivo `.env` exista. Se necessario, copie de `.env.example`.
4. Confirme os valores principais no `.env`:
   - `DB_CONNECTION=sqlite`
   - `DB_DATABASE=database/database.sqlite`
   - `QUEUE_CONNECTION=database`
5. Se o arquivo `database/database.sqlite` nao existir, crie-o.
6. Execute `php artisan key:generate`.
7. Execute `php artisan migrate`.
8. Suba a API com `php artisan serve`.
9. Em outro terminal, suba o worker com `php artisan queue:work`.

### Frontend

1. Entre em `frontend/`.
2. Execute `npm install`.
3. Copie `.env.example` para `.env` se quiser customizar:
   - `VITE_API_BASE_URL=/api`
   - `VITE_BACKEND_URL=http://127.0.0.1:8000`
4. Execute `npm run dev`.
5. Gere a build de producao com `npm run build`.

## Autenticacao

Todas as rotas consumidas pelo dashboard usam token Bearer do Sanctum.

Fluxo:

1. Cadastro em `POST /api/register`
2. Login em `POST /api/login`
3. Armazenamento local do token no frontend
4. Envio do token nas chamadas autenticadas
5. Logout em `POST /api/logout`

## Rotas principais da API

Publicas:

- `POST /api/register`
- `POST /api/login`

Protegidas:

- `GET /api/user`
- `POST /api/logout`
- `POST /api/transactions/import`
- `GET /api/transactions`
- `GET /api/dashboard/summary`

### Filtros suportados em `GET /api/transactions`

- `status`
- `start_date`
- `end_date`
- `min_amount`
- `max_amount`
- `per_page`

## Fonte de dados externa simulada

As transacoes de entrada estao em `backend/storage/app/mock-transactions.json`.

O arquivo contem exemplos validos e invalidos para demonstrar:

- processamento normal
- deteccao de payload corrompido
- consolidacao de metricas no dashboard
- tratamento de indisponibilidade da origem externa

## Comando Artisan

Tambem e possivel disparar a ingestao pelo terminal:

```bash
php artisan transactions:import
```

Opcoes:

- `--source=mock`
- `--limit=10`

## Dashboard

O frontend entrega:

- listagem paginada das transacoes
- filtros por status, periodo e faixa de valores
- indicadores de pendentes, processadas, falhas, invalidas e total
- acao para disparar nova importacao
- atualizacao automatica por short polling a cada 15 segundos

## Observacoes para avaliacao

- O backend usa SQLite e fila com driver `database`.
- Os valores monetarios sao persistidos em centavos via `amount_cents`.
- A autenticacao da API utiliza Laravel Sanctum com Bearer Token.
- O frontend usa Vue 3 com `script setup`, Vite e Tailwind CSS 4.

## Testes executados

Backend validado com:

```bash
php artisan migrate:fresh
php artisan route:list --path=api
php artisan test tests/Feature/AuthApiTest.php tests/Feature/TransactionImportTest.php
npm run build
```

Os testes cobrem:

- cadastro, login e logout
- importacao autenticada
- indisponibilidade controlada da origem externa
- consolidacao do summary
- filtros de listagem