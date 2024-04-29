<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gazelle Running Supplies - Admin - Stock</title>
    <link rel="stylesheet" href="styles.css" />
    
</head>
<body>

    <div id="navBar">
        <div id="adminUserName">
            <p>Username is logged in</p>
        </div>
        <div id="adminLogin">
            <a href="adminLogin.php">Logout</a>
        </div>
    </div>


    <img id="logoImage" src="gazelleLogo.jpeg" />
    <h1 id="header">Admin - Stock</h1>
    

    <div id="mainContent">  
        <div id="adminTabs">
            <a href="adminOrders.php"><h2>Orders</h2></a>
            <h2 id="stockLink">Stock</h2>
        </div>
        <div id="stockBreakdown">
            <h2>All Current Stock</h2>
            <div id="stockTableDiv">
                <table id="stockTable">
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Current Quantity Ordered</th>
                        <th>Current Stock Available</th>
                    </tr>
                    <tr>
                        <td>Hello</td>
                        <td>Hello</td>
                        <td>Hello</td>
                        <td>Hello</td>
                    </tr>
                </table>
            </div>            
        </div>
                
    </div>


    <footer>
        <p>Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>