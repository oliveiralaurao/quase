<?php

function cadastra($mysqli, $nome, $sobrenome, $email, $telefone, $senha, $senha_repetida, $rede_social, $hobbie, $descricao, $foto, $nivel_usuario) {
    if (!empty($nome) && !empty($sobrenome) && !empty($email) && !empty($telefone) && !empty($senha) && !empty($senha_repetida) && !empty($foto) && !empty($nivel_usuario)) {
        
        if (isset($foto) && $foto['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../upload/';
            $fotoNome = basename($foto['name']);
            $fotoPath = $uploadDir . $fotoNome;

            if (move_uploaded_file($foto['tmp_name'], $fotoPath)) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                // Note que agora estamos usando $fotoNome em vez de $fotoPath no INSERT
                $query = "INSERT INTO usuario (nome_usuario, sobrenome_usuario, email_usuario, tel_usuario, senha_usuario, rede_social_usuario, hobbie_usuario, desc_usuario, foto_usuario, tipo_usuario) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                if ($stmt = $mysqli->prepare($query)) {
                    $stmt->bind_param("ssssssssss", $nome, $sobrenome, $email, $telefone, $senha_hash, $rede_social, $hobbie, $descricao, $fotoNome, $nivel_usuario);

                    if ($stmt->execute()) {
                        $referer = $_SERVER['HTTP_REFERER'];

                        if (strpos($referer, 'login.php') !== false) {
                            header("Location: ../public/login.php?msg=" . urlencode("Usuário criado com sucesso! Faça login."));
                        } elseif (strpos($referer, 'usuariocadastro.php') !== false) {
                            header("Location: ../frontBack/list/listUser.php?msg=" . urlencode("Usuário criado com sucesso! Você pode continuar."));
                        } else {
                            header("Location: ../frontBack/list/listUser.php?msg=" . urlencode("Usuário criado com sucesso!"));
                        }

                        exit();
                    } else {
                        echo "Erro ao inserir registro: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    echo "Erro ao preparar a query: " . $mysqli->error;
                }
            } else {
                echo "Erro ao fazer upload da foto.";
            }
        } else {
            echo "A foto é obrigatória.";
        }
    } else {
        echo "<script>alert('Por favor, preencha todos os campos obrigatórios.');</script>";
    }
}


?>
