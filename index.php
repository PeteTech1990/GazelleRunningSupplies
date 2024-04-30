<?php
        namespace gazelleRunningSupplies;
        use ArrayObject;
        use mysqli;
        
        
        $sqlServer = "localhost";
        $database = "gazellerunningsupplies";

        $sqlConnection = new mysqli($sqlServer, "sa", "sa", $database);

         class Product
        { 
            private int $productID;
            private string $productName;
            private string $productImagePath;
            private float $price;
            private int $stock;
            private string $category;

            function __construct(int $productID, string $productName, float $price, int $stock, string $category)
            {
                $this->productID = $productID;
                $this->productName = $productName;
                $this->price = $price;
                $this->stock = $stock;
                $this->category = $category;
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
                echo '<span class="productSpan" >
                        <img class="productImage" src="'.$this->productImagePath.'.jpeg"/>
                        <span class="productDetails">
                            <p id="productName">' . $this->productName . '</p>
                            <p id="productPrice">&pound;' . number_format($this->price, 2) . '</p> 
                        </span> 
                        <button class="uiButton" id="addToBasket('.$this->productID.')">Add To Basket</button>
                    </span>';
            }
        }
     
        $allProducts = new ArrayObject;
                
        $sqlComm = "SELECT * FROM tblProduct WHERE category='Shoes'";
        $sqlReturn = $sqlConnection->query($sqlComm);

        if($sqlReturn->num_rows > 0)
        {
            while($row = $sqlReturn->fetch_assoc())
            {
                $item = new Product($row["productID"], $row["productName"], $row["price"], $row["stock"], $row["category"]);                                                                         
                $allProducts->append($item);                
            }
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
    <div id="modalProductDetails" class="modal">
        
        <?php
        include 'productDetailModal.html';
        ?>
         
    </div>

    <!-- The Modal -->
    <div id="modalShoppingBasket" class="modal">
        
        <?php
        include 'shoppingBasketModal.html';
        ?>
         
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

                            foreach($allProducts as $item)
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
               
                    </div>

                <button class="accordion">Clothes</button>
                <div class="panel">
                
                    </div>

                <button class="accordion">Electronics</button>
                <div class="panel">
                
                    </div>

                <button class="accordion">Headgear</button>
                <div class="panel">
                
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