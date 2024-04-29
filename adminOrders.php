<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gazelle Running Supplies - Admin - Orders</title>
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
    <h1 id="header">Admin - Orders</h1>
    

    <div id="mainContent">  
        <div id="adminTabs">
            <h2 id="ordersLink">Orders</h2>
            <a href="adminStock.php"><h2>Stock</h2></a>
        </div>
        <div id="orderBreakdown">
            <h2>All Current Orders</h2>
            <div id="ordersTableDiv">
                <table id="ordersTable">
                    <tr>
                        <th>Order Number</th>
                        <th>Order Date</th>
                        <th>Customer Name</th>
                        <th>Order Total</th>
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
        <div id="orderDetailDiv">
            <h2>Order detail</h2>
            <div id="orderDetail">
                <span id="orderNumberSpan">
                    <strong>Order Number:</strong>
                    <p id="orderDetailNumber">33322332</p>
                </span>
                <div id="customerDetailsDiv">
                    <strong>Customer Details</strong>
                    <p id="orderDetailCustomerName">asdaasdadas</p>
                    <p id="orderDetailAddress">asda<br />asdasd<br />sdsad<br />asdfas<br />ll2345d</p>
                    <p id="orderDetailEmail">asdasdasdasd</p>
                    <p id="orderDetailContact">1234235234</p>
                </div>
                <div id="orderDetailsDiv">
                    <strong>Orders Details</strong>
                    <div id="orderDetailsTableDiv">
                        <table id="orderDetailTable">
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Item total</th>
                            </tr>
                            <tr>
                                <td>Hello</td>
                                <td>Hello</td>
                                <td>Hello</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <span id="orderTotalSpan">
                    <strong>Order Total:</strong>
                    <p id="orderDetailTotal">12312</p>
                </span>
            </div>
        </div>        
    </div>


    <footer>
        <p>Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>