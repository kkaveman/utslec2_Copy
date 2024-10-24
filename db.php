<?php

define('DSN','mysql:host=localhost;dbname=event_mn');
define('DBUSER','root');
define('DBPASS','1mperman3nt#');//change this

$db = new PDO(DSN,DBUSER,DBPASS);
