# 🔐 CONFIGURAÇÃO DE PERMISSÕES - HOSTGATOR

## 📋 PERMISSÕES RECOMENDADAS

### **Permissões Padrão**
- **Pastas:** `755` (rwxr-xr-x)
- **Arquivos:** `644` (rw-r--r--)

### **Permissões Específicas**

| Arquivo/Pasta | Permissão | Descrição |
|---------------|-----------|------------|
| `.env` | `600` | Arquivo de configuração sensível |
| `uploads/` | `755` | Pasta de uploads (escrita necessária) |
| `backups/` | `755` | Pasta de backups (escrita necessária) |
| `error_log` | `666` | Arquivo de log (escrita necessária) |
| `api/error_log` | `666` | Log da API (escrita necessária) |
| `.htaccess` | `644` | Configurações do Apache |
| `*.php` | `644` | Arquivos PHP (sem execução) |
| `*.sh` | `755` | Scripts shell (executáveis) |

---

## 🛠️ COMO CONFIGURAR NO CPANEL

### **Método 1: Gerenciador de Arquivos**

1. **Acesse o cPanel** da sua conta HostGator
2. Clique em **"Gerenciador de Arquivos"**
3. Navegue até `public_html/`
4. **Para cada arquivo/pasta:**
   - Clique com botão direito
   - Selecione **"Permissões"**
   - Configure conforme tabela acima
   - Marque **"Aplicar recursivamente"** para pastas

### **Método 2: Terminal (se disponível)**

```bash
# Navegue até public_html
cd public_html

# Execute o script de permissões
bash configurar_permissoes.sh
```

### **Método 3: Configuração Manual**

#### **Configurar Pastas (755)**
```bash
# Principais pastas do sistema
chmod 755 .
chmod 755 api
chmod 755 assets
chmod 755 helpers
chmod 755 uploads
chmod 755 backups
chmod 755 _partials
chmod 755 assets/css
chmod 755 assets/js
chmod 755 assets/img
```

#### **Configurar Arquivos PHP (644)**
```bash
# Arquivos principais
chmod 644 index.php
chmod 644 login.php
chmod 644 dashboard.php
chmod 644 config.php
chmod 644 email_config.php

# Arquivos da API
chmod 644 api/*.php

# Arquivos helpers
chmod 644 helpers/*.php
```

#### **Configurar Arquivos Sensíveis**
```bash
# Arquivo de configuração
chmod 600 .env

# Arquivos de log
chmod 666 error_log
chmod 666 api/error_log

# Configurações do servidor
chmod 644 .htaccess
```

---

## 🔍 VERIFICAÇÃO DE PERMISSÕES

### **Comandos para Verificar**
```bash
# Listar permissões detalhadas
ls -la

# Verificar permissões de pasta específica
ls -la uploads/

# Verificar arquivo .env
ls -la .env
```

### **Saída Esperada**
```
drwxr-xr-x  2 user user  4096 Sep  4 16:00 api/
drwxr-xr-x  2 user user  4096 Sep  4 16:00 assets/
-rw-------  1 user user  1234 Sep  4 16:00 .env
-rw-r--r--  1 user user   567 Sep  4 16:00 .htaccess
-rw-r--r--  1 user user  2345 Sep  4 16:00 index.php
drwxr-xr-x  2 user user  4096 Sep  4 16:00 uploads/
```

---

## ⚠️ PROBLEMAS COMUNS

### **Erro 500 - Internal Server Error**
**Causa:** Permissões incorretas
**Solução:**
```bash
# Resetar permissões
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 600 .env
```

### **Erro de Upload de Arquivos**
**Causa:** Pasta uploads sem permissão de escrita
**Solução:**
```bash
chmod 755 uploads/
chmod 755 uploads/*/
```

### **Erro de Log**
**Causa:** Arquivo de log sem permissão de escrita
**Solução:**
```bash
chmod 666 error_log
chmod 666 api/error_log
```

### **Erro de Configuração**
**Causa:** Arquivo .env inacessível
**Solução:**
```bash
chmod 600 .env
# Verificar se o proprietário está correto
```

---

## 🛡️ SEGURANÇA

### **Boas Práticas**

1. **Nunca use 777:** Evite permissões 777 (muito permissivas)
2. **Proteja o .env:** Sempre use 600 para arquivos de configuração
3. **Monitore logs:** Verifique regularmente por tentativas de acesso
4. **Backup regular:** Mantenha backups das configurações

### **Arquivos a Proteger**
```bash
# Arquivos que NUNCA devem ter permissão 777
.env
config.php
email_config.php
helpers/*.php
```

### **Verificação de Segurança**
```bash
# Procurar arquivos com permissões perigosas
find . -type f -perm 777
find . -type f -perm 666 ! -name "error_log"
```

---

## 📞 SUPORTE

### **Se Precisar de Ajuda**

1. **HostGator Support:** Chat 24/7 disponível
2. **Documentação:** https://www.hostgator.com.br/ajuda
3. **Logs de Erro:** Sempre consulte os logs primeiro

### **Informações para o Suporte**
- Mensagem de erro exata
- Arquivo/pasta com problema
- Permissões atuais (`ls -la`)
- Ações realizadas antes do erro

---

## ✅ CHECKLIST DE PERMISSÕES

- [ ] Pastas principais: 755
- [ ] Arquivos PHP: 644
- [ ] Arquivo .env: 600
- [ ] Pasta uploads: 755
- [ ] Arquivos de log: 666
- [ ] .htaccess: 644
- [ ] Teste de funcionamento realizado
- [ ] Logs verificados por erros
- [ ] Backup das configurações feito

---

**🎉 Permissões configuradas com sucesso!**

> ⚠️ **Lembre-se:** Sempre teste o funcionamento do sistema após alterar permissões!