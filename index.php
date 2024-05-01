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
        
        //https://phppot.com/php/simple-php-shopping-cart/
        if(!empty($_GET["action"]))
        {
            switch($_GET["action"])
            {
                case "addToBasket":
                    $dbConnect->addToBasket($_POST["productID"]);                
                    break;
                case "removeFromBasket":
                    $dbConnect->removeFromBasket($_POST["basketItemID"]);
                    break;
                case "changeBasketQuantity":
                    $dbConnect->updateBasket($_POST["basketItemID"], $_POST["quantity"]);
                    break;
                case "cancel":
                    $dbConnect->destroyBasket();
                    session_destroy();
                    session_start();
                    $_SESSION["basketID"] = $dbConnect->createBasket();

            }
        }

        //https://stackoverflow.com/questions/44887880/store-object-in-php-session

        if(!isset($_SESSION["basketID"]))
        {            
            $_SESSION["basketID"] = $dbConnect->createBasket();
        }
        else
        {
            $dbConnect->InstantiateAndPopulateBasket();            
        }

        
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gazelle Running Supplies - Product Catalogue</title>
    <link rel="stylesheet" href="styles.css" />
    
</head>
<body>
    
    <!--https://www.w3schools.com/howto/howto_css_modals.asp-->
     

    <!-- The Modal -->
    
        
        <?php 
            foreach($dbConnect->getAllProducts() as $product)
            {
                $product->getDetailDiv();
            }       
        ?>
         
    
    <!-- The Modal -->
    <div id="modalShoppingBasket" class="modal">
        
        <div class="modal-content-basket">
            <span id="shoppingBasketCloseButton">&times;</span>
            <h2>Your Shopping Basket</h2>
            <div class="modalInner" class="basketItems">
                <?php 
                   if($dbConnect->getBasket() != null)
                   {
                        if($dbConnect->getBasket()->getAllItems() != null)
                        {
                            foreach($dbConnect->getBasket()->getAllItems() as $basketItem)
                            {
                                $basketItem->getDiv();
                            }
                        }
                    }   
                ?>
            </div>
            <span class="basketTotal">
                <h2>Basket Total</h2>
                <?php $dbConnect->getBasketTotal()?>
            </span>            
             
            <?php
            if($dbConnect->getBasket()->getAllItems() != null)
            {
                echo ' <button class="uiButton" onclick="launchOrderForm()">Proceed To Order Form</button>';
            }           
            ?>
        </div>
         
    </div>
<!------------------------------------------------------>

    <div id="navBar">
        <div id="adminUserName">

        </div>
        <div id="adminLogin">
            <a href="adminLogin.php">Admin Login</a>
        </div>
    </div>


    <img id="logoImage" src="gazelleLogo.jpeg" />
    <h1 id="header">Product Catalogue</h1>
    <a id="shoppingBasketLink">
        <img id="shoppingBasket" src="shoppingBasket.png" />
    </a>

    <div id="mainContent">
        <div id="productCategories">
            <h2>Product Categories</h2>
            <div id="productAccordion">
                <!--https://www.w3schools.com/howto/howto_js_accordion.asp-->
                <button class="accordion">Shoes</button>
                <div class="panel">
                    <div class="products">
                        
                        <?php  

                            foreach($dbConnect->allProducts as $item)
                            {
                                if($item->getCategory() == "Shoes")
                                {
                                $item->getSpan();
                                }
                            }                               
                            
                        ?>
                        
                    </div>
                </div>

                <button class="accordion">Protective Wear</button>
                <div class="panel">
                <?php  

                    foreach($dbConnect->allProducts as $item)
                    {
                        if($item->getCategory() == "Protective Wear")
                        {
                        $item->getSpan();
                        }
                    }                               

                ?>
                    </div>

                <button class="accordion">Clothes</button>
                <div class="panel">
                <?php  

                    foreach($dbConnect->allProducts as $item)
                    {
                        if($item->getCategory() == "Clothes")
                        {
                        $item->getSpan();
                        }
                    }                               

                ?>
                    </div>

                <button class="accordion">Electronics</button>
                <div class="panel">
                <?php  

                    foreach($dbConnect->allProducts as $item)
                    {
                        if($item->getCategory() == "Electronics")
                        {
                        $item->getSpan();
                        }
                    }                               

                ?>
                    </div>

                <button class="accordion">Headgear</button>
                <div class="panel">
                <?php  

                    foreach($dbConnect->allProducts as $item)
                    {
                        if($item->getCategory() == "Headgear")
                        {
                        $item->getSpan();
                        }
                    }                               

                ?>
                     </div>
            </div>
        </div>

    </div>


    <footer>
        <p>Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>