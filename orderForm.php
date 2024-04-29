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
            <a href="adminLogin.php">Admin Login</a>
        </div>
    </div>


    <img id="logoImage" src="gazelleLogo.jpeg" />
    <h1 id="header">Order Form</h1>
    

    <div id="mainContent">        
        <div id="orderBreakdown">
            <h2>Order Breakdown</h2>
            <div id="orderItems">
                
            </div>
            <span id="orderTotalLabels">
                <h2>Order Total:</h2>
                <h2 id="orderTotal"></h2>
            </span>
        </div>
        <div id="customerDetails">
            <h2>Customer Details</h2>
            <form id="detailsForm">
                <span class="inputAreas" id="customerNameInput"><label for="txtFullName">Full Name: </label><input id="txtFullName" required/></span>
                <span class="inputAreas" id="address1Input"><label for="txtAddress1">Address Line 1: </label><input id="txtAddress1" required/></span>
                <span class="inputAreas" id="address2Input"><label for="txtAddress2">Address Line 2: </label><input id="txtAddress2"/></span>
                <span class="inputAreas" id="cityInput"><label for="txtCity">City: </label><input id="txtCity" required/></span>
                <span class="inputAreas" id="countyInput"><label for="txtCounty">County: </label><input id="txtCounty" required/></span>
                <span class="inputAreas" id="postcodeInput"><label for="txtPostcode">Postcode: </label><input id="txtPostcode" required/></span>
                <span class="inputAreas" id="phoneInput"><label for="txtPhone">Contact Number: </label><input id="txtPhone" required/></span>
                <span class="inputAreas" id="emailInput"><label for="txtEmail">Email Address: </label><input id="txtEmail" required/></span>
            </form>
        </div>
        <div id="customerActions">
            <button class="uiButton" onclick="launchInvoice()">Place Order</button>
            <button class="uiButton">Amend Order</button>
            <button class="uiButton">Cancel Order</button>
        </div>
    </div>


    <footer>
        <p>Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>