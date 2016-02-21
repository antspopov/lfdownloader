<?php
    // Подключаем файл auth.php
    include_once ("auth.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>LostFilm Downloader</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/button.js"></script>
        <style>
            body {
                position: relative;
            }
            #section1 {padding-top:50px;height:500px;color: #fff; background-color: #1E88E5;}
            #section2 {padding-top:50px;height:500px;color: #fff; background-color: #673ab7;}
            #section3 {padding-top:50px;height:500px;color: #fff; background-color: #ff9800;}
            #section41 {padding-top:50px;height:500px;color: #fff; background-color: #00bcd4;}
            #section42 {padding-top:50px;height:500px;color: #fff; background-color: #009688;}
        </style>
    </head>

    <!-- HTML-код модального окна -->
    <div id="myModalBox" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Заголовок модального окна -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Авторизация</h4>
                </div>
                <!-- Основное содержимое модального окна -->
                <?php
                    // Форма для ввода пароля и логина
                    print '
                        <form role="form" action="login.php" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="user">Логин:</label>
                                    <input type="text" name="login" class="form-control" id="user">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Пароль:</label>
                                    <input type="password" name="password" class="form-control" id="pwd">
                                </div>
                            </div>
                            <!-- Футер модального окна -->
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-default">Войти</button>
                            </div>
                        </form>
                    ';
                ?>
            </div>
        </div>
    </div>

    <!-- Скрипт, вызывающий модальное окно после загрузки страницы -->
    <script>
        $(document).ready(function() {
            $("#myModalBox").modal('show');
        });
    </script>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=koi8-r" />
        <title>Postfix Транспорт</title>
    </head>

</html>