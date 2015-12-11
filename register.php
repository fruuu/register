<?php

require_once ("core/init.php");
echo "<a href='index.php'> Home </a> <br> ";

if(Input::exists()){
    if(Token::check(Input::get("token"))){
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            "Name" => array(
                "required" => true,
                "min" => 2,
                "max" => 50
            ),
            "Username" => array(
                "required" => true,
                "min" => "2",
                "max" => 20,
                "unique" => "users"
            ),
            "Password" => array(
                "required" => true,
                "min" => 6
            ),
            "pass_again" => array(
                "required" => true,
                "matches" => "Password"
            ),
        ));
        if($validate->passed()){
            $user = new User;
            $salt = Hash::salt(32);
            try{
                $user->create(array(
                    "username" => Input::get("Username"),
                    "password" => Hash::make(Input::get("Password"), $salt),
                    "salt" => $salt,
                    "name" => Input::get("Name"),
                    "joined" => date("Y-m-d H:i:s"),
                    "group" => 1
                ));

                Session::flash("home", "You have been registered <br>");
                Redirect::to("index.php");

            }
            catch(Exception $e){
                die($e->getMessage());
            }

        }
        else{
            foreach($validate->errors() as $var){
                echo $var."<br>";
            }
        }
    }
}

?>

<form method="post" action="">

    <label for="name"> Name </label>
    <input name="Name" type="text" value="<?php echo escape(Input::get('name')) ?>" id="name"> <br>

    <label for="username"> Username </label>
    <input name="Username" type="text" value="<?php echo escape(Input::get('username')) ?>" id="username"> <br>

    <label for="password"> Password </label>
    <input name="Password" type="password" id="password"> <br>

    <label for="again"> Again password </label>
    <input name="pass_again" type="password" id="again"> <br>

        <input type="hidden" name="token" value="<?php echo Token::generate() ?>">
    <input type="submit" value="Register">

</form>

