<?php
    session_start();
    require_once '../connect.php';

    $page = $_POST['page'];
    $user_id = $_POST['user_id'];
    $name_pet = $_POST['name_pet'];
    $path = 'uploads/' . $user_id. '/' . time() . $user_id . $_FILES['image']['name'];
    $type = $_FILES['image']['type'];
    $size = $_FILES['image']['size'];
    $status = $_POST['status'];

    

    if ($type === 'image/jpeg' || $type === 'image/bmp') {
        if ($size <= '2097152') {
            move_uploaded_file($_FILES['image']['tmp_name'], '../' . $path);
            $pdo->prepare('INSERT INTO applications (id, id_user, name_pet, foto_pet, status) VALUES (NULL, ?, ?, ?, ?)')->execute([
                $user_id,
                $name_pet,
                $path,
                $status
            ]);
            $_SESSION['messageApp'] = 'Заявка успешно отправлена!';
            header('Location: ../user/?page=' . $page . '');
        } else {
            $_SESSION['messageAppErrSize'] = 'Размер фотографии должен быть не более 2мб';
            header('Location: ../user/?page=' . $page . '');
        }
    } else {
        $_SESSION['messageAppErrType'] = 'Неверный формат фотографии';
        header('Location: ../user/?page=' . $page . '');
    }

    

    

    
?>