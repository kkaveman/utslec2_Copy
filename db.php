<?php

define('DSN','mysql:host=localhost;dbname=event_mn');
define('DBUSER','root');
define('DBPASS','');//change this

$db = new PDO(DSN,DBUSER,DBPASS);
