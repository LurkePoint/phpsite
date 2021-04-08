<?php
    session_start();
    require_once 'connect.php';

    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }

    $appCountOnPage = 4;
    $appFrom = ($page - 1) * $appCountOnPage;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" type="text/css">
    <title>Site</title>
</head>
<body style="background-image: url(images/fon.jpg);">
    <header>
        <div class="header_block">
        <div class="header_block_logo">
            <a href="/">
            <img class="logo" src="images/logo.png" alt="">
            </a>
        </div>
        <div class="header_block_menu">
            <nav class="header_menu">
            <div class="header_menu_item"><a href="/">Главная</a></div>
                <?if(isset($_SESSION['user']) !== false):?>
                    <?if($_SESSION['status_user'] === '1'):?>
                        <div class="header_menu_item"><a href="/user">Личный кабинет</a></div>
                    <?endif?>
                    <?if($_SESSION['status_user'] === '10'):?>
                        <div class="header_menu_item"><a href="/groom">Личный кабинет</a></div>
                    <?endif?>
                <?endif?>
            </nav>
        </div>
        </div>
    </header>
    <div class="main">
        <?if(!isset($_SESSION['user'])):?>
        <div class="block_form">
        <form class="form" action="vendor/avtoriz_vendor.php" method="post">
            <h2>Авторизация</h2>
            <input type="hidden" name="page" value="<?= $page ?>"></input>
            <input type="text" placeholder="Введите логин" name="login" required></input>
            <input type="password" placeholder="Введите пароль" name="password" required></input>
            <button type="submit">Авторизоваться</button>
            <?php
                if (isset($_SESSION['messageNo']) !== false) {
                    echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messageNo'] . '</h4>';
                    unset($_SESSION['messageNo']);
                }
            ?>
        </form>
        <form class="form" action="vendor/registr_vendor.php" method="post">
            <h2>Регистрация</h2>
            <input type="hidden" name="page" value="<?= $page ?>"></input>
            <input type="text" name="fio" placeholder="Введите ФИО" required></input>
            <input type="text" name="login" placeholder="Введите логин" required></input>
            <input class="email" type="email" name="email" placeholder="Введите email" required></input>
            <input type="password" name="password" placeholder="Введите пароль" required></input>
            <input type="password" name="password_confirm" placeholder="Повторите пароль" required></input>
            <label class="check">
            <input type="checkbox" class="check_input" required>Соглашаюсь на обработку персональных данных</input>
            </label>
            <button type="submit">Зарегистрироваться</button>
            
            <?php
                if (isset($_SESSION['messagePass']) !== false) {
                    echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messagePass'] . '</h4>';
                    unset($_SESSION['messagePass']);
                }
                if (isset($_SESSION['messageLoginEmail']) !== false) {
                    echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messageLoginEmail'] . '</h4>';
                    unset($_SESSION['messageLoginEmail']);
                }
                if (isset($_SESSION['messageLogin']) !== false) {
                    echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messageLogin'] . '</h4>';
                    unset($_SESSION['messageLogin']);
                }
                if (isset($_SESSION['messageEmail']) !== false) {
                    echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messageEmail'] . '</h4>';
                    unset($_SESSION['messageEmail']);
                }
                if (isset($_SESSION['messageReg']) !== false) {
                    echo '<h4 style="border: 2px solid lightgreen; padding: 5px;">'. $_SESSION['messageReg'] . '</h4>';
                    unset($_SESSION['messageReg']);
                }
            ?>
            
        </form>
        </div>
        <?endif?>
        <div class="applications">
            <h1>Все выполненные заявки:</h1><br>
            <div class="block_pag">
            <?php
                
                $applications = $pdo->prepare('SELECT * FROM applications WHERE status = "Услуга оказана" ORDER BY id DESC LIMIT :appFrom,:appCountOnPage');
                $applications->bindValue(':appFrom', $appFrom, PDO::PARAM_INT);
                $applications->bindValue(':appCountOnPage', $appCountOnPage, PDO::PARAM_INT);
                $applications->execute();

                $applicationsCount = $pdo->query('SELECT * FROM applications WHERE status = "Услуга оказана"');
                $count = $applicationsCount->rowCount();
                $res = ceil($count / $appCountOnPage);
                
                for ($i = 1; $i <= $res; $i++) {
                    if ($page == $i) {
                        $active = 'active';
                    } else {
                        $active = '';
                    }
                    echo '<a class="' . $active . '" href="?page=' . $i . '">' . $i . '</a> ';
                }
            ?>
            </div>
            <?php

                $appArray = $applications->fetchAll();
 
                if ($applications->rowCount() > 0) {
                    foreach ($appArray as $app) {
                        echo '  <div class="block_app" style="background-color: #BDECB6;">
                                <div class="block_app_item">Кличка питомца: ' . '<span class="pet">' . $app['name_pet'] . '</span>' . '</div>
                                <div class="block_app_item"><img src="' . $app['foto_pet'] . '" alt=""></div>
                                </div>
                            ';
                    }
                } else {
                    echo '<div class="msg_else">Админ ничего не обработал</div>';
                }
             
            ?>
            <div class="block_pag">
            <?php
                for ($i = 1; $i <= $res; $i++) {
                    if ($page == $i) {
                        $active = 'active';
                    } else {
                        $active = '';
                    }
                    echo '<a class="' . $active . '" href="?page=' . $i . '">' . $i . '</a>';
                }
            ?>
            </div>
        </div>
    </div>
    <footer>
        Grooming
    </footer>    
</body>
</html>