# Deploy em produção — PIB (Portal Invest Bahia)

Stack de produção: **Nginx + PHP-FPM (Laravel) + worker de filas + PostgreSQL**,
orquestrada por `compose.prod.yaml`. Alvo: VPS no Brasil, domínio `.com.br`.

## Pré-requisitos no servidor
- Docker + Docker Compose.
- Um proxy TLS na frente (recomendado): Caddy, Traefik ou Nginx do host com
  Let's Encrypt, encaminhando 443 → porta 80 do serviço `web`.

## Passos

1. Clonar o repositório e preparar o `.env`:
   ```sh
   cp .env.production.example .env
   # edite DB_PASSWORD, APP_URL, MAIL_*, etc.
   ```
   **Obrigatório:** defina `PIB_ADMIN_PASSWORD` e `PIB_BROKER_PASSWORD` com
   senhas próprias — o `db:seed` (passo 3) **recusa rodar em produção** se
   alguma delas ainda for a senha padrão de desenvolvimento (`password`).

2. Subir a stack (build das imagens app + web):
   ```sh
   docker compose -f compose.prod.yaml up -d --build
   ```
   O `entrypoint` roda `migrate --force`, cria o `storage:link` e faz cache de
   config/rotas/views automaticamente. O serviço `queue` envia os e-mails em
   segundo plano e reinicia periodicamente para carregar novos deploys.

3. Gerar a `APP_KEY` (se ainda não definida) e o usuário admin:
   ```sh
   docker compose -f compose.prod.yaml exec app php artisan key:generate
   docker compose -f compose.prod.yaml exec app php artisan db:seed --force
   ```
   O admin é criado com o e-mail/senha definidos em `PIB_ADMIN_EMAIL`/
   `PIB_ADMIN_PASSWORD` no `.env` (não há mais senha padrão em produção).

## Persistência
- `pib_pgsql` — dados do PostgreSQL.
- `pib_storage` — uploads (imagens dos anúncios/eventos). Montado no `app` (rw) e
  no `web` (ro), servido via symlink `public/storage`.
- `pib_backups` — dumps automáticos do banco.

## Backups
O serviço `backup` faz `pg_dump` diário comprimido em `pib_backups`, retendo 14
dias. Para restaurar:
```sh
gunzip -c /caminho/pib-AAAAMMDD-HHMM.sql.gz | \
  docker compose -f compose.prod.yaml exec -T pgsql psql -U pib -d pib
```
Recomenda-se também copiar o volume `pib_storage` para armazenamento externo
periodicamente (as imagens não estão no dump do banco).

## Atualizações
```sh
git pull
docker compose -f compose.prod.yaml up -d --build
```
Cada deploy reconstrói assets e dependências; o `entrypoint` reaplica migrations
e recria os caches.
