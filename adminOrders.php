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
            <p>Username is logged in</p>
        </div>
        <div id="adminLogin">
            <a href="adminLogin.php">Admin Login</a>
        </div>
    </div>


    <img id="logoImage" src="gazelleLogo.jpeg" />
    <h1 id="header">Admin - Orders</h1>
    

    <div id="mainContent">  
        <div id="adminTabs">
            <h2 id="ordersLink">Orders</h2>
            <a href="adminStock.php"><h2 id="stockLink">Stock</h2></a>
        </div>
        <div id="orderBreakdown">
            <h2>All Current Orders</h2>
            <div id="ordersTableDiv">
                <table id="ordersTable">
                    <tr>
                        <th>Hello</th>
                        <th>Hello</th>
                        <th>Hello</th>
                    </tr>
                    <tr>
                        <td>Hello</td>
                        <td>Hello</td>
                        <td>Hello</td>
                    </tr>
                </table>
            </div>            
        </div>
        <div id="orderDetailDiv">
            <h2>Order detail</h2>
            <div id="orderDetail">
                <span id="orderNumberSpan"></span>
                <div id="customerDetailsDiv"></div>
                <div id="orderDetailsDiv">
                    <div id="orderDetailsTableDiv"></div>
                </div>
                <span id="orderTotalSpan"></span>
            </div>
        </div>        
    </div>


    <footer>
        <p>Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>