<?php
        if (file_exists('config.ini')) {
            $config = parse_ini_file('config.ini',1);
            if ($config['mysql']['host'] == "") {
                echo '<script>alert("Не заполнен хост MySQL в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['mysql']['db_name'] == "") {
                echo '<script>alert("Не заполнена база данных MySQL в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['mysql']['db_username'] == "") {
                echo '<script>alert("Не заполнен пользователь MySQL в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['mysql']['db_password'] == "") {
                echo '<script>alert("Не заполнен пароль MySQL в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['lostfilm']['uid'] == "") {
                echo '<script>alert("Не заполнен uid LostFilm в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['lostfilm']['pass'] == "") {
                echo '<script>alert("Не заполнен pass LostFilm в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['lostfilm']['usess'] == "") {
                echo '<script>alert("Не заполнен usess LostFilm в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['lostfilm']['phpbb2mysql_data'] == "") {
                echo '<script>alert("Не заполнен phpbb2mysql_data LostFilm в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['transmission']['host'] == "") {
                echo '<script>alert("Не заполнен хост transmission в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['transmission']['port'] == "") {
                echo '<script>alert("Не заполнен порт transmission в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['transmission']['user'] == "") {
                echo '<script>alert("Не заполнен пользователь transmission в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['transmission']['password'] == "") {
                echo '<script>alert("Не заполнен пароль transmission в конфигурационном файле!");</script>';
                exit;
            }
            if ($config['transmission']['download_dir'] == "") {
                echo '<script>alert("Не заполнена папка закачки transmission в конфигурационном файле!");</script>';
                exit;
            }
        }
        else {
            echo '<script>alert("Нет конфигурационного файла.");</script>';
            exit;
        }

        // определяем начальные данные
        $db_host = $config['mysql']['host'];
        $db_name = $config['mysql']['db_name'];
        $db_username = $config['mysql']['db_username'];
        $db_password = $config['mysql']['db_password'];

        // соединяемся с сервером базы данных
        $connect_to_db = mysql_connect($db_host, $db_username, $db_password)
        or die("Could not connect: " . mysql_error());

        mysql_set_charset( 'utf8' );

        $flag = false;
        $query = "SHOW DATABASES";
        $dbs = mysql_query($query);
        if(!$dbs) exit(mysql_error());

        while($data_base = mysql_fetch_array($dbs,MYSQL_NUM))
        {
            if($data_base[0] == $db_name)
            {
                $flag = true;
            break;
            }
        }
        if($flag) {
            mysql_select_db($db_name, $connect_to_db)
            or die("Could not select DB: " . mysql_error());
        }
        else  {
            echo "База данных " . $db_name . " не cуществует";
            exit;
        }

        // подключаемся к базе данных
?>