<?php
        namespace gazelleRunningSupplies;
        use mysqli;
        
        

        class dbConnect
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

            function getBasket()
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
                    foreach($dbConnect->getBasket()->getAllItems() as $basketItem)
                        {
                            echo '<tr>
                                <td>'.$basketItem->getProduct()->getName().'</td>
                                <td>'.$basketItem->getQuantity().'</td>
                                <td>&pound;'.number_format($basketItem->getProduct()->getPrice()*$basketItem->getQuantity(), 2).'</td>
                                </tr>';
                        }
                    ?>
                </table>
            </div>
            <span id="orderTotalLabels">
                <h2>Order Total:</h2>
                <?php $dbConnect->getBasketTotal()?>
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