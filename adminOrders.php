<?php
        namespace gazelleRunningSupplies;

use DateTime;
use mysqli;



        

        class dbConnect
        {       

            public object $sqlConnection;
            var $allProducts;
            var $basket;

            var $allOrders;

            var $displayedOrder;

            function __construct()
            {
                $sqlServer = "localhost";
                $database = "gazellerunningsupplies";
                $this->allProducts = array();
                $this->allOrders = array();
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

            function retrieveFilteredOrders()
            {
                $dateFrom = $_SESSION["dateFrom"];
                $dateTo = $_SESSION["dateTo"];
                $sqlComm = "SELECT * FROM tblOrder WHERE orderDate BETWEEN '$dateFrom' AND '$dateTo' ORDER BY orderDate DESC";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                if($sqlReturn->num_rows > 0)
                {
                    while($row = $sqlReturn->fetch_assoc())
                    {
                        $item = new Order($row["orderID"], $row["orderDate"], $row["customerID"]); 
                        $this->GetOrderDetail($item);                                                                        
                        $this->allOrders[] = $item;                
                    }
                }  
            }

            function getAllProducts()
            {
                return $this->allProducts;
            }

            function getAllOrders()
            {
                return $this->allOrders;
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
                $dateCreated = date("Y-m-d");
                $sqlComm = "INSERT INTO tblBasket (dateCreated) VALUES ('$dateCreated')";
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

                $sqlComm = "SELECT * FROM tblBasketItem WHERE basketID='$basketID'";
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
                    $sqlComm = "INSERT INTO tblBasketItem (productID, quantity, basketID) VALUES ('$productID', 1, '$basketID')";
               
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

                $sqlComm = "SELECT * FROM tblBasketItem WHERE basketID='$basketID'";
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

            function getOrderTotal(Order $order)
            {
                $total = 0;

                if($order->getAllItems() != null)
                {
                    foreach($order->getAllItems() as $orderItem)
                    {
                        $total += $orderItem->getProduct()->getPrice()*$orderItem->getQuantity();
                    }
                }

                return number_format($total, 2);
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

            
                function getCustomer(string $request, int $customerID)
                {
                    
                    $customer = null;

                    switch($request)
                    {
                        case "nameOnly":
                            $sqlComm = "SELECT customerName FROM tblcustomer WHERE customerID='$customerID'";
                            $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                            if($sqlReturn->num_rows > 0)
                            {
                                while($row = $sqlReturn->fetch_assoc())
                                {
                                    $customer = $row["customerName"];               
                                }
                            }  
                            break;  
                        case "fullDetail":
                            $sqlComm = "SELECT * FROM tblcustomer WHERE customerID='$customerID'";
                            $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                            if($sqlReturn->num_rows > 0)
                            {
                                while($row = $sqlReturn->fetch_assoc())
                                {
                                    $customer = new Customer($customerID, $row["customerName"], $row["addressLine1"], $row["addressLine2"], $row["city"], $row["county"], $row["postcode"], $row["contactNumber"], $row["emailAddress"]);               
                                }
                            }
                            break; 
                    }

                    return $customer;
                }

            

            function checkAuthAndPrintLoggedInUser()
            {
                if(isset($_SESSION["userID"]))
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
                else
                {
                    header("Location: adminLogin.php");
                }   
                
            }

            function GetOrderDetail(Order $order)
            {
                $allDetail = array();
                $orderID = $order->getID();
                $sqlComm = "SELECT * FROM tblOrderDetail WHERE orderID='$orderID'";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                if($sqlReturn->num_rows > 0)
                {
                    while($row = $sqlReturn->fetch_assoc())
                    {
                        $product = $this->getProduct($row["productID"]);
                        $order->addDetail(new orderDetail($row["orderDetailID"], $product, $row["quantity"]));          
                    }
                }  
                
            }

            function setDisplayedOrder(Order $order)
            {
                $this->displayedOrder = $order;
            }

            function getDisplayedOrder()
            {
               return $this->displayedOrder;
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

            function getName()
            {
                return $this->customerName;
            }

            function getAddressLine1()
            {
                return $this->addressLine1;
            }

            function getAddressLine2()
            {
                return $this->addressLine2;
            }

            function getCity()
            {
                return $this->city;
            }

            function getCounty()
            {
                return $this->county;
            }

            function getPostcode()
            {
                return $this->postcode;
            }

            function getPhone()
            {
                return $this->contactNumber;
            }
            
            function getEmailAddress()
            {
                return $this->emailAddress;
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

            private string $orderDate;

            private int $customerID;

            function __construct(int $orderID, string $orderDate, int $customerID)
            {
                $this->orderID = $orderID;
                $this->orderDate = $orderDate;
                $this->customerID = $customerID;
                $this->orderItems = array();
            }

            function getID()
            {
                return $this->orderID;
            }           

            function getDate()
            {
                return $this->orderDate;
            }

            function getCustomerID()
            {
                return $this->customerID;
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

        
        session_start();
        $dbConnect = new dbConnect;
        $dbConnect->retrieveAllProducts();   
                
       
        if(!isset($_SESSION["dateFrom"]))
        {
            $lastWeek = strtotime("-1 week");
            $_SESSION["dateFrom"] = date("Y-m-d", $lastWeek);
        }
        else if(!isset($_POST["orderChange"]) and isset($_POST["dateFrom"]))
        {
            $_SESSION["dateFrom"] = $_POST["dateFrom"];
        }

        if(!isset($_SESSION["dateTo"]))
        {
            
            $_SESSION["dateTo"] = date("Y-m-d");
        }
        else if(!isset($_POST["orderChange"]) and isset($_POST["dateTo"]))
        {
            $_SESSION["dateTo"] = $_POST["dateTo"];
        }
        
        $dbConnect->retrieveFilteredOrders();

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
            <?php $dbConnect->checkAuthAndPrintLoggedInUser() ?>
        </div>
        <div id="adminLogin">
            <a href="adminLogin.php?action=logout">Logout</a>
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
                <span id="dateRange">
                    <p>Filter orders by date</p>   
                    <form method="post">              
                        <label for="dateFrom">From:</label><input type="date" name="dateFrom" value="<?php echo $_SESSION["dateFrom"] ?>"/>
                        <label for="dateTo">To:</label><input type="date" name="dateTo" value="<?php echo $_SESSION["dateTo"] ?>"/>
                        <input type="submit" value="Click to filter"/>
                    </form>
                </span>
                <table id="ordersTable">
                    <tr>
                        <th>Order Number</th>
                        <th>Order Date</th>
                        <th>Customer Name</th>
                        <th>Order Total</th>
                        <th>View Detail</th>
                    </tr>
                        <?php
                            if($dbConnect->getAllOrders() != null)
                            {
                                $loopCount = 1;
                                $viewDetail = null;

                                foreach($dbConnect->getAllOrders() as $order)
                                {
                                    
                                    if($loopCount == $_GET["order"])
                                        {
                                            $viewDetail = "Selected. See Below";
                                            $dbConnect->setDisplayedOrder($order);                                            
                                        }
                                        else
                                        {
                                            $viewDetail = '<form method="post" action="adminOrders.php?order='.($loopCount).'">
                                            <input type="hidden" name="orderChange" value="true">
                                            <input type="submit" value="View Details">
                                            </form>';
                                        }
                                    $customerID = $order->getCustomerID();
                                    echo '<tr>
                                    <td>'.$order->getID().'</td>
                                    <td>'.$order->getDate().'</td>
                                    <td>'.$dbConnect->getCustomer("nameOnly", $customerID).'</td>
                                    <td>'.$dbConnect->GetOrderTotal($order).'</td>
                                    <td>'.$viewDetail.'</td>
                                    </tr>';

                                    $loopCount++;
                                }
                            }
                        ?>    
                </table>
            </div>            
        </div>

        

        <div id="orderDetailDiv" <?php if($dbConnect->getDisplayedOrder() == null){echo 'class="hidden"';} ?>>
            <h2>Order detail</h2>
            <div id="orderDetail">
                <span id="orderNumberSpan">
                    <strong>Order Number:</strong>
                    <p id="orderDetailNumber"><?php echo $dbConnect->getDisplayedOrder()->getID(); ?></p>
                </span>
                <div id="customerDetailsDiv">
                    <strong>Customer Details</strong>
                    <?php 
                        $customer = $dbConnect->getCustomer("fullDetail", $dbConnect->getDisplayedOrder()->getCustomerID());
                    echo '<p id="orderDetailCustomerName">'.$customer->getName().'</p>
                    <p id="orderDetailAddress">'.$customer->getAddressLine1().'<br />'.$customer->getAddressLine2().'<br />'.$customer->getCity().'<br />'.$customer->getCounty().'<br />'.$customer->getPostcode().'</p>
                    <p id="orderDetailEmail">'.$customer->getEmailAddress().'</p>
                    <p id="orderDetailContact">'.sprintf("%011s",$customer->getPhone()).'</p>';
                    ?>
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
                            <?php
                                foreach($dbConnect->getDisplayedOrder()->getAllItems() as $orderDetail)
                                    {
                                        echo '<tr>
                                            <td>'.$orderDetail->getProduct()->getName().'</td>
                                            <td>'.$orderDetail->getQuantity().'</td>
                                            <td>'.number_format($orderDetail->getProduct()->getPrice()*$orderDetail->getQuantity(), 2).'</td>
                                            </tr>';
                                    }
                            ?>
                        </table>
                    </div>
                </div>
                <span id="orderTotalSpan">
                    <strong>Order Total:</strong>
                    <p id="orderDetailTotal">&pound;<?php echo $dbConnect->GetOrderTotal($dbConnect->getDisplayedOrder()) ?></p>
                </span>
            </div>
        </div>        
    </div>


    <footer>
        <p>&copy; Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>