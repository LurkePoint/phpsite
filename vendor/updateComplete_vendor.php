<?php
    session_start();
    require_once '../connect.php';

    $page = $_POST['page'];
    $user_id = $_POST['id_user'];
    $id = $_POST['id'];
    $path = 'uploads/' . $user_id. '/' . time() . $id . $_FILES['image']['name'];
    $type = $_FILES['image']['type'];
    $size = $_FILES['image']['size'];

    if ($type === 'image/jpeg' || $type === 'image/bmp') {
        if ($size <= '2097152') {
            move_uploaded_file($_FILES['image']['tmp_name'], '../' . $path);
            $pdo->prepare('UPDATE applications SET foto_pet_admin = ? WHERE id = ?')->execute([
                $path,
                $id
            ]);
            $pdo->prepare('UPDATE applications SET status = "Услуга оказана" WHERE id = ?')->execute([
                $id
            ]);
            header('Location: ../groom/?page=' . $page . '');
        } else {
            $_SESSION['messageAppErrSizeGroom'] = 'Размер фотографии должен быть не более 2мб';
            header('Location: ../groom/?page=' . $page . '');
        }
    } else {
        $_SESSION['messageAppErrTypeGroom'] = 'Неверный формат фотографии';
        header('Location: ../groom/?page=' . $page . '');
    }

    