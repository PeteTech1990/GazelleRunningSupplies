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
                        
                        <span class="productSpan" >
                                <img class="productImage" id="shoe1Image"/>
                                <span class="productDetails" id="shoe1Details">
                                <p id="productName">Product Name</p>
                                <p id="productPrice">Product Price</p>
                            </span>
                            <button class="uiButton" id="addShoe1Basket">Add To Basket</button>
                        </span>
                        
                    </div>
                </div>

                <button class="accordion">Protective Wear</button>
                <div class="panel">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                </div>

                <button class="accordion">Clothes</button>
                <div class="panel">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                </div>

                <button class="accordion">Electronics</button>
                <div class="panel">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                </div>

                <button class="accordion">Headwear</button>
                <div class="panel">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
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