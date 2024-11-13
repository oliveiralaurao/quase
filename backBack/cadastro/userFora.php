<?php
require_once('../startup/connectBD.php');

function cadastra($mysqli, $nome, $sobrenome, $email, $telefone, $senha, $foto) {
    // Verifica se o e-mail já existe no banco
    $sql = "SELECT * FROM usuario WHERE email_usuario = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Verifica se a foto foi enviada e se é um array válido
    $foto_usuario = null;
    if (isset($foto) && is_array($foto) && $foto['error'] === UPLOAD_ERR_OK) {
        $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nome_imagem = uniqid() . "." . $extensao;
        $caminho_imagem = '../upload/' . $nome_imagem;
        if (move_uploaded_file($foto['tmp_name'], $caminho_imagem)) {
            // Salva apenas o nome do arquivo
            $foto_usuario = $nome_imagem;
        }
    } else {
        $foto_usuario = '';  // Caso não haja foto, define um valor vazio
    }

    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere no banco de dados
    $sql = "INSERT INTO usuario (nome_usuario, sobrenome_usuario, email_usuario, tel_usuario, senha_usuario, foto_usuario) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssss", $nome, $sobrenome, $email, $telefone, $senha_hash, $foto_usuario);

    if ($stmt->execute()) {
        header("Location: ../public/login.php?msg=" . urlencode("Usuário criado com sucesso! Faça login."));
    } else {
        echo "<script>alert('Erro ao realizar o cadastro. Tente novamente mais tarde.');</script>";
    }
}
?>
