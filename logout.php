<?php

require("C:/xampp/htdocs/register/core/init.php");

$user = new User();
$user->logout();
Redirect::to("index.php");
