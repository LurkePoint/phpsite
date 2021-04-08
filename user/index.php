<?php
    session_start();
    require_once '../connect.php';
    if (!isset($_SESSION['user'])) {
        header('Location: /');
    }
    if ($_SESSION['status_user'] === '10') {
        header('Location: /');
    }
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
    <link rel="stylesheet" href="../style.css" type="text/css">
    <title>Site</title>
</head>
<body style="background-image: url(../images/fon.jpg);">
    <header>
        <div class="header_block">
        <div class="header_block_logo">
            <a href="/">
            <img class="logo" src="../images/logo.png" alt="">
            </a>
        </div>
        <div class="header_block_menu">
            <nav class="header_menu">
                <?if(isset($_SESSION['user']) !== false):?>
                <div class="header_menu_item"><a href="/">Главная</a></div>
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
        <div class="main_kabinet">
        <?php
            $name_user = explode(' ', $_SESSION['user_fio']);
            if (isset($_SESSION['user']) !== false) {
                echo 'Здравствуйте, ' . $name_user[1] . '!';
                echo '<br><a href="../vendor/exit_vendor.php">Выход</a>';
            }
        ?>
        </div>
        <h1 style="padding-left: 10px;">Оставить заявку:</h1><br>
        <?if(isset($_SESSION['user']) !== false):?>
                <form class="form_pet" action="../vendor/applications_vendor.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>"></input>
                <input type="hidden" name="page" value="<?= $page ?>"></input>
                <label class="label_user">Кличка домашнего животного:</label>
                <input type="text" name="name_pet" placeholder="Введите кличку" required></input>
                <label class="label_user">Фотография домашнего животного (формат фотографии - jpeg или bmp):</label>
                <input class="file" type="file" name="image" required></input>
                <input type="hidden" name="status" value="Новая"></input>
                <button type="submit">Отправить</button> 
                <?php
                    if (isset($_SESSION['messageApp']) !== false) {
                        echo '<h4 style="border: 2px solid lightgreen; padding: 5px;">'. $_SESSION['messageApp'] . '</h4>';
                        unset($_SESSION['messageApp']);
                    }
                    if (isset($_SESSION['messageAppErrType']) !== false) {
                        echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messageAppErrType'] . '</h4>';
                        unset($_SESSION['messageAppErrType']);
                    }
                    if (isset($_SESSION['messageAppErrSize']) !== false) {
                        echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messageAppErrSize'] . '</h4>';
                        unset($_SESSION['messageAppErrSize']);
                    }
                ?>
                </form>
            <div class="applications">
                <h1>Ваши заявки:</h1><br>
                <?php
                if (isset($_SESSION['messageDelete']) !== false) {
                    echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messageDelete'] . '</h4>';
                    unset($_SESSION['messageDelete']);
                }
                ?>
                <div class="block_pag">
                    <?php
                    
                    $user_id = $_SESSION['user_id'];

                    $applications = $pdo->prepare('SELECT * FROM applications WHERE id_user = :userid ORDER BY id DESC LIMIT :appFrom,:appCountOnPage');
                    $applications->bindValue(':userid', $user_id, PDO::PARAM_INT);
                    $applications->bindValue(':appFrom', $appFrom, PDO::PARAM_INT);
                    $applications->bindValue(':appCountOnPage', $appCountOnPage, PDO::PARAM_INT);
                    $applications->execute();

                    $applicationsCount = $pdo->prepare('SELECT * FROM applications WHERE id_user = ?');
                    $applicationsCount->execute([
                        $user_id
                    ]);
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
                            if ($app['status'] === 'Услуга оказана') {
                                echo '  <div class="block_app" style="background-color: #BDECB6;">
                                        <div class="block_app_item">Кличка питомца: ' . '<span class="pet">' . $app['name_pet'] . '</span>' . '</div>
                                        <div class="block_app_item"><img src="../' . $app['foto_pet_admin'] . '" alt=""></div>
                                        <div class="block_app_item">Статус заявки: ' . '<span class="status">' . $app['status'] . '</span>' . '</div>
                                        </div>
                                    ';
                            } elseif ($app['status'] === 'Обработка данных') {
                                echo '  <div class="block_app" style="background-color: #98FB98;">
                                        <div class="block_app_item">Кличка питомца: ' . '<span class="pet">' . $app['name_pet'] . '</span>' . '</div>
                                        <div class="block_app_item">Статус заявки: ' . '<span class="status">' . $app['status'] . '</span>' . '</div>
                                        </div>
                                    ';
                            } elseif ($app['status'] === 'Новая') {
                                echo '  <div class="block_app">
                                        <div class="block_app_item">Кличка питомца: ' . '<span class="pet">' . $app['name_pet'] . '</span>' . '</div>
                                        <div class="block_app_item">Статус заявки: ' . '<span class="status">' . $app['status'] . '</span>' . '</div>
                                        <div class="block_app_item">
                                        <form action="../vendor/delete_vendor.php" method="post">
                                        <input type="hidden" name="id" value="'. $app['id'] .'"></input>
                                        <input type="hidden" name="page" value="' . $page . '"></input>
                                        <button class="deleteApp" type="submit">Удалить запись</button>
                                        </form>
                                        </div>
                                        </div>
                                    ';
                            }
                        }
                    } else {
                        echo '<div class="msg_else">Список ваших заявок пуст</div>';
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
        <?endif?>
    </div>
    <footer>
        Grooming
    </footer>   
</body>
</html>