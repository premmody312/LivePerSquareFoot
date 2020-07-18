<?php

// Provide Log In/Out functionality.

require_once 'header.php';

//check if the user clicked the logout link and set the logout GET parameter
if(isset($_GET['logout']) && $_GET['logout'] == 1){
    //Destroy the user's session.
    unset($_SESSION['user_id'], $_SESSION['is_admin'], $_SESSION['isloggedIn']);

    session_unset();
    session_destroy();
}

require_once 'navbartransparent.php';

?>


<?php

//check if the user is already logged in and has an active session
if(userLoggedIn()){
    //Redirect the browser to the profile page and kill this page.
    header("Location: profile.php");
    die();
}


//check if the login form has been submitted
if(isset($_POST['loginBtn'])){

        // SELECT query
        $query = "SELECT user_id, email, password, is_admin FROM users WHERE email=? AND password=?";

        // prepare query for execution
        if($stmt = $con->prepare($query)){

            $stmt->bind_Param("ss", $_POST['email'], $_POST['password']);
            $stmt->execute();
            $result = $stmt->get_result();

            // Get the number of rows returned
            $num = $result->num_rows;

            if($num>0){
                //If the email/password matches a user in our database
                //Read the user details
                $row = $result->fetch_assoc();

                //Create a session variable that holds the user's user_id
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['isloggedIn'] = true;
                $row['is_admin'] ? $_SESSION['is_admin'] = true: $_SESSION['is_admin'] = false;



                //Redirect the browser to the profile editing page and kill this page.
                header("Location: profile.php");
                die();
            } else {
                //If the email/password doesn't match a user in our database
                // Display an error message and the login form
                echo '<br><p class="alert alert-danger text-center col-md-4 col-md-offset-4">Failed to login. Wrong Email or Password</p>';
            }
        }
        else {
            echo "Failed to prepare the SQL";
        }
 }

?>



<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" href="css/lp.css">
        <style></style>
    </head>

    <body>

        <div class="container col-md-4 col-md-offset-4">
        <form action="index.php" method="post">
                <h2 class = "form-signin-heading text-center" style="color:#fff"> Welcome to LivePerSquareFoot<br>Please Log In or Register</h2>
                <hr class = "colorgraph"> <br>

                <!-- Login Form Email Address -->
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-envelope"></span>
                    </span>
                    <input type="text"  class="form-control" name="email" placeholder="Email" required="" autofocus=""/>
                </div>

                <br>

                <!-- Login Form Password -->
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-lock"></span>
                    </span>
                <input type="password" class="form-control" name="password" placeholder = "Password" required=""/>
                </div>

                <br>
                <input type="submit" class="btn btn-lg btn-primary btn-block" name="loginBtn" value="Log In" />
        </form>

            <hr class = "colorgraph">
            <a type="button" class="btn btn-lg btn-primary btn-block" href="signup.php">Register</a>
        </div>


    </body>
</html>

