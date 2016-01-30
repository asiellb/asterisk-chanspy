<?php
//amp sets in func.php
$amp=amp();
$cdrdb = new mysqli('localhost', $amp['AMPDBUSER'], $amp['AMPDBPASS'], "asteriskcdrdb");
if ($cdrdb->connect_errno) {
    printf("Не удалось подключиться: %s\n", $cdrdb->connect_error);
    exit();
}
$asteriskdb = new mysqli('localhost', $amp['AMPDBUSER'], $amp['AMPDBPASS'], "asterisk");
if ($asteriskdb->connect_errno) {
    printf("Не удалось подключиться: %s\n", $asteriskdb->connect_error);
    exit();
}

?>