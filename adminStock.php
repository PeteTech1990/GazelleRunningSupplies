<?php
        namespace gazelleRunningSupplies;

use DateTime;
use mysqli;



        

        class DBConnect
        {       

            public object $sqlConnection;
            var $allProducts;

            var $allOrders;


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
                $sqlComm = "SELECT * FROM tblProduct ORDER BY productName";
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

            

            function GetNumberOrdered(int $productID)
            {
                $result = null;
                $sqlComm = "SELECT COUNT(orderDetailID) AS countOfRows FROM tblOrderDetail WHERE productID='$productID'";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                if($sqlReturn->num_rows > 0)
                {
                    $result = $sqlReturn->fetch_assoc();
                    
                } 
                
                return $result["countOfRows"];
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



            function GetStock()
            {
                return $this->stock;
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


            public function AddDetail(orderDetail $newItem)
            {
                $this->orderItems[] = $newItem;
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



            
        }

        
        

        
        session_start();
        $dbConnect = new DBConnect;
        $dbConnect->RetrieveAllProducts();  
       
?>


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
            <?php $dbConnect->CheckAuthAndPrintLoggedInUser() ?>
        </div>
        <div id="adminLogin">
        <a href="adminLogin.php?action=logout">Logout</a>
        </div>
    </div>


    <img id="logoImage" src="gazelleLogo.jpeg" />
    <h1 id="header">Admin - Stock</h1>
    

    <div id="mainContent">  
        <div id="adminTabs">
            <a href="adminOrders.php?order=1"><h2>Orders</h2></a>
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
                    
                    <?php
                        foreach($dbConnect->GetAllProducts() as $product)
                        {                    
                            echo '<tr>
                            <td>'.$product->GetID().'</td>
                            <td>'.$product->GetName().'</td>
                            <td>'.$dbConnect->GetNumberOrdered($product->GetID()).'</td>
                            <td>'.$product->GetStock().'</td>
                            </tr>';
                        }
                    ?>
                </table>
            </div>            
        </div>
                
    </div>


    <footer>
        <p>&copy; Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>