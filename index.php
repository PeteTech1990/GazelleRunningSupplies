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
                        $item = new Product($row["productID"], $row["productName"], $row["price"], $row["stock"], 
                        $row["category"], $row["description"]);

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

            function CreateBasket()
            {
                
                $dateCreated = date("Y-m-d");
                
                $sqlComm = "INSERT INTO tblBasket (dateCreated) VALUES ('$dateCreated')";
                $sqlReturn = $this->sqlConnection->query($sqlComm);
    
                $basketID = mysqli_insert_id($this->sqlConnection);

                return $basketID;
            }
                
            function AddToBasket(int $productID)
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

            function RemoveFromBasket(int $basketItemID)
            {
                $sqlComm = "DELETE FROM tblBasketItem WHERE basketItemID='$basketItemID'";
                
                $this->sqlConnection->query($sqlComm);
            }

            function UpdateBasket(int $basketItemID, int $quantity)
            {
                $maxStock = $this->basket->GetItem($basketItemID)->GetProduct()->GetStock();

                if($quantity < 1)
                {
                    $this->removeFromBasket($basketItemID);
                }
                else
                {
                    if($maxStock >= $quantity)
                    {
                        $sqlComm = "UPDATE tblBasketItem SET quantity='$quantity' WHERE basketItemID='$basketItemID'";
                
                        $this->sqlConnection->query($sqlComm);
                    }
                    else
                    {
                        $sqlComm = "UPDATE tblBasketItem SET quantity='$maxStock' WHERE basketItemID='$basketItemID'";
                
                        $this->sqlConnection->query($sqlComm);
                    }
                }
            }

            function DestroyBasket()
            {
                $basketID = $_SESSION["basketID"];
                $sqlComm = "DELETE FROM tblBasketItem WHERE basketID='$basketID'";
                
                $this->sqlConnection->query($sqlComm);
                $sqlComm = "DELETE FROM tblBasket WHERE basketID='$basketID'";
                
                $this->sqlConnection->query($sqlComm);
            }

            function InstantiateBasket()
            {
                $basketID = $_SESSION["basketID"];
                $this->basket = new Basket($basketID);                     
                
            }

            function PopulateBasket()
            {
                $basketID = $this->basket->GetID();
                $this->basket->Clear();

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

                if($this->basket != null)
                {
                if($this->basket->GetAllItems() != null)
                {
                    foreach($this->basket->GetAllItems() as $basketItem)
                    {
                        $total += $basketItem->GetProduct()->GetPrice()*$basketItem->GetQuantity();
                    }
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

            function GetStock()
            {
                return $this->stock;
            }

            function GetCategory()
            {
                return $this->category;
            }

            function GetSpan()
            {
                echo '<span id=productDetails'.$this->productID.' class="productSpan" >
                
                        <img onclick="openProductModal('.$this->productID.')" 
                        id=productImage'.$this->productID.' class="productImage" src="'.$this->productImagePath.'.jpeg"/>

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

            function GetDetailDiv()
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

            public function AddProductToBasket(basketItem $newItem)
            {
                $this->basketItems[] = $newItem;
            }

            public function GetID()
            {
                return $this->basketID;
            }

            public function GetAllItems()
            {
                return $this->basketItems;
            }

            public function GetItem(int $basketItemID)
            {
                foreach($this->basketItems as $basketItem)
                {
                    if ($basketItem->GetID() == $basketItemID)
                    {
                        return $basketItem;
                    }
                }
            }

            public function GetItemCount()
            {
                $total = 0;
                foreach($this->basketItems as $basketItem)
                {
                    $total += $basketItem->GetQuantity();
                }
                return $total;
            }

            public function Clear()
            {
                $this->basketItems = array();
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

            function GetID()
            {
                return $this->basketItemID;
            }

            function GetDiv()
            {

                echo '<div class="basketItem" >
                        <p class="basketProductName">'.$this->product->GetName().'</p>
                        <form method="post" action="index.php?action=removeFromBasket">
                            <input type="hidden" name="basketItemID" value="'.$this->basketItemID.'"/>
                            <input type="submit" class="uiBasketButton" value="Remove"/>
                        </form>
                        <span>
                        <p class="basketProductPrice">&pound;'.number_format($this->product->GetPrice(), 2).' each</p>
                        <p>('.$this->product->GetStock().' available)</p>
                        </span>
                        <form method="post" action="index.php?action=changeBasketQuantity">
                            <input type="hidden" name="basketItemID" value="'.$this->basketItemID.'"/>
                            <label for="quantitySelector">Quantity:</label><input type="number" id="quantitySelector" class="quantitySelector" name="quantity" value="'.$this->quantity.'"/>
                            <input type="submit" class="uiBasketButton" value="Update"/>
                        </form>
                        <span class="basketItemTotal">
                            <p>Total:</p>
                            <p >&pound;'.number_format(($this->product->GetPrice()*$this->quantity), 2).'</p>
                        </span>
                    </div>';
            }
        }
        
        
        session_start();
        $dbConnect = new DBConnect;
        $dbConnect->RetrieveAllProducts();

        //https://stackoverflow.com/questions/44887880/store-object-in-php-session

        if(!isset($_SESSION["basketID"]))
        {            
            $_SESSION["basketID"] = $dbConnect->CreateBasket();
            $dbConnect->InstantiateBasket();
        }
        else
        {
            $dbConnect->InstantiateBasket(); 
            $dbConnect->PopulateBasket();            
        }
        
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
                    if($_POST["quantity"] != "")
                    {                        
                        $dbConnect->updateBasket($_POST["basketItemID"], $_POST["quantity"]);
                    }
                     
                    break;
                case "cancel":
                    $dbConnect->destroyBasket();
                    session_destroy();
                    session_start();
                    $_SESSION["basketID"] = $dbConnect->createBasket();

            }
        }

        $dbConnect->PopulateBasket();

        
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
            foreach($dbConnect->GetAllProducts() as $product)
            {
                $product->GetDetailDiv();
            }       
        ?>
         
    
    <!-- The Modal -->
    <div id="modalShoppingBasket" class="modal">
        
        <div class="modal-content-basket">
            <span id="shoppingBasketCloseButton">&times;</span>
            <h2>Your Shopping Basket</h2>
            <div class="modalInner" class="basketItems">
                <?php 
                   if($dbConnect->GetBasket() != null)
                   {
                        if($dbConnect->GetBasket()->GetAllItems() != null)
                        {
                            foreach($dbConnect->GetBasket()->GetAllItems() as $basketItem)
                            {
                                $basketItem->GetDiv();
                            }
                        }
                    }   
                ?>
            </div>
            <span class="basketTotal">
                <h2>Basket Total</h2>
                <?php $dbConnect->GetBasketTotal()?>
            </span>            
             
            <?php
           if($dbConnect->GetBasket() != null)
           {
                if($dbConnect->GetBasket()->GetAllItems() != null)
                    {
                        if($dbConnect->GetBasket()->GetItemCount() > 1)
                        {
                            echo ' <button class="uiButton" onclick="launchOrderForm()">Proceed To Order Form</button>';
                        }
                        else
                        {
                            echo ' <p id="minBasketWarning">**** Minimum order quantity is 2 items ****</p>';
                        }

                    } 
                    else
                        {
                            echo ' <p id="minBasketWarning">**** Minimum order quantity is 2 items ****</p>';
                        }
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

                            foreach($dbConnect->GetAllProducts() as $item)
                            {
                                if($item->GetCategory() == "Shoes")
                                {
                                $item->GetSpan();
                                }
                            }                               
                            
                        ?>
                        
                    </div>
                </div>

                <button class="accordion">Protective Wear</button>
                <div class="panel">
                <?php  

                    foreach($dbConnect->GetAllProducts() as $item)
                    {
                        if($item->GetCategory() == "Protective Wear")
                        {
                        $item->GetSpan();
                        }
                    }                               

                ?>
                    </div>

                <button class="accordion">Clothes</button>
                <div class="panel">
                <?php  

                    foreach($dbConnect->GetAllProducts() as $item)
                    {
                        if($item->GetCategory() == "Clothes")
                        {
                        $item->GetSpan();
                        }
                    }                               

                ?>
                    </div>

                <button class="accordion">Electronics</button>
                <div class="panel">
                <?php  

                    foreach($dbConnect->GetAllProducts() as $item)
                    {
                        if($item->GetCategory() == "Electronics")
                        {
                        $item->GetSpan();
                        }
                    }                               

                ?>
                    </div>

                <button class="accordion">Headgear</button>
                <div class="panel">
                <?php  

                    foreach($dbConnect->GetAllProducts() as $item)
                    {
                        if($item->GetCategory() == "Headgear")
                        {
                        $item->GetSpan();
                        }
                    }                               

                ?>
                     </div>
            </div>
        </div>

    </div>


    <footer>
        <p>&copy; Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>