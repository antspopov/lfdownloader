<?php
	    session_start();
	 
	    function Destroy() {
	        unset($_SESSION['user_id']);
            setcookie('login', '', 0, "/");
            setcookie('password', '', 0, "/");
	        header("location: login.php");
	    }
	 
	    if (!isset($_SESSION['user_id'])) {
	        Destroy();
	    }
	?>