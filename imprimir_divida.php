<?php
require_once 'api/db_connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$venda_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($venda_id <= 0) {
    die("ID da venda inválido.");
}

// Busca configurações da loja baseado na empresa do usuário logado
$empresa_id = $_SESSION['empresa_id'] ?? 1;
$stmt_config = $pdo->prepare("SELECT * FROM configuracoes_loja WHERE empresa_id = ? ORDER BY id DESC LIMIT 1");
$stmt_config->execute([$empresa_id]);
$config_loja = $stmt_config->fetch(PDO::FETCH_ASSOC);

// Busca a cidade da configuração da loja
$cidade_empresa = $config_loja['cidade'] ?? 'São João del Rei';

// Se não encontrar configurações para a empresa específica, busca qualquer configuração disponível
if (!$config_loja) {
    $stmt_config = $pdo->prepare("SELECT * FROM configuracoes_loja ORDER BY id DESC LIMIT 1");
    $stmt_config->execute();
    $config_loja = $stmt_config->fetch(PDO::FETCH_ASSOC);
}

// Define valores padrão caso não haja configurações
if (!$config_loja) {
    $config_loja = [
        'nome_loja' => 'BikeOS',
        'endereco' => 'R. Américo Deodoro Brighenti, 322\nJardim Aeroporto, São João del Rei/MG',
        'telefone' => '(32) 99962-8568',
    
    ];
}

// --- DADOS DA SUA EMPRESA ---
// Buscar nome da credora das configurações da loja
$stmt_credora = $pdo->prepare("SELECT razao_social, cnpj FROM configuracoes_loja WHERE empresa_id = ? LIMIT 1");
$stmt_credora->execute([$empresa_id]);
$config_credora = $stmt_credora->fetch(PDO::FETCH_ASSOC);

// Se não encontrar, busca qualquer configuração disponível
if (!$config_credora) {
    $stmt_credora = $pdo->prepare("SELECT razao_social, cnpj FROM configuracoes_loja ORDER BY id DESC LIMIT 1");
    $stmt_credora->execute();
    $config_credora = $stmt_credora->fetch(PDO::FETCH_ASSOC);
}

$empresa_nome = $config_credora['razao_social'] ?? $config_loja['nome_loja'] ?? "BikeOS";
$empresa_cnpj = $config_credora['cnpj'] ?? "37.817.450/0001-81";

$stmt = $pdo->prepare("
    SELECT 
        v.id as venda_id, v.valor_total, v.data_venda, v.os_id,
        c.nome as cliente_nome, c.cpf as cliente_cpf, c.telefone as cliente_telefone,
        c.cep as cliente_cep, c.logradouro as cliente_logradouro, c.numero as cliente_numero,
        c.complemento as cliente_complemento, c.bairro as cliente_bairro, 
        c.cidade as cliente_cidade, c.estado as cliente_estado
    FROM vendas v
    JOIN clientes c ON v.cliente_id = c.id
    WHERE v.id = ?
");
$stmt->execute([$venda_id]);
$venda = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venda) {
    die("Venda não encontrada.");
}

$os_id = $venda['os_id'];
$produtos = [];
$servicos = [];

if ($os_id) {
    $stmt_produtos = $pdo->prepare("SELECT op.quantidade, op.preco_unitario_cobrado, p.nome as produto_nome FROM os_produtos op JOIN produtos p ON op.produto_id = p.id WHERE op.os_id = ?");
    $stmt_produtos->execute([$os_id]);
    $produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

    $stmt_servicos = $pdo->prepare("SELECT os_s.preco_cobrado, s.nome as servico_nome FROM os_servicos os_s JOIN servicos s ON os_s.servico_id = s.id WHERE os_s.os_id = ?");
    $stmt_servicos->execute([$os_id]);
    $servicos = $stmt_servicos->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt_produtos = $pdo->prepare("SELECT vi.quantidade, vi.preco_unitario_cobrado, p.nome as produto_nome FROM venda_itens vi JOIN produtos p ON vi.produto_id = p.id WHERE vi.venda_id = ?");
    $stmt_produtos->execute([$venda_id]);
    $produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);
}

$data_venda_obj = new DateTime($venda['data_venda']);
$data_vencimento = $data_venda_obj->add(new DateInterval('P30D'))->format('d/m/Y');

function valorPorExtenso($valor) {
    return "R$ " . number_format($valor, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Termo de Dívida - Venda #<?php echo $venda['venda_id']; ?></title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Consolas', 'Lucida Console', 'Courier New', Courier, monospace; 
            font-size: 12px; 
            line-height: 1.4;
            font-weight: bold;
        }
        body { 
            width: 76mm; 
            margin: 0 auto;
            /* ALTERAÇÃO: Adiciona uma margem interna para evitar cortes */
            padding: 0 4mm;
        }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { font-size: 15px; text-transform: uppercase; }
        .content { text-align: justify; }
        .content p { margin-bottom: 10px; }
        .section { margin-top: 15px; padding-top: 10px; border-top: 1px dashed #000; }
        h2 { font-size: 13px; text-align: center; margin-bottom: 10px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { text-align: left; padding: 1px 0; }
        .text-right { text-align: right; }
        .total-geral { font-size: 14px; padding-top: 5px; margin-top: 5px; border-top: 1px dashed #000; }
        .assinatura { margin-top: 30px; text-align: center; }
        .assinatura p { margin-top: 2px; }
        .dados-parte { margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #000; }
        .dados-parte:first-child { border-top: 1px dashed #000; padding-top: 10px; }
        .dados-parte strong { display: block; text-transform: uppercase; font-size: 13px; margin-bottom: 5px;}
        .dados-parte span { display: block; }
        @media print { 
            @page { 
                margin: 0.4cm; 
            } 
            body { 
                margin: 0; 
                padding: 0; /* Remove o padding extra no modo de impressão, pois @page já cuida da margem */
            } 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Instrumento Particular de Confissão de Dívida</h1>
        </div>

        <div class="content">
            <div class="dados-parte">
                <strong>CREDORA:</strong>
                <span><?php echo htmlspecialchars($config_loja['nome_loja']); ?></span>
                <?php 
                $endereco_linhas = explode('\n', $config_loja['endereco']);
                foreach ($endereco_linhas as $linha): 
                ?>
                    <span><?php echo htmlspecialchars($linha); ?></span>
                <?php endforeach; ?>
                <span>Telefone: <?php echo htmlspecialchars($config_loja['telefone']); ?></span>
                <?php if (!empty($config_loja['cnpj'])): ?>
                    <span>CNPJ: <?php echo htmlspecialchars($config_loja['cnpj']); ?></span>
                <?php endif; ?>

            </div>
            <div class="dados-parte">
                <strong>DEVEDOR(A):</strong>
                <span>Nome: <?php echo htmlspecialchars($venda['cliente_nome']); ?></span>
                <span>CPF: <?php echo htmlspecialchars($venda['cliente_cpf']); ?></span>
                <?php 
                $endereco_completo = '';
                if ($venda['cliente_logradouro']) {
                    $endereco_completo = $venda['cliente_logradouro'];
                    if ($venda['cliente_numero']) $endereco_completo .= ', ' . $venda['cliente_numero'];
                    if ($venda['cliente_complemento']) $endereco_completo .= ', ' . $venda['cliente_complemento'];
                    if ($venda['cliente_bairro']) $endereco_completo .= ', ' . $venda['cliente_bairro'];
                    if ($venda['cliente_cidade']) $endereco_completo .= ', ' . $venda['cliente_cidade'];
                    if ($venda['cliente_estado']) $endereco_completo .= ' - ' . $venda['cliente_estado'];
                    if ($venda['cliente_cep']) $endereco_completo .= ', CEP: ' . $venda['cliente_cep'];
                }
                ?>
                <span>Endereço: <?php echo htmlspecialchars($endereco_completo ?: 'Não informado'); ?></span>
            </div>
            
            <p>Pelo presente instrumento, o(a) DEVEDOR(A) acima qualificado(a) confessa ser devedor(a) da CREDORA da quantia líquida, certa e exigível de <strong><?php echo valorPorExtenso($venda['valor_total']); ?></strong>.</p>
            
            <p>A dívida origina-se da aquisição de produtos e/ou serviços referentes à Venda Nº <?php echo $venda['venda_id']; ?><?php if ($os_id): ?>, vinculada à OS Nº <?php echo $os_id; ?><?php endif; ?>, detalhados abaixo.</p>
        </div>

        <div class="section">
            <h2>Detalhamento do Débito</h2>
            <table>
                <tbody>
                    <?php foreach ($servicos as $servico): ?>
                        <tr><td><?php echo htmlspecialchars($servico['servico_nome']); ?></td><td class="text-right"><?php echo valorPorExtenso($servico['preco_cobrado']); ?></td></tr>
                    <?php endforeach; ?>
                    <?php foreach ($produtos as $produto): ?>
                        <tr><td><?php echo $produto['quantidade']; ?>x <?php echo htmlspecialchars($produto['produto_nome']); ?></td><td class="text-right"><?php echo valorPorExtenso($produto['quantidade'] * $produto['preco_unitario_cobrado']); ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total-geral">
                <span>VALOR TOTAL DA DÍVIDA:</span>
                <span style="float: right;"><?php echo valorPorExtenso($venda['valor_total']); ?></span>
                <div style="clear: both;"></div>
            </div>
        </div>

        <div class="section content">
            <p>O valor confessado será pago pelo(a) DEVEDOR(A) à CREDORA até a data de <strong><?php echo $data_vencimento; ?></strong>.</p>
            <p>O não pagamento na data aprazada implicará na incidência de multa de 2% (dois por cento) sobre o valor total do débito, acrescido de juros de mora de 1% (um por cento) ao mês e correção monetária.</p>
            <p>Este instrumento constitui Título Executivo Extrajudicial, nos termos do Art. 784, III, do Código de Processo Civil, podendo ser executado em caso de inadimplência.</p>
        </div>
        
        <p style="text-align: center; margin-top: 20px;"><?php echo htmlspecialchars($cidade_empresa); ?>, <?php echo date("d/m/Y"); ?></p>

        <div class="assinatura">
            <p>___________________________________</p>
            <p><?php echo htmlspecialchars($venda['cliente_nome']); ?><br>(DEVEDOR/A)</p>
        </div>
        <div class="assinatura">
            <p>___________________________________</p>
            <p><?php echo htmlspecialchars($empresa_nome); ?><br>(CREDORA)</p>
        </div>
        <div class="assinatura">
            <p>___________________________________</p>
            <p>Testemunha 1 (Nome/CPF)</p>
        </div>
        <div class="assinatura">
            <p>___________________________________</p>
            <p>Testemunha 2 (Nome/CPF)</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>