<?php
    //Начинаем сессию
    session_start();
    if (file_exists('config.ini')) {
        $config = parse_ini_file('config.ini',1);
        $authtype = $config['authentication']['type'];
        $simplelogin = $config['authentication']['user'];
        $simplepassword = $config['authentication']['password'];
    }
    else {
        echo '<script>alert("Нет конфигурационного файла.");</script>';
        exit;
    }

    // Logout
    if (isset($_GET['logout']))
    {
        if (isset($_SESSION['user_id']))
            {
            unset($_SESSION['user_id']);
            setcookie('login', '', 0, "/");
            setcookie('password', '', 0, "/");
            header('Location: login.php');
            exit;
        }
    }

    //Если пользователь уже аутентифицирован, то перебросить его на страницу main.php
    if (isset($_SESSION['user_id']))
    {
        header('Location: index.php');
        exit;
    }

    //Если пользователь не аутентифицирован, то проверить его
    if (isset($_POST['login']) && isset($_POST['password']))
    {
        if ($authtype == 'ldap')
        {
            //Подключаем конфигурационный файл
            include_once ("ldap.php");
            $username = $_POST['login'];
            $login = $_POST['login'].$domain;
            $password = $_POST['password'];
            //подсоединяемся к LDAP серверу
            $ldap = ldap_connect($ldaphost,$ldapport) or die("Cant connect to LDAP Server");
            //Включаем LDAP протокол версии 3
            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

            if ($ldap)
            {
                // Пытаемся войти в LDAP при помощи введенных логина и пароля
                $bind = ldap_bind($ldap,$login,$password);

                if ($bind)
                {
                    // Проверим, является ли пользователь членом указанной группы.
                    $result = ldap_search($ldap,$base,"(&(memberOf=".$memberof.")(".$filter.$username."))");
                    // Получаем количество результатов предыдущей проверки
                    $result_ent = ldap_get_entries($ldap,$result);
                }
                else
                {
                    print '
                        <link rel="stylesheet" href="css/bootstrap.min.css">
                        <script src="js/jquery.min.js"></script>
                        <script src="js/bootstrap.min.js"></script>
                        <script src="js/button.js"></script>
                        <div class="container">
                            <div class="alert alert-danger">
                                <strong>Ошибка!</strong> Вы ввели неправильный логин или пароль. Попробуйте еще раз.<br>
                                <div align="right"><a href="login.php" class="btn btn-primary" role="button" >Назад</a></div>
                            </div>
                        </div>
                    ';
                    die();
                }
            }
        }
        elseif ($authtype == 'simple')
        {
            $login = $_POST['login'];
            $password = md5($_POST['password']);
            if ( $login == $simplelogin && $password == $simplepassword )
            {
                $result_ent = array('count' => 1);
            }

        }

        else
        {
            print '
                <link rel="stylesheet" href="css/bootstrap.min.css">
                <script src="js/jquery.min.js"></script>
                <script src="js/bootstrap.min.js"></script>
                <script src="js/button.js"></script>
                <div class="container">
                    <div class="alert alert-danger">
                        <strong>Ошибка!</strong> Не выбран тип аутентификации в файле config.ini.<br>
                        <div align="right"><a href="login.php" class="btn btn-primary" role="button" >Назад</a></div>
                    </div>
                </div>
            ';
            die();
        }

        // Если пользователь найден, то пропускаем его дальше и перебрасываем на main.php
        if ($result_ent['count'] != 0)
        {
            $_SESSION['user_id'] = $login;
            header('Location: index.php');
            exit;
        }
        else
        {
            print '
                <link rel="stylesheet" href="css/bootstrap.min.css">
                <script src="js/jquery.min.js"></script>
                <script src="js/bootstrap.min.js"></script>
                <script src="js/button.js"></script>
                <div class="container">
                    <div class="alert alert-danger">
                        <strong>Ошибка!</strong> Вы ввели неправильный логин или пароль. Попробуйте еще раз.<br>
                        <div align="right"><a href="login.php" class="btn btn-primary" role="button" >Назад</a></div>
                    </div>
                </div>
            ';
            die();
        }
    }
?>

