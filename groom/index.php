<?php
    session_start();
    require_once '../connect.php';
    if (!isset($_SESSION['user'])) {
        header('Location: /');
    }
    if ($_SESSION['status_user'] === '1') {
        header('Location: /');
    }
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }

    $appCountOnPage = 4;
    $appFrom = ($page - 1) * $appCountOnPage;

    function msgAppAdmin() {
        if (isset($_SESSION['messageAppErrTypeGroom']) !== false) {
            echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messageAppErrTypeGroom'] . '</h4>';
            unset($_SESSION['messageAppErrTypeGroom']);
        }
        if (isset($_SESSION['messageAppErrSizeGroom']) !== false) {
            echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messageAppErrSizeGroom'] . '</h4>';
            unset($_SESSION['messageAppErrSizeGroom']);
        }
        if (isset($_SESSION['messageDeleteGroom']) !== false) {
            echo '<h4 style="border: 2px solid red; padding: 5px;">'. $_SESSION['messageDeleteGroom'] . '</h4>';
            unset($_SESSION['messageDeleteGroom']);
        }
    }
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
            if (isset($_SESSION['user']) !== false) {
                echo 'Здравствуйте, ' . $_SESSION['user'] . '!';
                echo '<br><a href="../vendor/exit_vendor.php">Выход</a>';
            }
        ?>
        </div>
        <div class="applications">
            <h1>Заявки пользователей:</h1><br>
            <?= msgAppAdmin() ?>
            <div class="block_pag">
            <?php
             
                $applications = $pdo->prepare('SELECT * FROM applications WHERE status != "Услуга оказана" ORDER BY id DESC LIMIT :appFrom,:appCountOnPage');
                $applications->bindValue(':appFrom', $appFrom, PDO::PARAM_INT);
                $applications->bindValue(':appCountOnPage', $appCountOnPage, PDO::PARAM_INT);
                $applications->execute();

                $applicationsCount = $pdo->query('SELECT * FROM applications WHERE status != "Услуга оказана"');
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
                        if ($app['status'] === 'Новая') {
                            echo '  <div class="block_app">
                                <div class="block_app_item">Кличка питомца: ' . '<span class="pet">' . $app['name_pet'] . '</span>' . '</div>
                                <div class="block_app_item"><img src="../' . $app['foto_pet'] . '" alt=""></div>
                                <div class="block_app_item">Статус заявки: ' . '<span class="status">' . $app['status'] . '</span>' . '</div>
                                <div class="block_app_item">
                                <form action="../vendor/updateData_vendor.php" method="post">
                                <input type="hidden" name="id" value="'. $app['id'] .'"></input>
                                <input type="hidden" name="page" value="' . $page . '"></input>
                                <button class="updateData" type="submit">Начать обработку данных</button>
                                </form>
                                </div>
                                </div>
                            ';
                        } else {
                            echo '  <div class="block_app" style="background-color: #98FB98;">
                                <div class="block_app_item">Кличка питомца: ' . '<span class="pet">' . $app['name_pet'] . '</span>' . '</div>
                                <div class="block_app_item"><img src="../' . $app['foto_pet'] . '" alt=""></div>
                                <div class="block_app_item">Статус заявки: ' . '<span class="status">' . $app['status'] . '</span>' . '</div>
                                <div class="block_app_item">
                                <form action="../vendor/updateComplete_vendor.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="'. $app['id'] .'"></input>
                                <input type="hidden" name="page" value="' . $page . '"></input>
                                <input type="hidden" name="id_user" value="'. $app['id_user'] .'"></input>
                                <input class="fileGroom" type="file" name="image" required></input>
                                <button class="updateComplete" type="submit">Закончить обработку данных</button>
                                </form>
                                </div>
                                </div>
                            ';
                        }
                        
                    }
                } else {
                    echo '<div class="msg_else">Заявок нет</div>';
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