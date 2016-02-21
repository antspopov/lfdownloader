<?php
    include("checkauth.php");
    include_once "db.php";

    switch ($_GET['type']){
        case 'serial':
            if ($_GET['id'] == ""){
                $episode_result = mysql_query("SELECT * from Serials");
            }
            else{
                $episode_result = mysql_query("SELECT * from Serials WHERE ID = " . $_GET['id']);
            }
            while($row = mysql_fetch_assoc($episode_result)){
                $episode_data[] = $row;
            }
            echo json_encode($episode_data);
            break;
        case 'serial_e':
            $episode_result = mysql_query("SELECT * from Episodes WHERE Serial = " . $_GET['id'] . " AND File != 0");
            while($row = mysql_fetch_assoc($episode_result)){
                $episode_data[] = $row;
            }
            echo json_encode($episode_data);
            break;
        case 'serial_s':
            $episode_result = mysql_query("SELECT DL FROM  Files WHERE ID IN (SELECT FILE FROM  Episodes WHERE FILE !=0 AND SERIAL = " . $_GET['id'] . ")");
            while($row = mysql_fetch_assoc($episode_result)){
                $episode_data[] = $row;
            }
            echo json_encode($episode_data);
            break;
        case 'episode':
            $episode_result = mysql_query("SELECT Episodes.ID, Files.DL, Quality.Quality FROM Files INNER JOIN Episodes ON Episodes.File = Files.ID INNER JOIN Quality ON Episodes.Quality = Quality.ID ORDER BY ID ASC");
            while($row = mysql_fetch_assoc($episode_result)){
                $episode_data[] = $row;
            }
            echo json_encode($episode_data);
//            $episode_result = mysql_query("select * from Episodes WHERE ID = " . $_GET['id'])
//            or die(mysql_error());
//            $episode_data = mysql_fetch_array($episode_result);
//            if ($episode_data['Quality'] != "") {
//                $quality_result = mysql_query("SELECT * FROM Quality WHERE ID = " . $episode_data['Quality'])
//                or die(mysql_error());
//                $quality_data = mysql_fetch_array($quality_result);
//            }
//            if ($episode_data['File'] != 0){
//                $file_result = mysql_query("SELECT * FROM Files WHERE ID = " . $episode_data['File'])
//                or die(mysql_error());
//                $file_data = mysql_fetch_array($file_result);
//                echo '[{"Status":"' . $file_data['DL'] . '","Quality":"' . $quality_data['Quality'] . '"}]';
//            }
//            else {
//                echo '[{"Status":"2"}]';
//            }
            break;
    }
?>