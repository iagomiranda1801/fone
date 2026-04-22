# ERP Estoque

Sistema ERP de gestão de estoque desenvolvido com **Laravel 13 + Vue 3 + MySQL 8**, containerizado com **Docker**. Permite controlar produtos, compras, vendas e visualizar relatórios gerenciais com exportação em Excel e PDF.

---

## Stack

| Camada     | Tecnologia                       |
|------------|----------------------------------|
| Backend    | PHP 8.3 · Laravel 11 · Sanctum   |
| Frontend   | Vue 3 · Vite · Tailwind CSS 3    |
| Banco      | MySQL 8.0                        |
| Auth       | Laravel Sanctum (Bearer Token)   |
| Containers | Docker · Docker Compose          |

---

## Quick Start (Docker)

> Pré-requisito: Docker Desktop instalado e em execução.

```bash
# 1. Clone o repositório
git clone <url-do-repositorio> erp-estoque
cd erp-estoque

# 2. Suba os containers
docker compose up -d --build

# 3. Aguarde o banco inicializar (~10 s) e rode migrations + seed
docker exec erp-estoque-backend php artisan migrate --seed
```

Acesse:
- **Frontend**: http://localhost:5173
- **API**: http://localhost:8000/api

**Credenciais padrão**
| Campo | Valor |
|-------|-------|
| E-mail | `admin@erp.com` |
| Senha | `admin123` |

---

## Configuração Manual (sem Docker)

### Backend

```bash
cd backend
cp .env.example .env          # configure DB_* conforme seu MySQL local
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve             # http://localhost:8000
```

### Frontend

```bash
cd frontend
cp .env.example .env          # VITE_API_URL=http://localhost:8000/api
npm install
npm run dev                   # http://localhost:5173
```

---

## Variáveis de Ambiente

### Backend (`.env`)

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `DB_HOST` | Host do MySQL | `db` |
| `DB_PORT` | Porta do MySQL | `3306` |
| `DB_DATABASE` | Nome do banco | `erp_estoque` |
| `DB_USERNAME` | Usuário | `erp_user` |
| `DB_PASSWORD` | Senha | `erp_password` |
| `FRONTEND_URL` | URL do frontend (CORS) | `http://localhost:5173` |

### Frontend (`.env`)

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `VITE_API_URL` | URL base da API | `http://localhost:8000/api` |

---

## Funcionalidades

- **Dashboard** — KPIs de vendas/compras/estoque, gráfico dos últimos 7 dias, top 5 produtos mais vendidos, alertas de estoque crítico
- **Produtos** — Cadastro, edição de nome, ativar/desativar, rastreamento de custo médio ponderado
- **Compras** — Registro de compras com múltiplos itens, atualização automática do estoque e custo médio
- **Vendas** — Registro com cálculo de lucro em tempo real, preenchimento automático do preço de venda, cancelamento com reversão de estoque
- **Relatórios** — 4 relatórios filtráveis (Vendas, Compras, Estoque, Lucratividade) com exportação em **Excel (.xlsx)** e **PDF**

---

## Rodando os Testes

Os testes usam SQLite em memória — nenhuma configuração extra é necessária.

```bash
# Dentro do container Docker
docker exec erp-estoque-backend php artisan test

# Localmente
cd backend
php artisan test

# Com cobertura (requer Xdebug ou pcov)
php artisan test --coverage
```

### Suíte de Testes

| Arquivo | Tipo | O que testa |
|---------|------|-------------|
| `Unit/EstoqueServiceTest` | Unit | Entrada/saída de estoque, custo médio ponderado, exceção de estoque insuficiente |
| `Unit/CompraServiceTest` | Unit | Criação de compra, atualização de estoque, custo médio |
| `Unit/VendaServiceTest` | Unit | Cálculo de total/lucro, decremento de estoque, cancelamento, reversão |
| `Feature/AuthTest` | Feature | Login, logout, rotas protegidas, token inválido |
| `Feature/ProdutoTest` | Feature | CRUD de produtos, validações, ordenação |
| `Feature/CompraTest` | Feature | Registro de compras, validações, impacto no estoque |
| `Feature/VendaTest` | Feature | Registro de vendas, estoque insuficiente, cancelamento |
| `Feature/DashboardTest` | Feature | Estrutura da resposta, filtros de vendas canceladas |

---

## Endpoints da API

Todas as rotas (exceto `/login`) requerem o header:
```
Authorization: Bearer <token>
```

### Autenticação

| Método | Rota | Descrição |
|--------|------|-----------|
| `POST` | `/api/login` | Login — retorna token |
| `GET` | `/api/me` | Dados do usuário autenticado |
| `POST` | `/api/logout` | Invalida o token atual |

### Dashboard

| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/api/dashboard` | KPIs, gráfico 7 dias, top produtos, estoque crítico |

### Produtos

| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/api/produtos` | Lista todos os produtos |
| `POST` | `/api/produtos` | Cadastra novo produto |
| `PATCH` | `/api/produtos/{id}/nome` | Altera o nome do produto |
| `PATCH` | `/api/produtos/{id}/toggle-ativo` | Ativa/desativa produto |

### Compras

| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/api/compras` | Lista histórico de compras |
| `POST` | `/api/compras` | Registra nova compra |

### Vendas

| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/api/vendas` | Lista histórico de vendas |
| `POST` | `/api/vendas` | Registra nova venda |
| `PATCH` | `/api/vendas/{id}/cancelar` | Cancela venda e reverte estoque |

### Relatórios

| Método | Rota | Parâmetros de filtro |
|--------|------|----------------------|
| `GET` | `/api/relatorios/vendas` | `data_inicio`, `data_fim`, `cliente`, `status`, `produto_id` |
| `GET` | `/api/relatorios/compras` | `data_inicio`, `data_fim`, `fornecedor`, `produto_id` |
| `GET` | `/api/relatorios/estoque` | — |
| `GET` | `/api/relatorios/lucratividade` | `data_inicio`, `data_fim` |
| `GET` | `/api/relatorios/produtos-lista` | — |

---

## Estrutura do Projeto

```
erp-estoque/
├── docker-compose.yml
├── backend/                          # Laravel 11
│   ├── app/
│   │   ├── Exceptions/               # EstoqueInsuficienteException
│   │   ├── Http/
│   │   │   ├── Controllers/          # AuthController, ProdutoController, etc.
│   │   │   ├── Requests/             # FormRequests com validação
│   │   │   └── Resources/            # API Resources (JSON transform)
│   │   ├── Models/                   # Produto, Compra, Venda, etc.
│   │   ├── Providers/                # AppServiceProvider (DI bindings)
│   │   └── Services/                 # CompraService, VendaService, EstoqueService
│   │       └── Contracts/            # Interfaces dos services
│   ├── database/
│   │   ├── factories/                # ProdutoFactory, VendaFactory, UserFactory
│   │   ├── migrations/               # Schema do banco
│   │   └── seeders/                  # ProdutoSeeder, CompraSeeder, VendaSeeder
│   ├── routes/
│   │   └── api.php                   # Todas as rotas da API
│   └── tests/
│       ├── Feature/                  # Testes de integração HTTP
│       └── Unit/                     # Testes de unidade dos Services
└── frontend/                         # Vue 3 + Vite
    └── src/
        ├── components/               # Componentes reutilizáveis
        ├── composables/              # Lógica reutilizável (Vue hooks)
        ├── router/                   # Vue Router 4 com lazy loading
        ├── services/                 # Camada de chamadas à API (Axios)
        └── views/                    # Páginas da aplicação
```

---

## Seed de Demonstração

O seed cria dados realistas para demonstração imediata:

- **12 produtos** (periféricos e eletrônicos)
- **15 compras** distribuídas nos últimos 28 dias (múltiplos fornecedores)
- **20 vendas** nos últimos 23 dias (3 canceladas)
- **1 usuário administrador** (`admin@erp.com` / `admin123`)

Após o seed o dashboard exibirá gráficos populados, KPIs calculados e alertas de estoque crítico.

---

## Licença

MIT
