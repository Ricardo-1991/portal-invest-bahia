# PIB — Portal Invest Bahia

Portal de classificados multilíngue (PT/EN/ES/IT) para corretores de
propriedades rurais (fazendas), ativos e serviços na Bahia. MVP com site
público + painel administrativo (Filament).

**Stack:** Laravel 13 + Filament 5 (admin) · Blade + Tailwind v4 + Alpine.js
(site público) · PostgreSQL · Docker (Laravel Sail no desenvolvimento).

## Já configurado? Só suba o container

Se `vendor/`, `node_modules/`, `.env` e o banco **já existem** (é o caso deste
projeto agora, ou de quem já rodou a "Primeira execução" abaixo alguma vez),
não precisa repetir composer/npm/migrate — é só:

```bash
./vendor/bin/sail up -d
```

Isso **é**, na prática, um `docker compose up -d` (o Sail é só um wrapper fino
em cima do `compose.yaml`, que já carrega o `.env` do projeto automaticamente).

### Por que não dá pra usar só `docker compose up` numa pasta zerada

O `compose.yaml` de desenvolvimento **monta** `vendor/` e `node_modules/` do
host como volume dentro do container, em vez de "assar" essas dependências na
imagem — é assim que o Sail permite instalar pacotes/hot-reload sem rebuildar
a imagem a cada mudança. Ou seja: **se essas pastas ainda não existem**, subir
o container não resolve sozinho — a aplicação sobe mas quebra na hora (sem
autoload do Composer, sem `APP_KEY`, sem schema migrado, sem CSS/JS
compilado). Por isso a "Primeira execução" abaixo faz `composer install` e
`npm install` **antes** de subir o container.

> Isso é diferente do `compose.prod.yaml` de produção: o Dockerfile de lá faz
> um build multi-estágio que realmente empacota composer + npm **dentro** da
> imagem, então `docker compose -f compose.prod.yaml up --build` é
> autossuficiente (ver [Deploy em produção](#deploy-em-produção)).

## Requisitos

- [Docker](https://www.docker.com/) + Docker Compose
- PHP 8.3+ e [Composer](https://getcomposer.org/) 2.x — usados **só** para o
  `composer install` inicial; depois disso todo o código roda dentro dos
  containers do Sail (PHP 8.5), não precisa do PHP do host para mais nada.
- [Node.js](https://nodejs.org/) 20+ e NPM

> Sem PHP/Composer instalados localmente? Veja a alternativa em
> [Sem PHP local](#sem-php-local-alternativa) mais abaixo.

## Primeira execução (pasta zerada / clone novo)

1. **Instalar as dependências PHP:**
   ```bash
   composer install
   ```

2. **Criar o `.env`** a partir do exemplo e ajustar as variáveis abaixo:
   ```bash
   cp .env.example .env
   ```
   Edite o `.env` e defina:
   ```env
   APP_NAME=PIB
   APP_URL=http://localhost:8080
   APP_LOCALE=pt
   APP_FALLBACK_LOCALE=pt

   DB_CONNECTION=pgsql
   DB_HOST=pgsql
   DB_PORT=5432
   DB_DATABASE=laravel
   DB_USERNAME=sail
   DB_PASSWORD=password

   # Portas expostas no host (ajuste se 80/5432 já estiverem em uso na sua máquina).
   APP_PORT=8080
   FORWARD_DB_PORT=5433
   ```
   Gere a chave da aplicação:
   ```bash
   php artisan key:generate
   ```

3. **Instalar as dependências JS:**
   ```bash
   npm install
   ```

4. **Subir os containers (Sail):**
   ```bash
   ./vendor/bin/sail up -d
   ```
   > Se aparecer o erro `Docker or Podman is not running`, seu contexto do
   > Docker pode estar apontando para o Docker Desktop indisponível. Troque
   > para o contexto padrão do sistema: `docker context use default`.

5. **Rodar as migrations e o seeder:**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```
   O seeder cria os papéis (`admin`, `broker`), um administrador, um corretor
   de exemplo e as 6 páginas fixas do site (Início, Fazenda, Ativo, Serviço,
   Informações, Contatos).

6. **Criar o link de storage** (necessário para as imagens dos anúncios/eventos
   aparecerem no site público):
   ```bash
   ./vendor/bin/sail artisan storage:link
   ```

7. **Compilar os assets front-end:**
   ```bash
   ./vendor/bin/sail npm run build
   ```
   Para desenvolvimento com hot-reload, use `./vendor/bin/sail npm run dev`
   em outro terminal.

Pronto — acesse:

- **Site público:** http://localhost:8080/pt (também `/en`, `/es`, `/it`)
- **Painel administrativo:** http://localhost:8080/admin

### Credenciais padrão (seeder)

| Papel | E-mail | Senha |
|---|---|---|
| Administrador | `admin@pib.com.br` | `password` |
| Corretor (exemplo) | `corretor@pib.com.br` | `password` |

> Troque essas senhas antes de qualquer uso além do ambiente local.

## Comandos do dia a dia

Todos os comandos `artisan`, `composer` e `npm` devem rodar **dentro do
Sail** para usar o PHP/Node do container:

```bash
./vendor/bin/sail up -d              # subir os containers em background
./vendor/bin/sail down                # parar os containers
./vendor/bin/sail artisan tinker      # REPL do Laravel
./vendor/bin/sail artisan test        # rodar a suíte de testes
./vendor/bin/sail composer require ...
./vendor/bin/sail npm run dev|build
```

Dica: crie um alias `sail='./vendor/bin/sail'` no seu shell para encurtar.

## Testes

```bash
./vendor/bin/sail artisan test
```
Suíte atual: 16 testes cobrindo autenticação/permissões do painel, regras de
publicação multilíngue, geração de PDF e o site público (i18n, busca,
fallback de idioma).

## Estrutura do projeto (visão geral)

- `app/Filament/` — Resources do painel admin (Listing, Event, PageContent,
  User) e os traits de tradução (`LocaleTabs`, `LoadsTranslations`,
  `GuardsListingPublication`).
- `app/Http/Controllers/PublicController.php` — rotas públicas
  (`/{locale}/...`), busca e paginação.
- `app/Models/` — `Listing`, `Event`, `PageContent` (traduzíveis via
  `spatie/laravel-translatable`) e `User` (papéis via `spatie/laravel-permission`).
- `resources/views/public/` — site público (Blade + Tailwind + Alpine).
- `resources/views/pdf/listing.blade.php` — PDF interno do classificado
  (gerado via `barryvdh/laravel-dompdf`, ação disponível só no admin).
- `docker/production/` — Dockerfile e `README.md` com o passo a passo de
  **deploy em produção** (Nginx + PHP-FPM + PostgreSQL, backups, VPS `.com.br`).

## Regras de negócio importantes

- **Idiomas:** PT, EN, ES, IT. O visitante escolhe livremente o idioma
  (`/pt`, `/en`, `/es`, `/it`); cada anúncio precisa ter título e descrição
  preenchidos em **pelo menos um** idioma para ser publicado (o corretor
  publica no idioma dele; o site cai para português e, se preciso, para
  qualquer idioma preenchido, em vez de mostrar texto em branco).
- **Papéis:** `admin` (acesso total) e `broker` (só enxerga/edita os próprios
  classificados).
- **Imagens:** sempre em disco `public` (`storage/app/public`, servido via
  `public/storage`). Coleções de mídia sem `->useDisk('public')` explícito
  ficam inacessíveis no site (ver comentário nos models `Listing`/`Event`).

## Sem PHP local (alternativa)

Se sua máquina não tem PHP/Composer, use um container temporário para o
`composer install` inicial:
```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html \
  laravelsail/php84-composer:latest composer install --ignore-platform-reqs
```
Depois siga normalmente a partir do passo 2 (criar `.env`).

## Deploy em produção

Ver [docker/production/README.md](docker/production/README.md) — stack
Nginx + PHP-FPM + PostgreSQL via `compose.prod.yaml`, com backups automáticos
e instruções de deploy em VPS.
