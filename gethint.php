<?php
// Dados para conexão com o banco de dados
$servidor = "localhost";
$usuario = "root";           // ou seu usuário do MySQL
$senha = "";                 // ou sua senha
$banco = "testeajax";

// Cria a conexão
$conexao = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica se houve erro na conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Pega o termo digitado enviado pela URL (ex: via Ajax)
$jogoDigitado = $_GET["q"] ?? '';

// Evita caracteres maliciosos (proteção contra SQL Injection)
$jogoDigitado = $conexao->real_escape_string($jogoDigitado);

$sugestao  = ""; // Armazena a resposta final

if (!empty($jogoDigitado)) {
    // Monta a consulta para buscar produtos(jogos) que começam com a letra
    $sql = "SELECT id, nome, link FROM produtos WHERE nome LIKE '$jogoDigitado%'";
    $sugestao  = $conexao->query($sql);

    if ($sugestao && $sugestao->num_rows > 0) {
        $jogosEncontrados = [];

        while ($linha = $sugestao->fetch_assoc()) {
            $id = $linha["id"];
            $nome = htmlspecialchars($linha["nome"]);
            $link = htmlspecialchars($linha["link"]);
            $jogosEncontrados[] = "<a href='$link' target='_blank'>$nome</a>";
        }
        // Junta os nomes encontrados em uma string separada por vírgulas
        $sugestao  = implode(", ", $jogosEncontrados);
    } else {
        $sugestao  = "Sem sugestão";
    }
}

// Exibe a resposta
echo $sugestao ;

// Fecha a conexão
$conexao->close();
?>
