<?php
require_once "C:/xampp/htdocs/ooplr/ooplr/core/init.php";

echo "<a href='index.php'> Home </a> <br> ";

$user = new User();
if(!$user->isLoggedIn()){
    Redirect::to("index.php");
}

if(Input::exists()){
    if(Token::check(Input::get("token"))){
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            "current_pass" => array(
                "required" => true,
                "min" => 6
            ),
            "new_pass" => array(
                "required" => true,
                "min" => 6
            ),
            "again_pass" => array(
                "required" => true,
                "matches" => "new_pass"
            )
        ));
        if($validate->passed()){
            try{
                if(Hash::make(Input::get("current_pass"), $user->data()->salt) !==  $user->data()->password){
                    echo "your current pass is wrong";
                }
                else{
                    $salt = Hash::salt(32);
                    $user->update("users", array(
                        "password" => Hash::make(Input::get("new_pass"), $salt),
                        "salt" => $salt
                    ));
                    Session::flash("home", "Your pass has been changed <br>");
                    Redirect::to("index.php");
                }


            }
            catch(Exception $e){
                echo $e->getMessage();
            }

        }
        else{
            foreach($validate->errors() as $error){
                echo $error."<br>";
            }
        }
    }
}


?>

<form action="" method="post">

    <label for="current_pass"> Current password </label>
    <input type="password" name="current_pass" id="current_pass"> <br>

    <label for="new_pass"> New password </label>
    <input type="password" name="new_pass" id="new_pass"> <br>

    <label for="again_pass"> Again </label>
    <input type="password" name="again_pass" id="again_pass"> <br>

    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
    <input type="submit" value="Change">

</form>




