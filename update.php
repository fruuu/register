<?php

require_once "C:/xampp/htdocs/register/core/init.php";

echo "<a href='index.php'> Home </a> <br> ";

$user = new User();

if(!$user ->isLoggedIn()){
    Redirect::to("index.php");
}

if(Input::exists()){
    if(Token::check(Input::get("token"))){
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            "name" => array(
                "required" => true,
                "min" => 2,
                "max" => 50
            )
        ));
        if($validate->passed()){
            try{
                $user->update("users",array(
                    "name" => Input::get("name")
                ), $user->data()->id);
                Session::flash("home", "Your details have been updated <br>");
                Redirect::to("index.php");
            }
            catch(Exception $e){
                die($e->getMessage());
            }
        }
    }
}
?>

<form action="" method="post">

    <label for="name"> Name: </label>
    <input type="text" id="name" name="name" value="<?php echo $user->data()->name ?>">
    <input type="hidden" name="token" value="<?php echo Token::generate() ?>"> <br> <br>
    <input type="submit" value="Update">
</form>
