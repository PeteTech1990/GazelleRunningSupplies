<?php
        namespace gazelleRunningSupplies;
        use mysqli;
        

        

        class DBConnect
        {       

            public object $sqlConnection;

            function __construct()
            {
                $sqlServer = "localhost";
                $database = "gazellerunningsupplies";
                $this->allProducts = array();

                $this->sqlConnection = new mysqli($sqlServer, "sa", "sa", $database);
                
            }

            function PrintLoginFailMessage()
            {
                if(isset($_SESSION["failLoginMessage"]))
                {
                    echo '<p id="loginFailMessage">'.$_SESSION["failLoginMessage"].'</p>';
                }
            }
        }

        
        session_start();
        $dbConnect = new DBConnect;        
        
        if(isset($_GET["action"]))
        {
            session_destroy();
            session_start();
        }
        

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gazelle Running Supplies - Order Form</title>
    <link rel="stylesheet" href="styles.css" />
    
</head>
<body>

    <div id="navBar">
        <div id="adminUserName">

        </div>
        <div id="adminLogin">
            <a href="index.php">Cancel</a>
        </div>
    </div>


    <img id="logoImage" src="gazelleLogo.jpeg" />
    <h1 id="header">Admin Login</h1>
    

    <div id="mainContent">        
        
        <div id="userDetails">
            <?php $dbConnect->PrintLoginFailMessage() ?>
            <h2>Enter your username and password</h2>
            <form id="userDetailsForm" method="post" action="loginCheck.php">
                <span class="inputAreasLogin" id="usernameInput"><label class="loginLabel" for="txtUsername">Username:</label><input name="txtUsername" required maxlength="20"/></span>
                <span class="inputAreasLogin" id="passwordInput"><label class="loginLabel" id="passwordLabel" for="txtPassword">Password:</label><input type="password" name="txtPassword" required maxlength="20"/></span>                
                <input type="submit" name="Login" value="Login"  class="uiButton"/>
            </form>
            
        </div>
        
        
    </div>


    <footer>
        <p>&copy; Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>