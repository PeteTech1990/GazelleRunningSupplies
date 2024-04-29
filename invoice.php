<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gazelle Running Supplies - Invoice</title>
    <link rel="stylesheet" href="styles.css" />
    
</head>
<body>

    <div id="navBar">
        <div id="adminUserName">

        </div>
        <div id="adminLogin">
            <a href="adminLogin.php">Admin Login</a>
        </div>
    </div>


    <img id="logoImage" src="gazelleLogo.jpeg" />
    <h1 id="header">Invoice</h1>
    

    <div id="mainContent">        
        <div id="invoiceDetails">
            <div id="invoiceNumberLabels">
               <h2>Invoice Number: </h2>
               <h2 id="invoiceNumber">234</h2>
            </div>
        
            <div id="customerDetailsInv">
            <h2>Customer Details</h2>
                <p id="fullName">Peter</p>
                <p id="addressFull" class="customerDetailsInvLabel">29 Pendas Park <br /> penley</p>
                <p id="emailAddress" class="customerDetailsInvLabel">me@me</p>
                <p id="contactNumber" class="customerDetailsInvLabel">123</p>
            </div>

            <div id="orderBreakdownInv">
                <h2>Order Breakdown</h2>
                <table id="orderBreakdownTable">
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
            <div id="orderTotals">
                <h2>Order Total: </h2>
                <h2 id="orderTotal">£0.00</h2>
            </div>
            <div id="customerActionsInv">
                <button class="uiButton">Print Invoice</button>
                <button class="uiButton">Close Invoice</button>
            </div>
        </div>
    </div>


    <footer>
        <p>Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>