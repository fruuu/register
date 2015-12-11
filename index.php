<?php

require_once ("core/init.php");

//--------------------------------------------------------------------------------------------------------------------
//Testing:

//DB::count_p(array("boby"));


//$user = DB::getInstance()->get("users", array("username", "in", DB::count_p(array("cool"))) );

//$niz = array("novi", "novi,", "novi","novi", date("Y-m-d H:i:s"), 1);


//$user =DB::getInstance()->insert("users", DB::count_p($niz));

//$user =DB::getInstance()->update("users", array("username", "password", "name", "joined"),
  //                              DB::count_p(array("user_ashely", "pass_ashley","ashleyyyyy","0000--11--11 ==:12:00")), 14);

/*
if(Session::exists("home")){
    echo "session exists";
    echo Session::get("home");
}
*/

//-------------------------------------------------------------------------------------------------------------------

if(!Session::exists(Config::get("session/session_name"))){
    echo "<h4> Hello </h4>";
}

$user = new User();

if(Session::exists("home")){
    echo Session::flash("home");
}



if($user->isLoggedIn()){
    echo "Current user: ".$user->data()->username."<br>";
    if($user->permission("admin")){
        echo "You are administrator <br> <br>";
    }

    echo "<a href='update.php'> Update informations </a> <br>";
    echo "<a href='changepassword.php'> Change password </a> <br>";
    echo "<a href='logout.php'> Logout </a> <br>";

}
else{
    echo "You need to <a href='login.php'> log in </a> or <a href='register.php'> register </a>";
}


echo "<br><br> Default user and pass: username password. <br> <br>";

?>






