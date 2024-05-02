<?php
        namespace gazelleRunningSupplies;
        use mysqli;
        session_start();

        

        class dbConnect
        {       

            public object $sqlConnection;
            var $allProducts;
            var $basket;

            var $failLoginMessage;

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

            

            function printLoginFailMessage()
            {
                if(isset($_SESSION["failLoginMessage"]))
                {
                    echo '<p id="loginFailMessage">'.$_SESSION["failLoginMessage"].'</p>';
                }
            }
        }

        
        
        $dbConnect = new dbConnect;        
        
        
        


        

        
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
            <a href="index.php">Cancel</a>
        </div>
    </div>


    <img id="logoImage" src="gazelleLogo.jpeg" />
    <h1 id="header">Admin Login</h1>
    

    <div id="mainContent">        
        
        <div id="userDetails">
            <?php $dbConnect->printLoginFailMessage() ?>
            <h2>Enter your username and password</h2>
            <form id="userDetailsForm" method="post" action="loginCheck.php">
                <span class="inputAreasLogin" id="usernameInput"><label for="txtUsername">Username: </label><input name="txtUsername" required/></span>
                <span class="inputAreasLogin" id="passwordInput"><label for="txtPassword">Password:   </label><input name="txtPassword" required/></span>                
                <input type="submit" name="Login"  class="uiButton"/>
            </form>
            
        </div>
        
        
    </div>


    <footer>
        <p>Gazelle Running Supplies</p>
    </footer>

    <script src="myJavaScript.js"></script>
</body>
</html>