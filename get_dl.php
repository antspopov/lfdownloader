<?php
    include("checkauth.php");
    include_once "db.php";

    if ( $_GET['type'] == 'serial' ){
        $dl_result = mysql_query("SELECT * FROM Serials WHERE ID = " . $_GET['id'])
        or die(mysql_error());

        $dl_data = mysql_fetch_array($dl_result);

        if ( $dl_data['DL'] == 0 ){
            $dl2_result = mysql_query("UPDATE Serials SET DL = 1 WHERE ID = " . $_GET['id'])
            or die(mysql_error());
            $dl2_result = mysql_query("UPDATE Episodes SET DL = 0 WHERE Serial = " . $_GET['id'])
            or die(mysql_error());
            exec('./updater.py');
        }
        else{
            $dl2_result = mysql_query("UPDATE Serials SET DL = 0 WHERE ID = " . $_GET['id'])
            or die(mysql_error());
        }
    }
    else{
        $dl_result = mysql_query("SELECT * FROM Episodes WHERE ID = " . $_GET['id'])
        or die(mysql_error());

        $dl_data = mysql_fetch_array($dl_result);

        if ( $dl_data['DL'] == 0 ){
            $temp = explode("_", $_GET['type']);
            if ( $temp[0] == 'season' ){
                $dl2_result = mysql_query("UPDATE Episodes SET DL = 0 WHERE Serial = " . $temp[1] . " AND Season = " . $dl_data['Season'])
                or die(mysql_error());
            }
            $dl2_result = mysql_query("UPDATE Episodes SET DL = 1 WHERE ID = " . $_GET['id'])
            or die(mysql_error());
            echo 2;
            exec('./episode_dl.py ' . $_GET['id']);
        }
        else{
            $dl2_result = mysql_query("UPDATE Episodes SET DL = 0 WHERE ID = " . $_GET['id'])
            or die(mysql_error());
        }
    }
?>