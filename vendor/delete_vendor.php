<?php
    session_start();
    require_once '../connect.php';

    $page = $_POST['page'];
    $id = $_POST['id'];

    $select_foto = $pdo->prepare('SELECT foto_pet FROM applications WHERE id = ? AND status = "Новая"');
    $select_foto->execute([
        $id
    ]);

    $foto = $select_foto->fetchColumn();
    if ($select_foto->rowCount() > 0) {
        unlink("../" . $foto);
        $pdo->prepare('DELETE FROM applications WHERE id = ?')->execute([
            $id
        ]);
        header('Location: ../user/?page=' . $page . '');
    } else {
        $_SESSION['messageDelete'] = 'Заявка уже обрабатывается';
        header('Location: ../user/?page=' . $page . '');
    }
     

    