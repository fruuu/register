<?php
include_once("C:/xampp/htdocs/register/core/init.php");
echo "<a href='index.php'> Home </a> <br> ";

if(Input::exists()){
    if(Token::check(Input::get("token"))){
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            "username" => array("required" => true),
            "password" => array("required" => true)
        ));

        if($validate->passed()){
            $user = new User;
            $remember = (Input::get("remember") === "on") ? true : false;

            $login = $user->login(Input::get("username"), Input::get("password"), $remember);
            if($login){
                Redirect::to("index.php");
            }
            else{
                echo "not success";
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

    <div>
        <label> Username: </label>
        <input type="text" id="username" name="username"><br>
    </div>

    <div>
        <label id="password"> Password:</label>
        <input type="password" id="password" name="password"><br>
    </div>
    <div>
        <label for="remember">
            <input type="checkbox" id="remember" name="remember"> Remember me
        </label>

    </div>

    <input type="hidden" name="token" value="<?php echo Token::generate() ?>"><br>
    <input type="submit" value="Submit">
</form>

































