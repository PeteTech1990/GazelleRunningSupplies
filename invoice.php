<?php
namespace gazelleRunningSupplies;

use DateTime;
use mysqli;

        

        class DBConnect
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

            function GetCustomer()
            {
                return $this->customer;
            }

            function GetOrder()
            {
                return $this->order;
            }

            function RetrieveAllProducts()
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

            function GetAllProducts()
            {
                return $this->allProducts;
            }

            function GetProduct(int $productID)
            {
                foreach($this->allProducts as $product)
                {
                    if($product->GetID() == $productID)
                    {
                        return $product;
                    }
                }
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
                        $product = $this->GetProduct($row["productID"]);
                        $newBasketItem = new basketItem($row["basketItemID"],$product , $row["quantity"]);
                        $this->basket->AddProductToBasket($newBasketItem);               
                    }
                }                
                
            }

            function GetOrderTotal()
            {
                $total = 0;

                if($this->order->GetAllItems() != null)
                {
                    foreach($this->order->GetAllItems() as $orderItem)
                    {
                        $total += $orderItem->GetProduct()->GetPrice()*$orderItem->GetQuantity();
                    }
                }

                echo '<h2>&pound;'.number_format($total, 2).'</h2>';
            }

            
            function createCustomer()
            {
                while(true)
                {
                   
                    if(trim($_POST["txtFullName"]) == ""){header("Location:orderForm.php?invalid=blankName");break;}
                    else{$customerName = trim($_POST["txtFullName"]);}
                    if(trim($_POST["txtAddress1"]) == ""){header("Location:orderForm.php?invalid=blankAddr");break;}
                    else{$customerAdd1 = $_POST["txtAddress1"];}
                    if(trim($_POST["txtAddress2"]) == ""){header("Location:orderForm.php?invalid=blankAddr");break;}
                    else{$customerAdd2 = $_POST["txtAddress2"];}
                    if(trim($_POST["txtCity"]) == ""){header("Location:orderForm.php?invalid=blankCity");break;}
                    else{$customerCity = $_POST["txtCity"];}
                    if(trim($_POST["txtCounty"]) == ""){header("Location:orderForm.php?invalid=blankCounty");break;}
                    else{$customerCounty = $_POST["txtCounty"];}

                    //Source of postcode validation RegEx pattern: (Angeles, 2022) 
                    
                    if(trim($_POST["txtPostcode"]) == ""){header("Location:orderForm.php?invalid=blankPost");break;}
                    else if(!preg_match("/^[a-z]{1,2}\d[a-z\d]?\s*\d[a-z]{2}$/i", trim($_POST["txtPostcode"])))
                    {header("Location:orderForm.php?invalid=badPost");break;}
                    else{$customerPostcode = $_POST["txtPostcode"];}


                    if(trim($_POST["txtPhone"]) == ""){header("Location:orderForm.php?invalid=blankPhone");break;}
                    else{$customerNumber = $_POST["txtPhone"];}
                    if(trim($_POST["txtEmail"]) == ""){header("Location:orderForm.php?invalid=blankEmail");break;}
                    else{$customerEmail = $_POST["txtEmail"];}

                    $insertString = "'$customerName', '$customerAdd1', '$customerAdd2', '$customerCity', 
                    '$customerCounty', '$customerPostcode', '$customerNumber', '$customerEmail'";
                    
                    $sqlComm = "INSERT INTO tblCustomer (customerName, addressLine1, addressLine2, city, 
                    county, postcode, contactNumber, emailAddress) VALUES ($insertString)";
                    
                    $this->sqlConnection->query($sqlComm);

                    $customerID = mysqli_insert_id($this->sqlConnection);

                    $this->customer = new Customer($customerID, $customerName, $customerAdd1, $customerAdd2, 
                    $customerCity, $customerCounty, $customerPostcode, $customerNumber, $customerEmail);
                    break;
                }
            }

            function createOrder()
            {
                $dateCreated =  date("Y-m-d");
                $customerID = $this->customer->GetID();
                $sqlComm = "INSERT INTO tblOrder (orderDate, customerID) VALUES ('$dateCreated', '$customerID')";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                $orderID = mysqli_insert_id($this->sqlConnection);

                $this->order = new Order($orderID, $dateCreated);

                if($this->basket->GetAllItems() != null)
                {
                    foreach($this->basket->GetAllItems() as $basketItem)
                    {
                        $productID = $basketItem->GetProduct()->GetID();
                        $quantity = $basketItem->GetQuantity();

                        $sqlComm = "INSERT INTO tblOrderDetail (productID, quantity, orderID) VALUES ('$productID', '$quantity', '.$orderID.')";
                        $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                        $orderDetailID = mysqli_insert_id($this->sqlConnection);

                        $this->order->AddDetail(new orderDetail($orderDetailID, $basketItem->GetProduct(), $basketItem->GetQuantity()));
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

            function GetID()
            {
                return $this->customerID;
            }

            function printDetails()
            {
                echo '<p id="fullName">'.$this->customerName.'</p>
                <p id="addressFull" class="customerDetailsInvLabel">'.$this->addressLine1.' '.$this->addressLine2.' <br /> '.$this->city.' <br /> '.$this->county.' <br /> '.$this->postcode.'</p>
                <p id="emailAddress" class="customerDetailsInvLabel">'.$this->emailAddress.'</p>
                <p id="contactNumber" class="customerDetailsInvLabel">'.sprintf("%011s",$this->contactNumber).'</p>';
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
                $this->AddProductImagePath("/productImages/" . $this->productID);
            }

            function AddProductImagePath(string $image)
            {
                $this->productImagePath = $image;
            }
            
            function GetID()
            {
                return $this->productID;
            }

            function GetName()
            {
                return $this->productName;
            }

            function GetPrice()
            {
                return $this->price;
            }
        }
     
        class Order
        {
            private $orderItems;

            private int $orderID;

            private string $orderDate;

            function __construct(int $orderID, string $orderDate)
            {
                $this->orderID = $orderID;
                $this->orderDate = $orderDate;
                $this->orderItems = array();
            }

            public function AddDetail(orderDetail $newItem)
            {
                $this->orderItems[] = $newItem;
            }

            public function GetAllItems()
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

            function GetProduct()
            {
                return $this->product;
            }

            function GetQuantity()
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
            public function AddProductToBasket(basketItem $newItem)
            {
                $this->basketItems[] = $newItem;
            }
         
            public function GetAllItems()
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

            function GetProduct()
            {
                return $this->product;
            }

            function GetQuantity()
            {
                return $this->quantity;
            }

        }
        
        session_start(); 
        $dbConnect = new DBConnect;
        $dbConnect->RetrieveAllProducts();
        $dbConnect->InstantiateAndPopulateBasket();        
        $dbConnect->createCustomer();
        $dbConnect->createOrder();       
        
  
        
?>
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
            <h2>Thankyou. Your order has been placed.</h2>
            <div id="invoiceNumberLabels">
               <h2>Invoice Number: </h2>
               <?php $dbConnect->GetOrder()->printOrderNumber();?>
            </div>
        
            <div id="customerDetailsInv">
            <h2>Customer Details</h2>
                <?php $dbConnect->GetCustomer()->printDetails(); ?>
            </div>

            <div id="orderBreakdownInv">
                <h2>Order Breakdown</h2>
                <table id="orderBreakdownTable">
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Item Total</th>
                </tr>
                <?php
                    foreach($dbConnect->GetOrder()->GetAllItems() as $orderDetail)
                        {
                            echo '<tr>
                                <td>'.$orderDetail->GetProduct()->GetName().'</td>
                                <td>'.$orderDetail->GetQuantity().'</td>
                                <td>&pound;'.number_format($orderDetail->GetProduct()->GetPrice()*$orderDetail->GetQuantity(), 2).'</td>
                                </tr>';
                        }
                    ?>
                </table>
            </div>
            <div id="orderTotals">
                <h2>Order Total: </h2>
                <?php $dbConnect->GetOrderTotal()?>
            </div>
            <div id="customerActionsInv">
                <button class="uiButton" onclick="window.print()">Print Invoice</button>
                <form method="post" action="index.php?action=cancel">
                    <input type="submit" class="uiButton" value="Close Invoice" formnovalidate/>
                </form>
            </div>
        </div>
    </div>

    

    <footer>
        <p>&copy; Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>