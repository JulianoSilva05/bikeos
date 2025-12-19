# 🚀 GUIA DE DEPLOY - BIKEOS NO HOSTGATOR

## 📋 INFORMAÇÕES DO SERVIDOR

- **Banco de Dados:** `jul33718_bikeos`
- **Usuário DB:** `jul33718_juliano`
- **Senha DB:** `Jjmgmz56@`
- **Host DB:** `localhost`

---

## 🔧 PASSO A PASSO PARA DEPLOY

### **ETAPA 1: PREPARAÇÃO DOS ARQUIVOS**

#### 1.1 Remover Arquivos de Desenvolvimento
```bash
# Consulte o arquivo: arquivos_remover_producao.txt
# Remova todos os arquivos listados antes do upload
```

#### 1.2 Configurar Ambiente de Produção
1. **Renomeie** `.env.production` para `.env`
2. **Edite** o arquivo `.env` e configure:
   ```env
   # Atualize estas configurações:
   EMAIL_PASSWORD=sua_senha_titan_email_real
   SECRET_KEY=gere_uma_chave_secreta_unica_32_chars
   BACKUP_ENCRYPTION_KEY=gere_uma_chave_backup_32_chars
   ```

#### 1.3 Limpar Logs de Erro
```bash
# Limpe o conteúdo dos arquivos de log:
# - error_log
# - api/error_log
```

---

### **ETAPA 2: CONFIGURAÇÃO DO BANCO DE DADOS**

#### 2.1 Acessar cPanel do HostGator
1. Faça login no **cPanel** da sua conta HostGator
2. Vá em **"Bancos de Dados MySQL"**

#### 2.2 Verificar/Criar Banco de Dados
1. Verifique se o banco `jul33718_bikeos` já existe
2. Se não existir, crie um novo banco com este nome
3. Certifique-se de que o usuário `jul33718_juliano` tem **TODOS OS PRIVILÉGIOS**

#### 2.3 Importar Estrutura do Banco
1. Vá em **"phpMyAdmin"** no cPanel
2. Selecione o banco `jul33718_bikeos`
3. Clique na aba **"Importar"**
4. Faça upload do arquivo `deploy_hostgator.sql`
5. Clique em **"Executar"**

---

### **ETAPA 3: UPLOAD DOS ARQUIVOS**

#### 3.1 Preparar Estrutura de Pastas
```
public_html/
├── api/
├── assets/
├── helpers/
├── uploads/
├── _partials/
├── .env
├── .htaccess
├── index.php
├── login.php
├── dashboard.php
└── [demais arquivos PHP]
```

#### 3.2 Upload via FTP/Gerenciador de Arquivos
1. **Opção A - FTP:**
   - Use um cliente FTP (FileZilla, WinSCP)
   - Conecte com as credenciais do HostGator
   - Faça upload para a pasta `public_html/`

2. **Opção B - Gerenciador de Arquivos:**
   - Acesse **"Gerenciador de Arquivos"** no cPanel
   - Navegue até `public_html/`
   - Faça upload dos arquivos

#### 3.3 Configurar Permissões
```bash
# Permissões recomendadas:
Pastas: 755
Arquivos PHP: 644
Arquivo .env: 600
Pasta uploads/: 755
Subpastas uploads/: 755
```

---

### **ETAPA 4: CONFIGURAÇÕES FINAIS**

#### 4.1 Verificar .htaccess
Certifique-se de que o arquivo `.htaccess` está presente com:
```apache
# Segurança
Options -Indexes

# Proteção do arquivo .env
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

# Configurações PHP
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
php_value memory_limit 256M

# Redirecionamento HTTPS (se aplicável)
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

#### 4.2 Configurar Domínio
1. No cPanel, vá em **"Domínios"**
2. Configure o domínio `bikeos.com.br` para apontar para `public_html/`
3. Se necessário, configure subdomínio

#### 4.3 Configurar SSL (Recomendado)
1. No cPanel, vá em **"SSL/TLS"**
2. Ative o **"Let's Encrypt"** gratuito
3. Force redirecionamento HTTPS

---

### **ETAPA 5: TESTES E VERIFICAÇÕES**

#### 5.1 Teste de Conexão com Banco
1. Acesse: `https://bikeos.com.br/`
2. Verifique se a página carrega sem erros
3. Teste o login com:
   - **Email:** `admin@bikeos.com.br`
   - **Senha:** `password` (altere após primeiro login)

#### 5.2 Teste de Funcionalidades
- [ ] Login/Logout
- [ ] Dashboard
- [ ] Cadastro de clientes
- [ ] Cadastro de produtos
- [ ] Criação de OS
- [ ] Envio de emails

#### 5.3 Verificar Logs
1. Monitore os arquivos de log por erros
2. Verifique o log de erros do cPanel

---

### **ETAPA 6: CONFIGURAÇÕES DE SEGURANÇA**

#### 6.1 Alterar Senhas Padrão
1. **Login do sistema:** Altere a senha do admin
2. **Banco de dados:** Considere alterar a senha do usuário DB
3. **Email:** Configure a senha real do Titan Email

#### 6.2 Configurar Backup
1. No cPanel, configure **"Backup"** automático
2. Defina frequência (diário/semanal)
3. Configure notificações por email

#### 6.3 Monitoramento
1. Configure alertas de erro
2. Monitore uso de recursos
3. Verifique logs regularmente

---

## 🔍 SOLUÇÃO DE PROBLEMAS COMUNS

### **Erro de Conexão com Banco**
```php
// Verifique as configurações no .env:
DB_HOST=localhost
DB_NAME=jul33718_bikeos
DB_USER=jul33718_juliano
DB_PASS=Jjmgmz56@
```

### **Erro 500 - Internal Server Error**
1. Verifique permissões dos arquivos
2. Consulte o log de erros do cPanel
3. Verifique sintaxe do .htaccess

### **Problemas com Upload**
1. Verifique permissões da pasta `uploads/`
2. Confirme configurações PHP no .htaccess
3. Verifique limites de upload no cPanel

### **Emails não Enviando**
1. Verifique configurações SMTP no .env
2. Teste com outro provedor de email
3. Consulte logs de email do HostGator

---

## 📞 SUPORTE

### **Contatos HostGator**
- **Suporte Técnico:** Disponível 24/7 via chat/telefone
- **Documentação:** https://www.hostgator.com.br/ajuda

### **Logs Importantes**
- **cPanel > Logs de Erro:** Para erros do servidor
- **phpMyAdmin > SQL:** Para erros de banco
- **Arquivo error_log:** Para erros da aplicação

---

## ✅ CHECKLIST FINAL

- [ ] Banco de dados criado e importado
- [ ] Arquivos enviados via FTP
- [ ] Permissões configuradas
- [ ] .env configurado corretamente
- [ ] SSL ativado
- [ ] Domínio configurado
- [ ] Login testado
- [ ] Funcionalidades principais testadas
- [ ] Backup configurado
- [ ] Senhas alteradas
- [ ] Monitoramento ativo

---

## 🎉 DEPLOY CONCLUÍDO!

Seu sistema BikeOS está agora rodando em produção no HostGator!

**URL de Acesso:** https://bikeos.com.br
**Login Inicial:** admin@bikeos.com.br / password

> ⚠️ **IMPORTANTE:** Altere a senha padrão imediatamente após o primeiro login!