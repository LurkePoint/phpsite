<?php
    session_start();
    require_once '../connect.php';

    $page = $_POST['page'];
    $id = $_POST['id'];

    $select_row = $pdo->prepare('SELECT * FROM applications WHERE id = ?');
    $select_row->execute([
        $id
    ]);

    if ($select_row->rowCount() > 0) {
        $pdo->prepare('UPDATE applications SET status = "Обработка данных" WHERE id = ?')->execute([
            $id
        ]);
        header('Location: ../groom/?page=' . $page . '');
    } else {
        $_SESSION['messageDeleteGroom'] = 'Заявка уже удалена';
        header('Location: ../groom/?page=' . $page . '');
    }