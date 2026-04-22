# Arquitetura do Sistema — ERP Estoque

## Visão Geral

Sistema ERP full-stack para gestão de estoque, desenvolvido como demonstração de boas práticas de desenvolvimento. A arquitetura segue o padrão de separação de responsabilidades, com backend em API RESTful e frontend em SPA.

```
┌─────────────────────────────────────────────────────────┐
│                     Docker Compose                       │
│                                                          │
│  ┌──────────────┐   HTTP/JSON   ┌──────────────────┐    │
│  │   Frontend   │◄─────────────►│    Backend API   │    │
│  │   Vue 3 SPA  │               │    Laravel 11    │    │
│  │  Port: 5173  │               │    Port: 8000    │    │
│  └──────────────┘               └────────┬─────────┘    │
│                                          │               │
│                                          │ TCP/3306      │
│                                  ┌───────▼──────┐        │
│                                  │   MySQL 8.0  │        │
│                                  │  Port: 3307  │        │
│                                  └──────────────┘        │
└─────────────────────────────────────────────────────────┘
```

---

## Stack Tecnológica

### Backend

| Tecnologia | Versão | Justificativa |
|------------|--------|---------------|
| PHP | 8.3 | Arrow functions, named args, fibers, readonly properties |
| Laravel | 13 | Framework maduro, DI nativo, ORM eloquente, Sanctum integrado |
| Laravel Sanctum | 4.0 | Tokens stateless para SPA — sem overhead de sessão |
| MySQL | 8.0 | Window functions, JSON nativo, índices compostos, suporte a transações ACID |

### Frontend

| Tecnologia | Versão | Justificativa |
|------------|--------|---------------|
| Vue 3 | 3.5 | Composition API, `<script setup>`, reatividade granular |
| Vite | 6 | HMR instantâneo, build ES Modules, tree-shaking agressivo |
| Tailwind CSS | 3.4 | Utilitário primeiro — design system consistente sem CSS customizado |
| Vue Router | 4.4 | Lazy loading de rotas, guards de navegação, scroll restoration |
| Axios | 1.7 | Interceptores centralizados para auth e error handling |

### Export

| Biblioteca | Uso |
|------------|-----|
| `xlsx` (SheetJS) | Geração de planilhas `.xlsx` no navegador (sem servidor) |
| `jspdf` + `jspdf-autotable` | Geração de PDFs com tabelas formatadas |

---

## Modelo de Dados

```
produtos
├── id
├── nome
├── custo_medio    ← calculado automaticamente (média ponderada)
├── preco_venda
├── estoque        ← incrementado em compras, decrementado em vendas
└── ativo          ← soft-disable sem deleção

compras                    compra_items
├── id                     ├── id
├── fornecedor             ├── compra_id ──────────► compras
└── timestamps             ├── produto_id ─────────► produtos
                           ├── quantidade
                           ├── preco_unitario  ← snapshot do custo na data
                           └── timestamps

vendas                     venda_items
├── id                     ├── id
├── cliente                ├── venda_id ───────────► vendas
├── total        ← Σ(preco_unitario × qtde)          ├── produto_id ─────────► produtos
├── lucro        ← Σ((preco - custo) × qtde)          ├── quantidade
├── cancelada              ├── preco_unitario  ← snapshot do preço de venda
└── timestamps             ├── custo_unitario  ← snapshot do custo_medio no momento
                           └── timestamps
```

### Decisão de Design: Snapshots nos Itens

Os campos `preco_unitario` e `custo_unitario` em `venda_items` são **snapshots imutáveis** registrados no momento da venda. Isso garante que:

- Relatórios históricos permanecem corretos mesmo após alterações futuras nos preços
- O lucro calculado em `vendas.lucro` sempre pode ser reconciliado com os itens individuais
- O mesmo princípio se aplica a `compra_items.preco_unitario`

---

## Regras de Negócio

### Custo Médio Ponderado

A cada entrada de estoque (compra), o custo médio é recalculado pela fórmula:

```
custo_novo = (custo_atual × estoque_atual + preco_entrada × qtde_entrada)
             ─────────────────────────────────────────────────────────────
                         (estoque_atual + qtde_entrada)
```

Implementado em `EstoqueService::registrarEntrada()` dentro de uma transação com `lockForUpdate()` para evitar race conditions em operações simultâneas.

### Cálculo de Lucro

O lucro bruto por venda é calculado item a item no momento do registro:

```
lucro_venda = Σ( (preco_unitario_item - custo_unitario_item) × quantidade )
```

O `custo_unitario` capturado é o `custo_medio` atual do produto no momento da venda.

### Cancelamento de Venda

Ao cancelar uma venda:
1. O estoque de cada item é revertido via `EstoqueService::reverterSaida()`
2. A venda recebe `cancelada = true`
3. Uma venda já cancelada não sofre reversão dupla (guard no início de `VendaService::cancelar()`)
4. O cache do dashboard é invalidado

### Estoque Crítico

Produtos com `ativo = true` e `estoque <= 5` são sinalizados como críticos no dashboard. O limite de 5 unidades é arbitrário e pode ser externalizado para configuração se necessário.

---

## Padrão de Arquitetura: Service Layer

O domínio de negócio fica nos Services, não nos Controllers. Os Controllers são responsáveis apenas por:

1. Receber o request validado (FormRequest)
2. Delegar ao Service
3. Retornar o Resource (JSON formatado)

```
HTTP Request
    │
    ▼
FormRequest ──► Valida campos, tipos, regras de negócio simples
    │
    ▼
Controller ──► Recebe dados validados, chama o Service
    │
    ▼
Service ─────► Lógica de negócio, transações, orchestração
    │
    ▼
Eloquent ORM ► Persistência, queries, relacionamentos
    │
    ▼
HTTP Response ◄── API Resource (transforma Model → JSON)
```

### Injeção de Dependência via Interface

Os Services são registrados no `AppServiceProvider` como bindings de interface:

```php
public array $bindings = [
    EstoqueServiceInterface::class => EstoqueService::class,
    CompraServiceInterface::class  => CompraService::class,
    VendaServiceInterface::class   => VendaService::class,
];
```

Isso permite substituir implementações nos testes sem alterar os Controllers.

---

## Fluxo de Autenticação (Sanctum)

```
1. POST /api/login {email, password}
        │
        ▼
2. AuthController valida credenciais com Hash::check()
        │
        ▼
3. $user->createToken('auth-token')->plainTextToken
        │
        ▼
4. Frontend armazena token no localStorage
        │
        ▼
5. Axios interceptor adiciona:
   Authorization: Bearer <token>
        │
        ▼
6. Middleware auth:sanctum valida o token
        │
        ▼
7. POST /api/logout → currentAccessToken()->delete()
   (apenas o token atual é invalidado)
```

### Segurança

- Tokens são armazenados na tabela `personal_access_tokens` (hash SHA-256)
- Rate limiting: `throttle:10,1` no login (máx. 10 tentativas/minuto por IP)
- Rate limiting: `throttle:120,1` em todas as rotas autenticadas
- CORS configurado via `config/cors.php` com `FRONTEND_URL` do `.env`

---

## Estratégia de Cache

O dashboard agrega 8+ queries e é acessado frequentemente. Para reduzir a carga no banco:

```php
$data = Cache::remember('dashboard_data', 60, function () {
    return $this->buildDashboard();
});
```

- **TTL**: 60 segundos
- **Invalidação ativa**: `Cache::forget('dashboard_data')` é chamado após qualquer registro de compra ou venda (nos respectivos Services)
- **Driver**: Configurável — padrão `file` em desenvolvimento, pode ser `redis` em produção

---

## Performance

### Índices de Banco de Dados

Migration `2026_04_21_000001_add_performance_indexes` adiciona:

| Tabela | Índice | Justificativa |
|--------|--------|---------------|
| `produtos` | `idx_produtos_nome` | Ordenação na listagem |
| `produtos` | `idx_produtos_ativo` | Filtro mais frequente |
| `produtos` | `idx_produtos_estoque` | Alerta de estoque crítico |
| `vendas` | `idx_vendas_created_at` | Gráfico de 7 dias e relatórios |
| `vendas` | `idx_vendas_cancelada` | Scope `ativa()` em todas as queries |
| `venda_items` | `idx_venda_items_produto_id` | JOIN frequente nos relatórios |
| `compras` | `idx_compras_created_at` | Relatórios temporais |

### Prevenção de N+1

- `ProdutoController::index()` não carrega relações desnecessárias — apenas os campos do produto
- `CompraController::index()` e `VendaController::index()` usam `with('itens.produto')` com eager loading explícito
- `RelatorioController` usa JOINs e `selectRaw` em vez de carregar coleções e mapear em PHP

### Pessimistic Locking

`EstoqueService::registrarEntrada()` usa `lockForUpdate()` para serializar atualizações concorrentes no custo médio:

```php
$produto = Produto::lockForUpdate()->find($produto->id);
```

Isso evita lost updates quando duas compras do mesmo produto chegam simultaneamente.

---

## Arquitetura Frontend

```
src/
├── main.js              ← Bootstrap: instancia Vue, Router, monta app
├── App.vue              ← Raiz: RouterView + layout condicional
├── router/index.js      ← Rotas com lazy loading + guard de auth + títulos
├── services/
│   ├── api.js           ← Instância Axios configurada (interceptores)
│   ├── authService.js   ← login(), getMe(), logout()
│   ├── produtoService.js
│   ├── compraService.js
│   ├── vendaService.js
│   ├── dashboardService.js
│   └── relatorioService.js
├── composables/         ← Lógica reutilizável (useAuth, etc.)
├── components/
│   ├── layout/          ← AppLayout, AppSidebar, AppNavbar
│   ├── ui/              ← BaseButton, BaseInput, BaseCard, CenterToast, etc.
│   ├── dashboard/       ← KpiCards, WeeklyChart, TopProdutos, etc.
│   ├── produtos/        ← ProdutoForm, ProdutoTable
│   ├── compras/         ← CompraForm, CompraTable
│   ├── vendas/          ← VendaForm, VendaTable, VendaResumo
│   └── relatorios/      ← ReportTable, SummaryCard, EmptyState
└── views/               ← LoginView, DashboardView, ProdutosView, etc.
```

### Roteamento e Lazy Loading

Todas as views são carregadas de forma lazy (`() => import(...)`), reduzindo o bundle inicial:

```js
{ path: '/dashboard', component: () => import('@/views/DashboardView.vue') }
```

O guard `router.beforeEach` verifica o token no `localStorage` e redireciona para `/login` se ausente. As rotas têm `meta.title` que atualiza o `document.title` automaticamente.

### Camada de Serviços

Toda comunicação com a API passa pelos arquivos em `src/services/`. Os componentes e views nunca chamam Axios diretamente. O arquivo `api.js` centraliza:

- `baseURL` via `VITE_API_URL` (env var)
- Header `Authorization: Bearer <token>` em toda requisição
- Interceptor de 401: limpa token e redireciona para login
- Interceptor de 422: desempacota `errors` em string legível
- Interceptor de erro de rede: mensagem "Sem conexão com o servidor"

---

## Testes

### Estratégia

- **Unit Tests**: testam os Services de forma isolada com banco SQLite em memória. Verificam a lógica de negócio pura (custo médio ponderado, cálculo de lucro, exceções).
- **Feature Tests**: testam os endpoints HTTP de ponta a ponta. Verificam status codes, estrutura de JSON, efeitos colaterais no banco e autenticação.

### Configuração

O `phpunit.xml` configura automaticamente para testes:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="CACHE_STORE" value="array"/>
<env name="BCRYPT_ROUNDS" value="4"/>
```

Cada teste que usa `RefreshDatabase` recria o schema do zero via migrations, garantindo isolamento total entre testes.

---

## Decisões Técnicas Documentadas

| Decisão | Alternativa Considerada | Justificativa |
|---------|-------------------------|---------------|
| Sanctum Bearer Token | JWT (tymon/jwt-auth) | Sanctum é nativo do Laravel, mais simples de manter, suporte oficial |
| Service Layer + Interfaces | Fat Controllers | Testabilidade, substituição de implementações, responsabilidade única |
| Snapshots de custo/preço nos itens | Calcular dinamicamente | Histórico imutável, relatórios consistentes, performance |
| Custo médio ponderado | FIFO / PEPS | Mais simples de implementar, adequado para volumes baixos-médios |
| Export no frontend (SheetJS/jspdf) | Export no backend | Sem carga de I/O no servidor, download imediato |
| Lazy loading de rotas | Bundle único | Carregamento inicial mais rápido, melhor UX |
| Cache de 60s no dashboard | Sem cache | Dashboard agrega 8+ queries complexas; 60s é aceitável para dados de gestão |
