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
            <h2>Enter your username and password</h2>
            <form id="userDetailsForm" method="post" action="adminOrders.php">
                <span class="inputAreasLogin" id="usernameInput"><label for="txtUsername">Username: </label><input id="txtUsername" required/></span>
                <span class="inputAreasLogin" id="passwordInput"><label for="txtPassword">Password:   </label><input id="txtPassword" required/></span>                
                <input type="submit" name="Login"  class="uiButton"/>
            </form>
            
        </div>
        
        
    </div>


    <footer>
        <p>Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>