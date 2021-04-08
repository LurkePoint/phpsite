<?php
    session_start();
    require_once '../connect.php';

    $page = $_POST['page'];
    $login = $_POST['login'];
    $password = md5($_POST['password']);

    $check_user = $pdo->prepare('SELECT * FROM users WHERE login = ? AND password = ?');
    $check_user->execute([
        $login,
        $password
    ]);

    $user = $check_user->fetch();

    if ($check_user->rowCount() > 0) {
        if ($user['status_user'] === '1') {
            $_SESSION['user'] = $user['login'];
            $_SESSION['user_fio'] = $user['fio'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['status_user'] = $user['status_user'];
            header('Location: ../user');
        } else {
            $_SESSION['user'] = $user['login'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['status_user'] = $user['status_user'];
            header('Location: ../groom');
        }
    } else {
        $_SESSION['messageNo'] = 'Неверный логин или пароль';
        header('Location: /?page=' . $page . '');
    }