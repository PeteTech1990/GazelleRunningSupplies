<?php
        namespace gazelleRunningSupplies;

use DateTime;
use mysqli;

session_start();

        

        class dbConnect
        {       

            public object $sqlConnection;
            var $allProducts;
            var $basket;

            var $customer;

            var $order;

            function __construct()
            {
                $sqlServer = "localhost";
                $database = "gazellerunningsupplies";
                $this->allProducts = array();

                $this->sqlConnection = new mysqli($sqlServer, "sa", "sa", $database);
                
            }

            function retrieveAllProducts()
            {
                $sqlComm = "SELECT * FROM tblProduct";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                if($sqlReturn->num_rows > 0)
                {
                    while($row = $sqlReturn->fetch_assoc())
                    {
                        $item = new Product($row["productID"], $row["productName"], $row["price"], $row["stock"], $row["category"], $row["description"]);                                                                         
                        $this->allProducts[] = $item;                
                    }
                }  
            }

            function getAllProducts()
            {
                return $this->allProducts;
            }

            function getProduct(int $productID)
            {
                foreach($this->allProducts as $product)
                {
                    if($product->getID() == $productID)
                    {
                        return $product;
                    }
                }
            }

            function createBasket()
            {
                $dateCreated = date("Y/m/d");
                $sqlComm = "INSERT INTO tblBasket (dateCreated) VALUES ('.$dateCreated.')";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                $basketID = mysqli_insert_id($this->sqlConnection);

                return $basketID;
            }
                
            function addToBasket(int $productID)
            {
                $basketID = $_SESSION["basketID"];
                $productExist = false;
                $currentQuantity = 0;
                $currentBasketItemID = 0;

                $sqlComm = "SELECT * FROM tblBasketItem WHERE basketID='.$basketID.'";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                if($sqlReturn->num_rows > 0)
                {
                    while($row = $sqlReturn->fetch_assoc())
                    {
                        if($row["productID"] == $productID)
                        {
                            $productExist = true;
                            $currentQuantity = (int)$row["quantity"];
                            $currentBasketItemID = $row["basketItemID"];
                        }              
                    }
                }                

                if($productExist)
                {
                    $newQuantity = ($currentQuantity + 1);

                    $sqlComm = "UPDATE tblBasketItem SET quantity='$newQuantity' WHERE basketItemID='$currentBasketItemID'";
                    $this->sqlConnection->query($sqlComm);
                    
                    
                }
                else
                {
                    $sqlComm = "INSERT INTO tblBasketItem (productID, quantity, basketID) VALUES ('.$productID.', 1, '.$basketID.')";
               
                    $this->sqlConnection->query($sqlComm);

                }
 
            }

            function removeFromBasket(int $basketItemID)
            {
                $sqlComm = "DELETE FROM tblBasketItem WHERE basketItemID='$basketItemID'";
                
                $this->sqlConnection->query($sqlComm);
            }

            function updateBasket(int $basketItemID, int $quantity)
            {
                if($quantity < 1)
                {
                    $this->removeFromBasket($basketItemID);
                }
                else
                {
                    $sqlComm = "UPDATE tblBasketItem SET quantity='$quantity' WHERE basketItemID='$basketItemID'";
                
                    $this->sqlConnection->query($sqlComm);
                }
            }

            function destroyBasket()
            {
                $basketID = $_SESSION["basketID"];
                $sqlComm = "DELETE FROM tblBasketItem WHERE basketID='$basketID'";
                
                $this->sqlConnection->query($sqlComm);
                $sqlComm = "DELETE FROM tblBasket WHERE basketID='$basketID'";
                
                $this->sqlConnection->query($sqlComm);
            }

            function InstantiateAndPopulateBasket()
            {
                $basketID = $_SESSION["basketID"];
                $this->basket = new Basket($basketID);

                $sqlComm = "SELECT * FROM tblBasketItem WHERE basketID='.$basketID.'";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                if($sqlReturn->num_rows > 0)
                {
                    while($row = $sqlReturn->fetch_assoc())
                    {
                        $product = $this->getProduct($row["productID"]);
                        $newBasketItem = new basketItem($row["basketItemID"],$product , $row["quantity"]);
                        $this->basket->addProductToBasket($newBasketItem);               
                    }
                }                
                
            }

            function getBasketTotal()
            {
                $total = 0;

                if($this->basket->getAllItems() != null)
                {
                    foreach($this->basket->getAllItems() as $basketItem)
                    {
                        $total += $basketItem->getProduct()->getPrice()*$basketItem->getQuantity();
                    }
                }

                echo '<h2>&pound;'.number_format($total, 2).'</h2>';
            }

            function getOrderTotal()
            {
                $total = 0;

                if($this->order->getAllItems() != null)
                {
                    foreach($this->order->getAllItems() as $orderItem)
                    {
                        $total += $orderItem->getProduct()->getPrice()*$orderItem->getQuantity();
                    }
                }

                echo '<h2>&pound;'.number_format($total, 2).'</h2>';
            }

            function getBasket()
            {
                return $this->basket;
            }

            function createCustomer()
            {
                $customerName = $_POST["txtFullName"];
                $customerAdd1 = $_POST["txtAddress1"];
                $customerAdd2 = $_POST["txtAddress2"];
                $customerCity = $_POST["txtCity"];
                $customerCounty = $_POST["txtCounty"];
                $customerPostcode = $_POST["txtPostcode"];
                $customerNumber = $_POST["txtPhone"];
                $customerEmail = $_POST["txtEmail"];

                $insertString = "'$customerName', '$customerAdd1', '$customerAdd2', '$customerCity', '$customerCounty', '$customerPostcode', '$customerNumber', '$customerEmail'";
                
                $sqlComm = "INSERT INTO tblCustomer (customerName, addressLine1, addressLine2, city, county, postcode, contactNumber, emailAddress) VALUES ($insertString)";
               
                $this->sqlConnection->query($sqlComm);

                $customerID = mysqli_insert_id($this->sqlConnection);

                $this->customer = new Customer($customerID, $customerName, $customerAdd1, $customerAdd2, $customerCity, $customerCounty, $customerPostcode, $customerNumber, $customerEmail);
            }

            function createOrder()
            {
                $dateCreated =  strtotime("Y/m/d");
                $customerID = $this->customer->getID();
                $sqlComm = "INSERT INTO tblOrder (orderDate, customerID) VALUES ('.$dateCreated.', '.$customerID.')";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                $orderID = mysqli_insert_id($this->sqlConnection);

                $this->order = new Order($orderID, $dateCreated);

                if($this->basket->getAllItems() != null)
                {
                    foreach($this->basket->getAllItems() as $basketItem)
                    {
                        $productID = $basketItem->GetProduct()->GetID();
                        $quantity = $basketItem->GetQuantity();

                        $sqlComm = "INSERT INTO tblOrderDetail (productID, quantity, orderID) VALUES ('$productID', '$quantity', '.$orderID.')";
                        $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                        $orderDetailID = mysqli_insert_id($this->sqlConnection);

                        $this->order->addDetail(new orderDetail($orderDetailID, $basketItem->getProduct(), $basketItem->getQuantity()));
                    }
                }

            }

            function printLoggedInUser()
            {
                $userID = $_SESSION["userID"];
                $sqlComm = "SELECT displayName FROM tblUser WHERE userID='$userID'";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                if($sqlReturn->num_rows > 0)
                {
                    while($row = $sqlReturn->fetch_assoc())
                    {
                        echo '<p>'.$row["displayName"].' is logged in</p>';             
                    }
                }
                
            }
        }

        class Customer
        {
            private int $customerID;

            private string $customerName;

            private string $addressLine1;

            private string $addressLine2;

            private string $city;

            private string $county;

            private string $postcode;

            private int $contactNumber;

            private string $emailAddress;

            function __construct(int $ID, string $name, string $add1, string $add2, string $city, string $county, string $postcode, int $contactNo, string $email)
            {
                $this->customerID = $ID;
                $this->customerName = $name;
                $this->addressLine1 = $add1;
                $this->addressLine2 = $add2;
                $this->city = $city;
                $this->county = $county;
                $this->postcode = $postcode;
                $this->contactNumber = $contactNo;
                $this->emailAddress = $email;
            }  

            function getID()
            {
                return $this->customerID;
            }

            function printDetails()
            {
                echo '<p id="fullName">'.$this->customerName.'</p>
                <p id="addressFull" class="customerDetailsInvLabel">'.$this->addressLine1.' '.$this->addressLine2.' <br /> '.$this->city.' <br /> '.$this->county.' <br /> '.$this->postcode.'</p>
                <p id="emailAddress" class="customerDetailsInvLabel">'.$this->emailAddress.'</p>
                <p id="contactNumber" class="customerDetailsInvLabel">'.$this->contactNumber.'</p>';
            }
        }

         class Product
        { 
            private int $productID;
            private string $productName;
            private string $productImagePath;
            private float $price;
            private int $stock;
            private string $category;

            private string $description;

            function __construct(int $productID, string $productName, float $price, int $stock, string $category, string $description)
            {
                $this->productID = $productID;
                $this->productName = $productName;
                $this->price = $price;
                $this->stock = $stock;
                $this->category = $category;
                $this->description = $description;
                $this->addProductImagePath("/productImages/" . $this->productID);
            }

            function addProductImagePath(string $image)
            {
                $this->productImagePath = $image;
            }

            function adjustPrice(float $amount)
            {
                $this->price = $amount;
            }

            function getID()
            {
                return $this->productID;
            }

            function getName()
            {
                return $this->productName;
            }

            function getImagePath()
            {
                return $this->productImagePath;
            }

            function getPrice()
            {
                return $this->price;
            }

            function getStock()
            {
                return $this->stock;
            }

            function getCategory()
            {
                return $this->category;
            }

            function getSpan()
            {
                echo '<span id=productDetails'.$this->productID.' class="productSpan" >
                        <img onclick="openProductModal('.$this->productID.')" id=productImage'.$this->productID.' class="productImage" src="'.$this->productImagePath.'.jpeg"/>
                        <span class="productDetails">
                            <p id="productName">' . $this->productName . '</p>
                            <p id="productPrice">&pound;' . number_format($this->price, 2) . '</p> 
                        </span> 
                        <form method="post" action="index.php?action=addToBasket">
                            <input type="hidden" name="productID" value="'.$this->productID.'"/>
                            <input type="submit" class="uiButton" value="Add to Basket"/>
                        </form>
                    </span>';
            }

            function getDetailDiv()
            {
                echo '<div id='.$this->productID.' class="modal modalProductDetails">
                <div class="modal-content-product">
                <span onclick="closeProductModal('.$this->productID.')" id="modalClose'.$this->productID.'" class="close">&times;</span>
                <img class="productImageModal" src="'.$this->productImagePath.'.jpeg"/>
                <div class="modalInner">
                    <h2>'. $this->productName .'</h2>
                    <p>'. $this->description .'</p>
                    <p>&pound;'. number_format($this->price, 2) .'</p>
                </div>
                <form method="post" action="index.php?action=addToBasket">
                            <input type="hidden" name="productID" value="'.$this->productID.'"/>
                            <input type="submit" class="uiButton" value="Add to Basket"/>
                        </form>
                </div>
                </div>';
            }
        }
     
        class Order
        {
            private $orderItems;

            private int $orderID;

            private int $orderDate;

            function __construct(int $orderID, int $orderDate)
            {
                $this->orderID = $orderID;
                $this->orderDate = $orderDate;
                $this->orderItems = array();
            }

            public function getID()
            {
                return $this->orderID;
            }

            public function addDetail(orderDetail $newItem)
            {
                $this->orderItems[] = $newItem;
            }

            public function removeProductFromBasket(int $itemID)
            {
                $this->orderItems[$itemID] = null;
            }

            public function getAllItems()
            {
                return $this->orderItems;
            }

            function printOrderNumber()
            {
                echo '<h2 id="invoiceNumber">'.$this->orderID.'</h2>';
            }
        }

        class orderDetail
        {
            private int $orderItemID;

            private Product $product;

            private int $quantity;

            function __construct(int $ID, Product $product, int $amount)
            {
                $this->orderItemID = $ID;
                $this->product = $product;
                $this->quantity = $amount;
            }

            function getProduct()
            {
                return $this->product;
            }

            function getID()
            {
                return $this->orderItemID;
            }

            function getQuantity()
            {
                return $this->quantity;
            }
            
        }

        class Basket
        {
            private $basketItems;

            private int $basketID;

            function __construct(int $basketID)
            {
                $this->basketID = $basketID;
                $this->basketItems = array();
            }

            public function getID()
            {
                return $this->basketID;
            }

            public function addProductToBasket(basketItem $newItem)
            {
                $this->basketItems[] = $newItem;
            }

            public function removeProductFromBasket(int $itemID)
            {
                $this->basketItems[$itemID] = null;
            }

            public function getAllItems()
            {
                return $this->basketItems;
            }
        }

        class basketItem
        {
            private int $basketItemID;

            private Product $product;

            private int $quantity;

            function __construct(int $ID, Product $product, int $amount)
            {
                $this->basketItemID = $ID;
                $this->product = $product;
                $this->quantity = $amount;
            }

            function getProduct()
            {
                return $this->product;
            }

            function getID()
            {
                return $this->basketItemID;
            }

            function getQuantity()
            {
                return $this->quantity;
            }

            function getDiv()
            {

                echo '<div class="basketItem" >
                        <p class="basketProductName">'.$this->product->getName().'</p>
                        <form method="post" action="index.php?action=removeFromBasket">
                            <input type="hidden" name="basketItemID" value="'.$this->basketItemID.'"/>
                            <input type="submit" class="uiBasketButton" value="Remove"/>
                        </form>
                        <p class="basketProductPrice">&pound;'.number_format($this->product->getPrice(), 2).' each</p>
                        <form method="post" action="index.php?action=changeBasketQuantity">
                            <input type="hidden" name="basketItemID" value="'.$this->basketItemID.'"/>
                            <input type="number" class="quantitySelector" name="quantity" value="'.$this->quantity.'"/>
                            <input type="submit" class="uiBasketButton" value="Update"/>
                        </form>
                        <span class="basketItemTotal">
                            <p>Total:</p>
                            <p >&pound;'.number_format(($this->product->getPrice()*$this->quantity), 2).'</p>
                        </span>
                    </div>';
            }
        }
        
        $dbConnect = new dbConnect;
        $dbConnect->retrieveAllProducts();           
       
?>

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
            <?php $dbConnect->printLoggedInUser() ?>
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