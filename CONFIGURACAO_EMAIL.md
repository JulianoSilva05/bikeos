# Configuração de Email - BikeOS

## Problema Identificado

O sistema não está enviando emails após o cadastro porque o **sendmail do XAMPP não está configurado corretamente**.

## Solução: Configurar o Sendmail do XAMPP

### Passo 1: Configurar o arquivo sendmail.ini

1. Navegue até a pasta: `C:\xampp\sendmail\`
2. Abra o arquivo `sendmail.ini` em um editor de texto
3. Localize e configure as seguintes linhas:

```ini
[sendmail]

; Configurações SMTP - Titan Email
smtp_server=smtp.titan.email
smtp_port=465
smtp_ssl=ssl

; Autenticação
auth_username=contato@bikeos.com.br
auth_password=Jjmgmz56@

; Email padrão
default_domain=bikeos.com.br
force_sender=contato@bikeos.com.br

; Configurações adicionais
error_logfile=error.log
debug_logfile=debug.log
```

### Passo 2: Configuração do Titan Email

**IMPORTANTE:** Para o Titan Email, use a senha normal da conta (não é necessário senha de app):

1. Email: contato@bikeos.com.br
2. Senha: Jjmgmz56@
3. Servidor SMTP: smtp.titan.email
4. Porta: 465
5. Criptografia: SSL/TLS
6. Servidor IMAP: imap.titan.email
7. Porta IMAP: 993

### Passo 3: Configurar o php.ini

1. Navegue até: `C:\xampp\php\`
2. Abra o arquivo `php.ini`
3. Localize e configure:

```ini
[mail function]
; Para Win32 apenas.
SMTP = smtp.titan.email
smtp_port = 465

; Para Win32 apenas.
sendmail_from = contato@bikeos.com.br

; Para Unix apenas. Você pode fornecer argumentos também (padrão: "sendmail -t -i").
sendmail_path = "C:\xampp\sendmail\sendmail.exe" -t
```

### Passo 4: Reiniciar o Apache

1. Abra o XAMPP Control Panel
2. Pare o Apache (Stop)
3. Inicie o Apache novamente (Start)

### Passo 5: Testar a Configuração

Após configurar, acesse: `http://localhost:8000/test_email_novo.php`

Se tudo estiver correto, você verá:
- ✓ Email de boas-vindas enviado com sucesso!
- ✓ Email de notificação admin enviado com sucesso!

## Alternativa: Usar um Serviço de Email Externo

Se você preferir não usar o Gmail, pode configurar outros provedores:

### Para Outlook/Hotmail:
```ini
smtp_server=smtp-mail.outlook.com
smtp_port=587
smtp_ssl=tls
auth_username=seu_email@outlook.com
auth_password=sua_senha
```

### Para outros provedores:
Consulte a documentação do seu provedor de email para as configurações SMTP.

## Verificação Final

Após a configuração:

1. Teste o cadastro de um novo cliente
2. Verifique se o email de boas-vindas chegou na caixa de entrada
3. Verifique se o email de notificação chegou para o administrador

## Troubleshooting

### Se ainda não funcionar:

1. **Verifique os logs:**
   - `C:\xampp\sendmail\error.log`
   - `C:\xampp\sendmail\debug.log`

2. **Verifique se o Gmail permite "Apps menos seguros":**
   - Acesse: https://myaccount.google.com/lesssecureapps
   - Ative "Permitir apps menos seguros" (se disponível)

3. **Teste com outro email:**
   - Tente usar um email diferente para teste

4. **Verifique firewall/antivírus:**
   - Certifique-se de que não estão bloqueando a conexão SMTP

## Contato

Se precisar de ajuda adicional, entre em contato com o suporte técnico.

---

**Nota:** Após configurar corretamente, todos os emails do sistema (cadastro, recuperação de senha, notificações) funcionarão automaticamente.