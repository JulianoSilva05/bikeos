# Prompt para continuar no Claude Code do PC

Cole o texto abaixo no Claude Code (app desktop, aba Code) aberto na pasta
`G:\Meu Drive\Sistemas\Programas\BikeOs`.

---

Estou continuando um trabalho no projeto **BikeOS** (SaaS de gestão para oficinas de bicicletas, PHP puro sem framework/Composer, hospedado na HostGator, banco MySQL). A pasta do projeto é `G:\Meu Drive\Sistemas\Programas\BikeOs` e o repositório é `JulianoSilva05/bikeos` (branch de trabalho `claude/focused-archimedes-fwhq2x`, Pull Request #1 aberto para `main`). Responda sempre em português do Brasil.

## Regras de segurança
- Nunca mostre, copie para o chat ou envie para o GitHub senhas, o Access Token do Mercado Pago ou o conteúdo do `.env`.
- O Access Token do Mercado Pago deve ficar só no `.env` local/servidor como `MP_ACCESS_TOKEN`. Leia-o de lá; não peça para eu colar no chat.
- Só faça **consultas de leitura** no banco de produção e no Mercado Pago. Não altere, não cancele e não crie cobranças sem me perguntar antes.

## Tarefa principal agora
Descobrir **quem usou o sistema nos últimos 7 dias** e **comparar com quem pagou no Mercado Pago**.

1. **Uso do sistema (banco online `jul33718_bikeos` na HostGator).** A tabela `saas_acesso_log` está vazia (o log de acesso nunca gravou), então use atividade como sinal de uso. Rode esta consulta:

```sql
SELECT
  e.id, e.nome_empresa, e.email, e.plano, e.status,
  e.saas_pago, e.saas_bloqueado, e.trial_fim, e.data_expiracao,
  u.registros_7d, u.ultima_atividade, u.onde_usou,
  (SELECT MAX(p.data_aprovacao) FROM saas_pagamentos p
     WHERE p.empresa_id = e.id
       AND LOWER(p.status) IN ('approved','aprovado','pago','paid')) AS ultimo_pagamento_mp,
  (SELECT CONCAT(m.status, ' | venc. ', m.vencimento, ' | R$ ', m.valor)
     FROM saas_mensalidades m WHERE m.empresa_id = e.id
     ORDER BY m.vencimento DESC LIMIT 1) AS ultima_mensalidade,
  CASE
    WHEN e.trial_fim >= CURDATE() THEN 'EM TESTE GRATIS'
    WHEN EXISTS (SELECT 1 FROM saas_pagamentos p
                 WHERE p.empresa_id = e.id
                   AND LOWER(p.status) IN ('approved','aprovado','pago','paid')
                   AND p.data_aprovacao >= NOW() - INTERVAL 35 DAY) THEN 'PAGOU (Mercado Pago)'
    WHEN EXISTS (SELECT 1 FROM saas_mensalidades m
                 WHERE m.empresa_id = e.id AND m.pago_em IS NOT NULL
                   AND CURDATE() BETWEEN m.competencia_inicio AND m.competencia_fim) THEN 'PAGOU (mensalidade do mês)'
    ELSE 'SEM PAGAMENTO REGISTRADO'
  END AS situacao
FROM (
  SELECT empresa_id, COUNT(*) AS registros_7d, MAX(quando) AS ultima_atividade,
         GROUP_CONCAT(DISTINCT origem ORDER BY origem) AS onde_usou
  FROM (
    SELECT empresa_id, data_venda AS quando, 'vendas' AS origem FROM vendas
    UNION ALL SELECT empresa_id, data_entrada,      'ordens_servico' FROM ordens_servico
    UNION ALL SELECT empresa_id, data_finalizacao,  'ordens_servico' FROM ordens_servico
    UNION ALL SELECT empresa_id, data_criacao,      'observacoes_os' FROM os_observacoes
    UNION ALL SELECT empresa_id, criado_em,         'orcamentos'     FROM orcamentos
    UNION ALL SELECT empresa_id, data_cadastro,     'clientes'       FROM clientes
    UNION ALL SELECT empresa_id, data_movimentacao, 'estoque'        FROM movimentacoes_estoque
    UNION ALL SELECT empresa_id, data_criacao,      'financeiro'     FROM movimentacoes_financeiras
    UNION ALL SELECT empresa_id, data_criacao,      'contas_pagar'   FROM contas_pagar
    UNION ALL SELECT empresa_id, created_at,        'contas_receber' FROM contas_receber
  ) atividades
  WHERE quando >= NOW() - INTERVAL 7 DAY AND quando <= NOW()
  GROUP BY empresa_id
) u
JOIN empresas e ON e.id = u.empresa_id
ORDER BY (situacao = 'SEM PAGAMENTO REGISTRADO') DESC, u.ultima_atividade DESC;
```

   Antes, confira os valores reais de status com:
   `SELECT 'pagamentos', status, COUNT(*) FROM saas_pagamentos GROUP BY status UNION ALL SELECT 'mensalidades', status, COUNT(*) FROM saas_mensalidades GROUP BY status;`
   e ajuste a lista de status "pago" se precisar.

2. **Pagamentos no Mercado Pago.** Usando o `MP_ACCESS_TOKEN` do `.env`, faça só leitura em `GET https://api.mercadopago.com/v1/payments/search` (pagamentos aprovados dos últimos 35 dias; pagine com `offset`/`limit`). Para cada pagamento pegue: id, data de aprovação, valor, status, `external_reference`, e-mail e nome do pagador.

3. **Cruzar e me entregar 3 listas:**
   - Usou o sistema e **pagou**.
   - Usou o sistema e **não pagou** (excluindo quem está em teste grátis).
   - **Pagamentos no Mercado Pago sem empresa correspondente** no banco (ex.: pagos por link manual).
   Cruze primeiro pelo `mercado_pago_payment_id` / `external_reference` de `saas_pagamentos`; depois pelo e-mail da empresa (`empresas.email`). Marque os casos em que o e-mail não bate para eu conferir à mão.
   Salve o resultado num CSV fora do repositório (não faça commit) e me mostre um resumo.

## Contexto que já sabemos do banco real (`jul33718_bikeos`)
- `usuarios`: id, nome, email, senha, tipo enum('ADMIN','FUNCIONARIO'), status enum('ATIVO','INATIVO'), data_criacao, data_atualizacao. (O `deploy_hostgator.sql` do repositório está desatualizado: não tem essas colunas.)
- `empresas`: id, nome_empresa, email, status, saas_bloqueado, saas_pago, plano enum('GRATUITO','BASICO','PREMIUM'), data_expiracao, dia_vencimento_cobranca, link_pagamento_manual, trial_fim, entre outras.
- Cobrança SaaS já existente, integrada ao Mercado Pago: `saas_pagamentos` (mercado_pago_payment_id, external_reference, valor, status, plano, periodo, data_aprovacao), `saas_mensalidades` (competencia_inicio/fim, vencimento, valor, status, pago_em, mp_payment_id, origem), `saas_checkouts` (empresa_id, external_reference, valor). `saas_acesso_log` existe mas está vazia.

## Pendências do projeto (depois da tarefa principal)
1. **Investigar o código da cobrança SaaS existente** (arquivos PHP que usam as tabelas `saas_*`; eles estão em subpastas que nunca foram para o GitHub, assim como `helpers/`). Envie essas pastas ao GitHub, sem `.env`.
2. **Assinatura recorrente que foi criada no PR #1** (`assinar.php`, `api_assinatura.php`, `mp_assinatura.php`, `assinaturas_mp.sql`): ela usa uma tabela própria ligada ao usuário e **duplica** a cobrança SaaS existente. **Não publicar na HostGator.** Avaliar se vale adaptar a assinatura (API `/preapproval`) ao sistema `saas_*` existente, ligada a `empresas`.
3. **Promoção Black Friday** (`promocao.php` + `index.php`, já no PR #1): aparece sozinha de 26/11/2026 00h00 a 27/11/2026 23h59 (horário de Brasília). Falta colocar o link de pagamento de R$ 34,00 na constante `PROMO_LINK_MERCADO_PAGO` e publicar `promocao.php` e `index.php` na HostGator antes do dia 26. Verificar se o campo `empresas.link_pagamento_manual` deve ser usado.
4. **Log de acesso:** descobrir por que `saas_acesso_log` nunca grava (provavelmente no fluxo de login) e corrigir.
5. **Segurança:** o `.env` e o `.env.production` (com senhas do banco e do e-mail) e o `deploy_hostgator.sql` (com a senha do banco no topo) estão públicos no GitHub. Rodar `git rm --cached .env .env.production`, tirar a senha do `.sql`, fazer commit e push, e me lembrar de trocar as senhas no cPanel. Os dois arquivos já estão no `.gitignore` do branch.
6. **Ambiente local:** o arquivo `config.local.php` (modelo em `config.local.example.php`) faz o sistema usar o MySQL local; rodar com `C:\xampp\php\php.exe -S localhost:8000` na pasta do projeto.
