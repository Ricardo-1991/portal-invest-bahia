TIPO DE NEGÓCIO: Portal/Classificado - CORRETORES ----------------------------------

REQUISITOS BÁSICOS: 1 + 5 usuários, sendo um estrangeiro(italiano), fazendo divulgação de ativos no site -> 1 Administrador Geral Login - Auth

Línguas - Port, Eng, Esp, Ita

LOGO - PIB (Portal Invest Bahia)

Paleta - Marrom - Dourado - Amarelo (cacau, ouro)

Postagens Geral

Abas: Fazenda - Ativo - Serviço - Informações - Contatos

\-------------------------------------

PONTOS FUNCIONAIS:

Usuário poderá fazer login no sistema e poderá criar/editar/excluir (por ID/Sessão) um classificado com Título, subtítulo, descrição, imagem e informações do contato do corretor Usuário poderá ser contatado por e-mail e redes sociais, caso o cliente se interesse pelo anúncio. Usuário poderá adicionar novas imagens em um Carrossel de informações de eventos (título, subtítulo, descrição, imagem) sobre o nicho do negócio -> Utilizar Modal ao clicar?

Cliente poderá exportar um PDF com todas as informações adicionais do ativo CLiente poderá enviar um e-mail para o corretor Cliente poderá entrar em contato via redes sociais (WhatsApp) Cliente fornecerá dados para ser registrado na base de dados do sistema, ao enviar e-mail? (LGPD) --------------------------------------

DIVULGAÇÃO DO PORTAL: - Tráfego Pago? --------------------------------------

REFERÊNCIAS:

\- CENTRAL DO CORRETOR

\-------------------------------------


REQUISITOS TÉCNICOS:

SISTEMA WEB MONOLITO (LARAVEL, NEXT, DJANGO, ETC) BANCO DE DADOS - POSTGRESQL DOCKER HOSPEDAGEM DOMÍNIO (JÁ TEMOS?) PARA ACESSO ESTRANGEIRO, SER .COM? QUAL A MENOR LATÊNCIA PARA

UTILIZAÇÃO DO MESMO?

\# Plano do Projeto PIB - Portal Invest Bahia

\## Resumo

Transformar o protótipo atual em um **monolito web em Next.js**, com área pública multilíngue e painel administrativo para classificados do portal. A primeira versão deve priorizar: login, gestão de páginas/classificados, imagens, contatos diretos com corretores, carrossel simples de eventos e PDF interno para o administrador.

A base atual em TanStack/HTML estático deve ser tratada como referência visual e de

conteúdo, não como arquitetura final.

\## Features Principais

\- **Área pública** - Páginas: Início, Fazenda, Ativo, Serviço, Informações e Contatos. - Idiomas: português, inglês, espanhol e italiano. - Conteúdo dos anúncios cadastrado manualmente em cada idioma. - Cards de divulgação com título, subtítulo, descrição, imagem principal e dados de contato do corretor. - Página de detalhe do classificado com galeria/carrossel de imagens. - Botões de contato direto por e-mail e WhatsApp, sem salvar dados do visitante na primeira versão.

\- **Painel administrativo** - Login obrigatório. - 1 administrador geral com acesso total. - Até 5 usuários corretores. - Cada corretor cria, edita e exclui apenas seus próprios classificados. - Administrador pode visualizar, editar, publicar, ocultar e excluir qualquer item. - Gestão de imagens dos classificados. - Gestão simples de eventos: título, subtítulo, descrição e imagem. - Eventos exibidos em carrossel público, com modal de detalhes ao clicar.

- \- **PDF**


\- Geração de PDF apenas no painel administrativo. - PDF deve conter informações completas do ativo/classificado, imagens principais e contato do corretor. - Não haverá download público do PDF na primeira versão.

\## Estrutura Técnica

\- **Monolito** - Migrar para **Next.js full-stack** com TypeScript. - Usar rotas públicas e rotas protegidas no mesmo projeto. - Manter React e componentes visuais como base, mas substituir os iframes/HTML estático por páginas reais.

\- **Banco e dados** - PostgreSQL. - Prisma como camada de acesso ao banco e migrations. - Entidades mínimas: - `User`: administrador e corretores. - `Listing`: classificado. - `ListingTranslation`: textos por idioma. - `ListingImage`: imagens do classificado. - `PageContent`: conteúdo das páginas Fazenda, Ativo, Serviço, Informações e

Contatos.

\- `Event`: itens do carrossel/modal. - As abas Fazenda, Ativo e Serviço serão páginas separadas, com conteúdo próprio e

classificados associados a cada página.

\- **Autenticação e permissões** - Auth.js com login por e-mail e senha. - Senhas criptografadas. - Perfis: `admin` e `broker`. - Middleware para proteger `/admin`. - Regras: - Admin acessa tudo. - Corretor acessa apenas seus próprios classificados.

\- **Arquivos e imagens** - Upload de imagens no painel. - Armazenamento inicial em volume persistente no servidor. - Padronizar imagem principal e galeria. - Preparar a estrutura para futura migração para storage externo, se o portal crescer.

\- **Infraestrutura** - Docker com serviços para app Next.js e PostgreSQL. - Hospedagem em VPS. - Deploy em domínio **`.com.br`**. - Servidor preferencialmente no Brasil, alinhado ao domínio e ao público principal.


\- Backups automáticos do banco e pasta de imagens.

\## Testes e Aceitação

\- Login:

- \- Admin entra no painel e vê todos os classificados.

- \- Corretor entra no painel e vê apenas seus próprios classificados.

- \- Usuário não autenticado não acessa `/admin`.

\- Classificados:

- \- Criar, editar, ocultar e excluir classificado.

- \- Cadastrar textos nos quatro idiomas.

- \- Adicionar imagem principal e galeria.

- \- Validar exibição correta nas páginas Fazenda, Ativo e Serviço.

\- Contato:

- \- Botão de e-mail abre contato com o corretor correto.

- \- Botão de WhatsApp abre conversa com número correto.

- \- Nenhum dado do visitante é salvo nessa etapa.

\- Eventos:

- \- Admin cria evento com imagem e texto.

- \- Evento aparece no carrossel público.

- \- Clique abre modal com detalhes.

\- PDF:

- \- Admin gera PDF de um classificado.

- \- PDF contém título, descrição, imagens e contato.

- \- PDF não aparece como download público.

\- Infra:

- \- Projeto sobe via Docker.

- \- Banco persiste após reiniciar containers.

- \- Uploads persistem após reiniciar aplicação.

- \- Build de produção executa sem erros.


\## Assumptions

\- A primeira entrega será um produto funcional enxuto, não o portal completo definitivo. - O nome comercial será **PIB - Portal Invest Bahia**. - O deploy oficial será em domínio **`.com.br`**. - A paleta futura seguirá marrom, dourado e amarelo, inspirada em cacau e ouro. - A logo nova será aplicada em uma etapa posterior. - O cliente não precisa guardar leads na primeira versão.

\- O painel deve ser simples o suficiente para usuários não técnicos operarem sozinhos.

TIPO DE NEGÓCIO: Portal/Classificado - CORRETORES ----------------------------------

REQUISITOS BÁSICOS: 1 + 5 usuários, sendo um estrangeiro(italiano), fazendo divulgação de ativos no site -> 1 Administrador Geral Login - Auth

Línguas - Port, Eng, Esp, Ita

LOGO - PIB (Portal Invest Bahia)

Paleta - Marrom - Dourado - Amarelo (cacau, ouro)

Postagens Geral

Abas: Fazenda - Ativo - Serviço - Informações - Contatos

\-------------------------------------

PONTOS FUNCIONAIS:

Usuário poderá fazer login no sistema e poderá criar/editar/excluir (por ID/Sessão) um classificado com Título, subtítulo, descrição, imagem e informações do contato do corretor Usuário poderá ser contatado por e-mail e redes sociais, caso o cliente se interesse pelo anúncio. Usuário poderá adicionar novas imagens em um Carrossel de informações de eventos

(título, subtítulo, descrição, imagem) sobre o nicho do negócio -> Utilizar Modal ao clicar?

Cliente poderá exportar um PDF com todas as informações adicionais do ativo


CLiente poderá enviar um e-mail para o corretor Cliente poderá entrar em contato via redes sociais (WhatsApp) Cliente fornecerá dados para ser registrado na base de dados do sistema, ao enviar

e-mail? (LGPD) --------------------------------------

DIVULGAÇÃO DO PORTAL: - Tráfego Pago? --------------------------------------

REFERÊNCIAS:

\- CENTRAL DO CORRETOR -------------------------------------

REQUISITOS TÉCNICOS:

SISTEMA WEB MONOLITO (LARAVEL, NEXT, DJANGO, ETC) BANCO DE DADOS - POSTGRESQL DOCKER HOSPEDAGEM DOMÍNIO (JÁ TEMOS?) PARA ACESSO ESTRANGEIRO, SER .COM? QUAL A MENOR LATÊNCIA PARA

UTILIZAÇÃO DO MESMO?
