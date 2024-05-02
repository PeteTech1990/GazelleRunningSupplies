<?php
        namespace gazelleRunningSupplies;

use DateTime;
use mysqli;



        

        class DBConnect
        {       

            public object $sqlConnection;
            var $allProducts;
          

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

            function RetrieveFilteredOrders()
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


            function GetAllOrders()
            {
                return $this->allOrders;
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


     



            

            function GetOrderTotal(Order $order)
            {
                $total = 0;

                if($order->GetAllItems() != null)
                {
                    foreach($order->GetAllItems() as $orderItem)
                    {
                        $total += $orderItem->GetProduct()->GetPrice()*$orderItem->GetQuantity();
                    }
                }

                return number_format($total, 2);
            }

            

           

            
                function GetCustomer(string $request, int $customerID)
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

            

            function CheckAuthAndPrintLoggedInUser()
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
                $orderID = $order->GetID();
                $sqlComm = "SELECT * FROM tblOrderDetail WHERE orderID='$orderID'";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                if($sqlReturn->num_rows > 0)
                {
                    while($row = $sqlReturn->fetch_assoc())
                    {
                        $product = $this->GetProduct($row["productID"]);
                        $order->AddDetail(new orderDetail($row["orderDetailID"], $product, $row["quantity"]));          
                    }
                }  
                
            }

            function SetDisplayedOrder(Order $order)
            {
                $this->displayedOrder = $order;
            }

            function GetDisplayedOrder()
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

            function GetID()
            {
                return $this->customerID;
            }

            function GetName()
            {
                return $this->customerName;
            }

            function GetAddressLine1()
            {
                return $this->addressLine1;
            }

            function GetAddressLine2()
            {
                return $this->addressLine2;
            }

            function GetCity()
            {
                return $this->city;
            }

            function GetCounty()
            {
                return $this->county;
            }

            function GetPostcode()
            {
                return $this->postcode;
            }

            function GetPhone()
            {
                return $this->contactNumber;
            }
            
            function GetEmailAddress()
            {
                return $this->emailAddress;
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

            private int $customerID;

            function __construct(int $orderID, string $orderDate, int $customerID)
            {
                $this->orderID = $orderID;
                $this->orderDate = $orderDate;
                $this->customerID = $customerID;
                $this->orderItems = array();
            }

            function GetID()
            {
                return $this->orderID;
            }           

            function GetDate()
            {
                return $this->orderDate;
            }

            function GetCustomerID()
            {
                return $this->customerID;
            }

            public function AddDetail(orderDetail $newItem)
            {
                $this->orderItems[] = $newItem;
            }

     

            public function GetAllItems()
            {
                return $this->orderItems;
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

        
        session_start();
        $dbConnect = new DBConnect;
        $dbConnect->RetrieveAllProducts();   
                
       
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
        
        $dbConnect->RetrieveFilteredOrders();

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
            <?php $dbConnect->CheckAuthAndPrintLoggedInUser() ?>
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
                            if($dbConnect->GetAllOrders() != null)
                            {
                                $loopCount = 1;
                                $viewDetail = null;

                                foreach($dbConnect->GetAllOrders() as $order)
                                {
                                    
                                    if($loopCount == $_GET["order"])
                                        {
                                            $viewDetail = "Selected. See Below";
                                            $dbConnect->SetDisplayedOrder($order);                                            
                                        }
                                        else
                                        {
                                            $viewDetail = '<form method="post" action="adminOrders.php?order='.($loopCount).'">
                                            <input type="hidden" name="orderChange" value="true">
                                            <input type="submit" value="View Details">
                                            </form>';
                                        }
                                    $customerID = $order->GetCustomerID();
                                    echo '<tr>
                                    <td>'.$order->GetID().'</td>
                                    <td>'.$order->GetDate().'</td>
                                    <td>'.$dbConnect->GetCustomer("nameOnly", $customerID).'</td>
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

        

        <div id="orderDetailDiv" <?php if($dbConnect->GetDisplayedOrder() == null){echo 'class="hidden"';} ?>>
            <h2>Order detail</h2>
            <div id="orderDetail">
                <span id="orderNumberSpan">
                    <strong>Order Number:</strong>
                    <p id="orderDetailNumber"><?php echo $dbConnect->GetDisplayedOrder()->GetID(); ?></p>
                </span>
                <div id="customerDetailsDiv">
                    <strong>Customer Details</strong>
                    <?php 
                        $customer = $dbConnect->GetCustomer("fullDetail", $dbConnect->GetDisplayedOrder()->GetCustomerID());
                    echo '<p id="orderDetailCustomerName">'.$customer->GetName().'</p>
                    <p id="orderDetailAddress">'.$customer->GetAddressLine1().'<br />'.$customer->GetAddressLine2().'<br />'.$customer->GetCity().'<br />'.$customer->GetCounty().'<br />'.$customer->GetPostcode().'</p>
                    <p id="orderDetailEmail">'.$customer->GetEmailAddress().'</p>
                    <p id="orderDetailContact">'.sprintf("%011s",$customer->GetPhone()).'</p>';
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
                                foreach($dbConnect->GetDisplayedOrder()->GetAllItems() as $orderDetail)
                                    {
                                        echo '<tr>
                                            <td>'.$orderDetail->GetProduct()->GetName().'</td>
                                            <td>'.$orderDetail->GetQuantity().'</td>
                                            <td>'.number_format($orderDetail->GetProduct()->GetPrice()*$orderDetail->GetQuantity(), 2).'</td>
                                            </tr>';
                                    }
                            ?>
                        </table>
                    </div>
                </div>
                <span id="orderTotalSpan">
                    <strong>Order Total:</strong>
                    <p id="orderDetailTotal">&pound;<?php echo $dbConnect->GetOrderTotal($dbConnect->GetDisplayedOrder()) ?></p>
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