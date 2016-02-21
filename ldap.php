<?php
    if (file_exists('config.ini')) {
        $config = parse_ini_file('config.ini',1);
        $ldaphost = $config['ldap']['ldaphost'];
        $ldapport = $config['ldap']['ldapport'];
        $memberof = $config['ldap']['memberof'];
        $base = $config['ldap']['base'];
        $filter = $config['ldap']['filter'];
        $domain = $config['ldap']['domain'];
    }
    else {
        echo '<script>alert("Нет конфигурационного файла.");</script>';
        exit;
    }
?>