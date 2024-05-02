<?php
        namespace gazelleRunningSupplies;
        use mysqli;
        
        

        class DBConnect
        {       

            public object $sqlConnection;
            var $allProducts;
            var $basket;

            function __construct()
            {
                $sqlServer = "localhost";
                $database = "gazellerunningsupplies";
                $this->allProducts = array();

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

            function GetBasketTotal()
            {
                $total = 0;

                if($this->basket->GetAllItems() != null)
                {
                    foreach($this->basket->GetAllItems() as $basketItem)
                    {
                        $total += $basketItem->GetProduct()->GetPrice()*$basketItem->GetQuantity();
                    }
                }

                echo '<h2>&pound;'.number_format($total, 2).'</h2>';
            }

            function GetBasket()
            {
                return $this->basket;
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
        
        $dbConnect = new DBConnect;
        $dbConnect->RetrieveAllProducts();
        session_start();
        $dbConnect->InstantiateAndPopulateBasket();

        ?>

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
                <table id="orderBreakdownTable">
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Item Total</th>
                </tr>
                <?php
                    foreach($dbConnect->GetBasket()->GetAllItems() as $basketItem)
                        {
                            echo '<tr>
                                <td>'.$basketItem->GetProduct()->GetName().'</td>
                                <td>'.$basketItem->GetQuantity().'</td>
                                <td>&pound;'.number_format($basketItem->GetProduct()->GetPrice()*$basketItem->GetQuantity(), 2).'</td>
                                </tr>';
                        }
                    ?>
                </table>
            </div>
            <span id="orderTotalLabels">
                <h2>Order Total:</h2>
                <?php $dbConnect->GetBasketTotal()?>
            </span>
        </div>
        <div id="customerDetails">
            <h2>Customer Details</h2>
            <form id="detailsForm" method="post" action="invoice.php">
                <span class="inputAreas" id="customerNameInput"><label for="txtFullName">Full Name: </label><input name="txtFullName" required/></span>
                <span class="inputAreas" id="address1Input"><label for="txtAddress1">Address Line 1: </label><input name="txtAddress1" required/></span>
                <span class="inputAreas" id="address2Input"><label for="txtAddress2">Address Line 2: </label><input name="txtAddress2" required/></span>
                <span class="inputAreas" id="cityInput"><label for="txtCity">City: </label><input name="txtCity" required/></span>
                <span class="inputAreas" id="countyInput"><label for="txtCounty">County: </label><input name="txtCounty" required/></span>
                <span class="inputAreas" id="postcodeInput"><label for="txtPostcode">Postcode: </label><input name="txtPostcode" required maxlength=7 /></span>
                <span class="inputAreas" id="phoneInput"><label for="txtPhone">Contact Number: </label><input type="number" name="txtPhone" required maxlength=11 /></span>
                <span class="inputAreas" id="emailInput"><label for="txtEmail">Email Address: </label><input type="email" name="txtEmail" required /></span>
            
        </div>
        <div id="customerActions">
            <input type="submit" class="uiButton" value="Place Order"/>
            </form>
            <form method="post" action="index.php">
                <input type="submit" class="uiButton" value="Amend Order" formnovalidate/>
            </form>
            <form method="post" action="index.php?action=cancel">
                <input type="submit" class="uiButton" value="Cancel Order" formnovalidate/>
            </form>
        </div>
        
    </div>


    <footer>
        <p>&copy; Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>