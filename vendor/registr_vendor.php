<?php
    session_start();
    require_once '../connect.php';

    $page = $_POST['page'];
    $fio = $_POST['fio'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $password_confirm = md5($_POST['password_confirm']);

    

    $query_login = $pdo->prepare('SELECT * FROM users WHERE login = ?');
    $query_login->execute([
        $login
    ]);
    $query_email = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $query_email->execute([
        $email
    ]);

    if ($password !== $password_confirm) {
        $_SESSION['messagePass'] = 'Пароли не совпадают';
        header('Location: /?page=' . $page . '');
    } 
    elseif (($query_login->rowCount() > 0) && ($query_email->rowCount() > 0)) {
        $_SESSION['messageLoginEmail'] = 'Пользователь с таким логином и почтой уже существует';
        header('Location: /?page=' . $page . '');
    }
    elseif ($query_login->rowCount() > 0) {
        $_SESSION['messageLogin'] = 'Пользователь с таким логином уже существует';
        header('Location: /?page=' . $page . '');
    }
    elseif ($query_email->rowCount() > 0) {
        $_SESSION['messageLogin'] = 'Пользователь с такой почтой уже существует';
        header('Location: /?page=' . $page . '');
    }
    
    else {
    
    $pdo->prepare('INSERT INTO users (id, fio, login, email, password, status_user) VALUES (NULL, ?, ?, ?, ?, 1)')->execute([
        $fio,
        $login,
        $email,
        $password
    ]);
    $res = $pdo->prepare('SELECT id FROM users WHERE login = ?');
    $res->execute([
        $login
    ]);
    
    $dir_id = $res->fetchColumn();
    $path_user = "../uploads/{$dir_id}";

    mkdir($path_user, 0777);
    
    $_SESSION['messageReg'] = 'Регистрация прошла успешно!';
    header('Location: /?page=' . $page . '');
    
    }