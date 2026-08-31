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

- [Docker](https://www.docker.com/) + Docker Compose. **Só isso** — o fluxo
  abaixo não precisa de PHP, Composer nem Node instalados na máquina; tudo
  roda dentro de containers (o próprio Composer/Node usados no bootstrap vêm
  de imagens Docker descartáveis).

> Já tem PHP 8.3+/Composer/Node instalados e prefere usá-los direto (mais
> rápido)? Veja [Alternativa com PHP/Node no host](#alternativa-com-phpnode-no-host).

## Primeira execução (pasta zerada / clone novo)

Time usa Docker como único requisito — nenhum passo abaixo toca em PHP/Node
da máquina.

1. **Clonar e entrar no projeto:**
   ```bash
   git clone <url-do-repo> portal-invest-bahia
   cd portal-invest-bahia
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

   # Portas expostas no host (ajuste se já estiverem em uso na sua máquina).
   APP_PORT=8080
   FORWARD_DB_PORT=5433
   ```

3. **Rodar o `composer install` inicial via container descartável**
   (isso cria `vendor/`, incluindo `vendor/bin/sail` — é o único jeito de
   começar sem PHP no host, já que o próprio Sail vive dentro do `vendor/`):
   ```bash
   docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html \
     laravelsail/php84-composer:latest composer install --no-scripts --ignore-platform-reqs
   ```
   > `--no-scripts` é necessário: essa imagem é só para resolver dependências,
   > não tem as extensões da aplicação (gd, intl...) para rodar o
   > `artisan package:discover` automático do Composer. Sem problema — isso
   > roda naturalmente no primeiro comando `sail artisan` do passo 5.

4. **Subir os containers (Sail):**
   ```bash
   ./vendor/bin/sail up -d
   ```
   > Se aparecer o erro `Docker or Podman is not running`, seu contexto do
   > Docker pode estar apontando para o Docker Desktop indisponível. Troque
   > para o contexto padrão do sistema: `docker context use default`.

5. **Gerar a chave da aplicação e instalar as dependências JS**
   (o container já tem PHP, Composer *e* Node/npm embutidos — tudo via `sail`):
   ```bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail npm install
   ```

6. **Rodar as migrations e o seeder:**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```
   O seeder cria os papéis (`admin`, `broker`), um administrador, um corretor
   de exemplo e as 6 páginas fixas do site (Início, Fazenda, Ativo, Serviço,
   Informações, Contatos).

7. **Criar o link de storage** (necessário para as imagens dos anúncios/eventos
   aparecerem no site público):
   ```bash
   ./vendor/bin/sail artisan storage:link
   ```

8. **Compilar os assets front-end:**
   ```bash
   ./vendor/bin/sail npm run build
   ```
   Para desenvolvimento com hot-reload, use `./vendor/bin/sail npm run dev`
   em outro terminal.

Pronto — acesse:

- **Site público:** http://localhost:8080/pt (também `/en`, `/es`, `/it`)
- **Painel administrativo:** http://localhost:8080/admin
- **Caixa de e-mails local (Mailpit):** http://localhost:8025

O serviço `queue` processa em segundo plano os e-mails de recuperação de
senha. Em desenvolvimento, as mensagens ficam somente no Mailpit e não são
enviadas para endereços reais. Em produção, configure as variáveis `MAIL_*`
com o SMTP do provedor e mantenha o serviço `queue` em execução.

### Credenciais padrão (seeder)

| Papel | E-mail | Senha |
|---|---|---|
| Administrador | `admin@pib.com.br` | `password` |
| Corretor (exemplo) | `corretor@pib.com.br` | `password` |

> Troque essas senhas antes de qualquer uso além do ambiente local (ou
> defina `PIB_ADMIN_PASSWORD`/`PIB_BROKER_PASSWORD` no `.env` antes do seed —
> ver [Regras de negócio importantes](#regras-de-negócio-importantes)).

### Se o container ficar em loop de erro logo na primeira subida

Se `sail up -d` funcionar mas o site não responder (container reiniciando),
rode `docker logs portal-invest-bahia-laravel.test-1` para ver o motivo. O
Supervisor do container desiste de reiniciar sozinho depois de algumas
tentativas rápidas — resolvido o problema (ex.: rodando o passo que faltava),
um `docker restart portal-invest-bahia-laravel.test-1` retoma normalmente.

## Comandos do dia a dia

Todos os comandos `artisan`, `composer` e `npm` devem rodar **dentro do
Sail** para usar o PHP/Node do container:

```bash
./vendor/bin/sail up -d              # subir os containers em background
./vendor/bin/sail down                # parar os containers
./vendor/bin/sail artisan tinker      # REPL do Laravel
./vendor/bin/sail artisan test        # rodar a suíte de testes
./vendor/bin/sail artisan queue:failed # consultar e-mails/jobs que falharam
./vendor/bin/sail artisan queue:retry all # tentar novamente os jobs com falha
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

## Alternativa com PHP/Node no host

Se sua máquina já tem PHP 8.3+, Composer e Node instalados, dá pra pular os
containers descartáveis do passo 3 e rodar direto (mais rápido):
```bash
composer install
cp .env.example .env   # e ajuste as variáveis (passo 2 acima)
php artisan key:generate
npm install
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link
./vendor/bin/sail npm run build
```
A partir do `sail up -d`, o resto é idêntico ao fluxo padrão — só os passos
1-3 (que criam `vendor/`/`node_modules/` e a `APP_KEY`) rodam no host em vez
de containers descartáveis.

## Deploy em produção

Ver [docker/production/README.md](docker/production/README.md) — stack
Nginx + PHP-FPM + PostgreSQL via `compose.prod.yaml`, com backups automáticos
e instruções de deploy em VPS.
