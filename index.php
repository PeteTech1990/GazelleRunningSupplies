<?php
        namespace gazelleRunningSupplies;
        
        //--mysqli namespace is used to facilitate communication with the MySQL database
        use mysqli;
        

        //--Class definition
        //--The DBConnect class is used to handle communication between the webpage and the MySQL database
        class DBConnect
        {       

            public object $sqlConnection;//--public object declaration. This object will be used to carry out queries on the MySQL database
            var $allProducts;//--Variable declaration. This variable will be used to store all "products" from the database
            var $basket;//--Variable declaration. This object will represent the current instance of the shopping basket

            //--Object constructor method. Takes no parameters and returns no value.
            function __construct()
            {

                //--Create and instantiate a string variable, storing the value "localhost". This value represents the name of the server device hosting the MySQL database.
                $sqlServer = "localhost";

                //--Create and instantiate a string variable, storing the value "gazellerunningsupplies". This value represents the name of the database that holds the data for this website.
                $database = "gazellerunningsupplies";
                
                //--Initialize the "allProducts" variable as an empty array
                $this->allProducts = array();

                //--Initialize the "sqlConnection" object as a mysqli object, passing the server, database, username and password parameters needed to establish communications with the database.
                $this->sqlConnection = new mysqli($sqlServer, "sa", "sa", $database);
                
            }

            //--Method declaration. Takes no parameters. Returns no objects.
            //--This method is used to retrieve "product" information from the database, create Product objects and then store those objects
            //--in the "allProducts" array
            function RetrieveAllProducts()
            {
                $sqlComm = "SELECT * FROM tblProduct";//--SQL SELECT query. Selecting all records from the tblProduct table
                $sqlReturn = $this->sqlConnection->query($sqlComm);//--Execute the query. Results are returned and assigned to the sqlReturn variable.
    
                if($sqlReturn->num_rows > 0)//--IF STATEMENT. If the query returned at least 1 result.
                {
                    while($row = $sqlReturn->fetch_assoc())//--WHILE LOOP. Will loop for as many records as are returned when calling the fetch_assoc method on the sqlReturn object.
                    {   //--This while loop iterates through each record in the tblProducts table.

                        //--Construct a new Product object, passing 3 values from the database record to the object constructor. Then assign the newly created object
                        //--to the variable "item"
                        $item = new Product($row["productID"], $row["productName"], $row["price"], $row["stock"], 
                        $row["category"], $row["description"]);

                        $this->allProducts[] = $item;//--Add the newly created object to the "allProducts" array.             
                    }
                }  
            }

            //--Method declaration. Takes no parameters. Returns an array object.
            //--This method is used return the "allProducts" array to the caller
            function GetAllProducts()
            {
                return $this->allProducts;
            }

            //--Method declaration. Takes 1 integer parameter. Returns a Product object.
            //--This method is used to retrieve a Product object from the allProducts array
            //--which has a productID matching the passed parameter. The Product object
            //--that is retrieved is then passed back to the caller.
            function GetProduct(int $productID)
            {
                foreach($this->allProducts as $product)//--FOREACH LOOP. Iterates through each object in the allProducts array, and assigns the object to the "product" variable.
                {
                    if($product->GetID() == $productID)//--IF STATEMENT. If the passed "productID" parameter matches the integer that is returned when the GetID method is called on the "product" object.
                    {
                        return $product;//--Return the object that is current referenced by the "product" object.
                    }
                }
            }

            //--Method declaration. Takes no parameters. Returns an integer value.
            //--This method is used to create a new record in the tblBasket table, in the database, every time a new browser session is started.
            function CreateBasket()
            {
                
                $dateCreated = date("Y-m-d");//Using the "date" method, assign a string representation of the current date to the variable "dateCreated"
                
                //SQL INSERT query. Will insert a new record into the tblBasket table, assigning the value of "dateCreated" to the "dateCreated" column for the record.
                $sqlComm = "INSERT INTO tblBasket (dateCreated) VALUES ('$dateCreated')";
                $sqlReturn = $this->sqlConnection->query($sqlComm);//--Execute the query
    
                //--The "autonumber" primary key ID for the newly created record is then returned using the "mysqli_insert_id" method.
                //--This ID integer is then assigned to the variable "basketID"
                $basketID = mysqli_insert_id($this->sqlConnection);

                return $basketID;//--The value assigned to "basketID" is returned to the caller
            }
                
            //--Method declaration. Takes 1 integer parameter. Returns no values.
            //--This method is used to add or alter records on the tblBasketItem table in the database.
            //--This method is invoked when the user has requested to add a new product to their shopping basket.
            function AddToBasket(int $productID)
            {
                //--Assign the current value of the session variable "basketID" to the local variable "basketID". This will tell the method which
                //--record on the "tblBasket" table is associated with the current browser session.
                $basketID = $_SESSION["basketID"];
                
                //--Declare and instantiate a boolean variable. This will be used to indicate whether or not the user has attempted to add a new product to the basket,
                //--or to simply increase the quantity of a current item in the basket.
                $productExist = false;
                
                //--Declare and instantiate an integer variable. If the product already exists in the basket, this variable will indicate the current quantity of that item in
                //--the shopping basket. 
                $currentQuantity = 0;

                //--Declare and instantiate an integer variable. If the product already exists in the basket, this variable will indicate the current 
                //--basketItemID of the product in the basket. 
                $currentBasketItemID = 0;


                //--SQL SELECT query. Selects all records from the tblBasketItem table WHERE the value of the "basketID" column matches the value of the "basketID" variable.
                //--This query will retrieve any "basket item" records for the shopping baskets associated with the current browser session.
                $sqlComm = "SELECT * FROM tblBasketItem WHERE basketID='$basketID'";
                $sqlReturn = $this->sqlConnection->query($sqlComm);//--Execute the query.
    
                if($sqlReturn->num_rows > 0)//--IF STATEMENT. If the query has returned at least 1 result
                {
                    while($row = $sqlReturn->fetch_assoc())//--WHILE LOOP. Will loop for as many records as are returned when calling the fetch_assoc method on the sqlReturn object.
                    //--Each returned result will assigned to the "row" object.
                    {   
                        if($row["productID"] == $productID)//--IF STATEMENT. If the value of the "productID" column, matches the value of the passed parameter "productID".
                        { //--This IF statement evaluates to true if, on the current iteration of the WHILE loop, the basket item record pertains to the product that was 
                            //--referenced by the passed parameter. In other words, the user has requested to increase the quantity of an already existing basket item.
                            $productExist = true;//--Set "productExist" to true
                            $currentQuantity = (int)$row["quantity"];//--Set "currentQuantity" to the value of the "quantity" column of the current row object.
                            $currentBasketItemID = $row["basketItemID"];//--Set "currentBasketItemID" to the value of the "basketItemID" column of the current row object.
                        }  
                        
                        //--IF the above IF STATEMENT evaluated to false, then the user has not requested to increase the quantity of an already existing basket item, and has
                        //--instead requested to add a new item.
                    }
                }                

                if($productExist)//--IF STATEMENT. IF "productExist" evaluates to true. 
                {
                    $newQuantity = ($currentQuantity + 1);//--Declare and instantiate a variable called "newQuantity". Assign the value of "currentQuantity", incremented by 1.

                    //--SQL UPDATE query. Updates "quantity" column in the tblBasketItem table, WHERE the basketItemID column is equal to the value of "currentBasketItemID".
                    //--This query will increase the quantity of an already existing basket item
                    $sqlComm = "UPDATE tblBasketItem SET quantity='$newQuantity' WHERE basketItemID='$currentBasketItemID'";
                    $this->sqlConnection->query($sqlComm);//--Execute the query
                    
                    
                }
                else//--ELSE (If "productExist" evaluates to false)
                {
                    //--SQL INSERT query. Inserts a record into the tblBasketItem table, using the values of "productID" and "basketID, and an integer value of 1, for
                    //--the "productID", "basketID" and "quantity" columns of the record.
                    //--This query will add a new basket item to the current shopping basket.
                    $sqlComm = "INSERT INTO tblBasketItem (productID, quantity, basketID) VALUES ('$productID', 1, '$basketID')";
               
                    $this->sqlConnection->query($sqlComm);//--Execute the query

                }
 
            }

            //--Method declaration. Takes 1 integer parameter. Returns no values.
            //--This method is used to delete records on the tblBasketItem table in the database.
            //--This method is invoked when the user has requested to remove a product from their shopping basket (or set the item's quantity value to 0).
            function RemoveFromBasket(int $basketItemID)
            {
                //--SQL DELETE query. Deletes any records from the tblBasketItem table WHERE the basketItemID column is equal to the value of the 
                //--passed parameter "basketItemID". This will delete a single record from the table, indicating that the item is no longer in the shopping basket.
                $sqlComm = "DELETE FROM tblBasketItem WHERE basketItemID='$basketItemID'";
                
                $this->sqlConnection->query($sqlComm);//--Execute the query.
            }

            //--Method declaration. Takes 2 integer parameters. Returns no values.
            //--This method is used to update records on the tblBasketItem table in the database.
            //--This method is invoked when the user has requested to update the quantity value for a product in their shopping basket. 
            function UpdateBasket(int $basketItemID, int $quantity)
            {
                //--Declare and instantiate a variable.
                //--First, call the "GetItem" method on the local "basket" object, passing the passed parameter "basketItemID" as a parameter.
                //--This will return the BasketItem object that is associated with the basket item that the user is trying to alter.
                //--Then, call the "GetProduct" method on the returned "BasketItem" object. This will return the Product object that is 
                //--associated with the basket item to be altered.
                //--Finally, call the "GetStock" method on the returned Product object. This will return an integer value indicating the current
                //--stock level for that product.
                //--This integer is assigned to the "maxStock" variable.
                $maxStock = $this->basket->GetItem($basketItemID)->GetProduct()->GetStock();

                if($quantity < 1)//--IF STATEMENT. If the value of the passed parameter "quantity" is less than one.
                {//--This indicates that the user wishes to remove the item from the basket.
                    $this->RemoveFromBasket($basketItemID);//--Call the local "RemoveFromBasket" method, passing the value of "basketItemID" as a parameter.
                }
                else//--ELSE (If the value of "quantity" is 1 or more)
                {//--Then the user wishes to increase the quantity for this item.

                    if($maxStock >= $quantity)//--IF STATEMENT. If the value of "maxStock" is more than or equal to the value of "quantity"
                    { //--Meaning there is enough of this item in stock to fulfill the order

                        //--SQL UPDATE query. Will update the quantity column to match the value of "quantity"
                        // for any records that have a "basketItemID" value matching the "basketItemID" passed parameter.
                        //--This will alter the basket item quantity to the amount the user has requested.
                        $sqlComm = "UPDATE tblBasketItem SET quantity='$quantity' WHERE basketItemID='$basketItemID'";
                
                        $this->sqlConnection->query($sqlComm);//--Execute the query.
                    }
                    else//--ELSE. ( If the value of "maxStock" is less than the value of "quantity"
                    { //--Meaning there is NOT enough of this item in stock to fulfill the order

                        //--SQL UPDATE query. Will update the quantity column to match the value of "maxStock", 
                        // for any records that have a "basketItemID" value matching the "basketItemID" passed parameter.
                        //--This will alter the basket item quantity to the maximum amount of stock available.
                        $sqlComm = "UPDATE tblBasketItem SET quantity='$maxStock' WHERE basketItemID='$basketItemID'";
                
                        $this->sqlConnection->query($sqlComm);//--Execute the query
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

            //--Method declaration. Takes 0 parameters. Returns no values.
            //--This method is used to retrieve any existing basket item records from the database for the current shopping basket.
            //--This method is called each time the web page is refreshed, so that the shopping basket can remain up-to-date
            function PopulateBasket()
            {
                //Declare and instantiate a variable. Call the "GetID" method on the local Basket object ("basket"). This will return the ID number for the current
                //--shopping basket. This ID number is then assigned to the varible "basketID".
                $basketID = $this->basket->GetID();
                
                //--Call the "Clear" method on the local Basket object. This will remove any existing BasketItem objects from the Basket's "basketItems" array
                $this->basket->Clear();

                //--SQL SELECT query. Select all records from tblBasketItem WHERE the value of the basketID column matches the value of the "basketID" variable.
                $sqlComm = "SELECT * FROM tblBasketItem WHERE basketID='$basketID'";
                $sqlReturn = $this->sqlConnection->query($sqlComm);//--Execute the query
    
                if($sqlReturn->num_rows > 0)//--IF STATEMENT. If the query has returned at least 1 result
                {
                    while($row = $sqlReturn->fetch_assoc())//--WHILE LOOP. Will loop for as many records as are returned when calling the fetch_assoc method on the sqlReturn object.
                    //--Each returned result will assigned to the "row" object.
                    {
                        //--On each iteration.
                        //--Calling the local "GetProduct" method, and passing the "productID" value from the database record, retrieve the associated Product
                        //--from the "allProducts" array. Then assign that product to the "product" variable.
                        $product = $this->GetProduct($row["productID"]);

                        //--Create a new BasketItem object, and assign it to the "newBasketItem" variable
                        $newBasketItem = new basketItem($row["basketItemID"],$product , $row["quantity"]);

                        //--Add the "newBasketItem" object to the basketItems array of the local Basket object, by calling the "AddProductToBasket" method on the 
                        //--Basket object and passing the newly created BasketItem object as a parameter.
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

            //--Method declaration. Takes 0 parameters. Returns no values.
            //--This method is used create a HTML representation of this Product object.
            //--The attributes for this object are interpolated into the HTML string
            //--which is then added to the outputted HTML code using the "echo" method.
            function GetSpan()
            {
                //--Interpolated values:
                //----"productID" into the id attribute of the parent SPAN object.
                //----"productID" into the onclick event handler for the img element
                //----"productID" into the id attribute of the img element
                //----"productImagePath" into the src attribute of the img element
                //----"productName" into the "productName" p element
                //----"price" into the "productPrice" p element
                //----"productID" into the "productID" hidden input field
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
        
        //--Start or resume the browser session
        session_start();
        $dbConnect = new DBConnect;
        $dbConnect->RetrieveAllProducts();


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
        
        //Inspiration for shopping basket method: (Vincy, 2022) 
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
    
    <!--Inspiration for CSS modal: (w3schools, no date b)-->
     

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
                <!--Inspiration for accordion view: (w3schools, no date a)-->
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