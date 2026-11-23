# BikeOS — instruções para o Claude

Responda sempre em português do Brasil.

## Subir arquivos para a HostGator

Sempre que o usuário pedir para **subir algo para a HostGator**, o envio é **por FTP**, feito pelo
workflow do GitHub Actions `.github/workflows/deploy-hostgator.yml` ("Subir para HostGator (FTP)").

- Servidor FTP: `br1048.hostgator.com.br`, porta 21. Usuário, senha e servidor ficam **só** nos
  segredos do GitHub (`FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`). Nunca escreva a senha em
  arquivos, commits, mensagens ou logs, e nunca peça para o usuário colar a senha no chat.
- Como subir:
  1. Faça commit e push das alterações e garanta que estão no branch que será enviado
     (normalmente `main`, depois do merge).
  2. Dispare o workflow `deploy-hostgator.yml` com `arquivos` = lista dos arquivos separados por
     espaço (ex.: `index.php promocao.php`) e `ref` = branch/commit.
  3. Acompanhe a execução e confirme o resultado. O backup da versão anterior de cada arquivo fica
     no artefato `backup-servidor-<número>` da execução.
  4. Confira o site em https://bikeos.com.br depois do envio.
- Envie só os arquivos que mudaram. Se um arquivo depende de outro (ex.: `index.php` inclui
  `promocao.php`), envie os dois, a dependência primeiro.
- Nunca envie `.env`, `.env.*`, `config.local.php` nem arquivos `.sql`.
- O ambiente na nuvem do Claude não consegue usar FTP diretamente (o proxy só permite HTTPS);
  por isso o envio é sempre pelo GitHub Actions.
- Não enviar para o servidor a assinatura do PR #1 (`assinar.php`, `api_assinatura.php`,
  `mp_assinatura.php`): ela duplica a cobrança SaaS que já existe (tabelas `saas_*`).
