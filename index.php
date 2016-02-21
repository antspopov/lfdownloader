<?php include("checkauth.php"); ?>
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

    <body data-spy="scroll" data-target=".navbar" data-offset="50">
        <nav class="navbar navbar-fixed-top">
            <div class="container" id='alert-box'></div>
        </nav>

        <div class="container">
            <h2><br><br>LostFilm Downloader<br></h2>
            <a href="auth.php?logout">Выйти (<?php echo $_SESSION['user_id']; ?>)</a>
            <div class="panel-group" id="accordion">
                <?php
                    // выбираем все значения из таблицы "Serials"
                    include_once "db.php";
                    $serials_result = mysql_query("select * from Serials ORDER BY Name ASC")
                    or die(mysql_error());
                    while($serials_data = mysql_fetch_array($serials_result)){
                        echo '  <div class="panel panel-default">';
                        echo '      <div class="panel-heading">';
                        echo '          <h4 class="panel-title">';
                        echo '              <div class="row">';
                        echo '                  <div class="col-sm-4">';
                        echo '                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse' . $serials_data['ID'] . '" class="panel-title"><b>' . $serials_data['Name'] . '</b><br>' . $serials_data['Name_ENG'] . '</a>';
                        echo '                  </div>';
                        echo '                  <div name="status" id=' . $serials_data['ID'] . ' class="col-sm-4">';
                        echo '                  </div>';
                        echo '                  <div class="col-sm-4" align="right">';
                        echo '                      <div class="btn-group">';
                        if ($serials_data['DL'] == 0){
                            echo '                          <button id="' . $serials_data['ID'] . '" name="serial" type="button" class="btn btn-success active" >Включить загрузку</button>';
                        }
                        else{
                            echo '                          <button id="' . $serials_data['ID'] . '" name="serial" type="button" class="btn btn-danger active">Выключить загрузку</button>';
                        }
                        echo '                      </div>';
                        echo '                  </div>';
                        echo '              </div>';
                        echo '          </h4>';
                        echo '      </div>';
                        echo '      <div id="collapse' . $serials_data['ID'] . '" class="panel-collapse collapse">';
                        echo '          <div class="panel-body">';
                        $season_result = mysql_query("select DISTINCT Season from Episodes where Serial = " . $serials_data['ID'])
                        or die(mysql_error());
                        while($season_data = mysql_fetch_array($season_result)){
                            echo '             <table class="table table-striped">';
                            echo '                  <thead>';
                            echo '                      <tr>';
                            echo '                          <th width=30%>' . $season_data['Season'] . ' Сезон</th>';
                            echo '                          <th width=60%></th>';
                            echo '                          <th width=10%></th>';
                            echo '                      </tr>';
                            echo '                  </thead>';
                            echo '                  <tbody>';
                            $query_result = mysql_query("select * from Episodes where Serial = " . $serials_data['ID'] . " AND Season = " . $season_data['Season'] . " AND Episode = 99")
                            or die(mysql_error());
                            $query_data = mysql_fetch_array($query_result);
                            // выбираем эпизоды из таблицы "Episode"
                            $episode_result = mysql_query("select * from Episodes where Serial = " . $serials_data['ID'] . " AND Season = " . $season_data['Season'] . " ORDER BY Episode DESC")
                            or die(mysql_error());
                            while($episode_data = mysql_fetch_array($episode_result)){
                                echo '                  <tr>';
                                echo '                      <td><b>' . $episode_data['Episode'] . '</b>   ' . $episode_data['Name'] . '<br>' . $episode_data['Name_ENG'] . '</td>';
                                echo '                      <td name="status" id=' . $episode_data['ID'] . '>';
                                echo '                      </td>';
                                echo '                      <td>';
                                if ($serials_data['DL'] == 1){
                                    if ( $episode_data['Episode'] == 99 ){
                                        echo '                  <button type="button" id="' . $episode_data['ID'] . '" name="season_' . $serials_data['ID'] . '_' . $episode_data['Season'] . '" class="btn btn-success disabled">Включить загрузку</button>';
                                    }
                                    else{
                                        echo '                  <button type="button" id="' . $episode_data['ID'] . '" name="episode_' . $serials_data['ID'] . '_' . $episode_data['Season'] . '" class="btn btn-success disabled">Включить загрузку</button>';
                                    }
                                }
                                else{
                                    if ($episode_data['DL'] == 0){
                                        if ( $episode_data['Episode'] == 99 ){
                                            echo '              <button type="button" id="' . $episode_data['ID'] . '" name="season_' . $serials_data['ID'] . '_' . $episode_data['Season'] . '" class="btn btn-success active">Включить загрузку</button>';
                                        }
                                        else{
                                            if ( $query_data['DL'] == 1 ){
                                                echo '          <button type="button" id="' . $episode_data['ID'] . '" name="episode_' . $serials_data['ID'] . '_' . $episode_data['Season'] . '" class="btn btn-success disabled">Включить загрузку</button>';
                                            }
                                            else{
                                                echo '          <button type="button" id="' . $episode_data['ID'] . '" name="episode_' . $serials_data['ID'] . '_' . $episode_data['Season'] . '" class="btn btn-success active">Включить загрузку</button>';
                                            }
                                        }
                                    }
                                    else{
                                        if ( $episode_data['Episode'] == 99 ){
                                            echo '              <button type="button" id="' . $episode_data['ID'] . '" name="season_' . $serials_data['ID'] . '_' . $episode_data['Season'] . '" class="btn btn-danger active">Выключить загрузку</button>';
                                        }
                                        else{
                                            echo '              <button type="button" id="' . $episode_data['ID'] . '" name="episode_' . $serials_data['ID'] . '_' . $episode_data['Season'] . '" class="btn btn-danger active">Выключить загрузку</button>';
                                        }
                                    }
                                }
                                echo '                      </td>';
                                echo '                  </tr>';
                            }
                            echo '                  </tbody>';
                            echo '              </table>';
                        }
                        echo '              </div>';
                        echo '          </div>';
                        echo '      </div>';
                    }
                ?>
            </div>
        </div>
    </body>
</html>



<?php
	// закрываем соединение с сервером  базы данных
	mysql_close($connect_to_db);
?>

