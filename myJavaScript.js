/*https://www.w3schools.com/howto/howto_js_accordion.asp */

var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function () {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
            panel.style.display = "none";
        } else {
            panel.style.display = "block";
        }
    });
}

/*****************************************/




//Shopping Basket

/**https://www.w3schools.com/howto/howto_css_modals.asp**/


var closeButtons = document.getElementsByClassName("close");
var shoppingBasketModal = document.getElementById("modalShoppingBasket");



// Get the modal
var modalShoppingBasket = document.getElementById("modalShoppingBasket");

// Get the anchor tag that opens a modal
var shoppingBasket = document.getElementById("shoppingBasketLink");

// Get the element that closes the modal
var shoppingBasketClose = document.getElementById("shoppingBasketCloseButton");

shoppingBasket.onclick = function () {

    modalShoppingBasket.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
shoppingBasketClose.onclick = function () {
    modalShoppingBasket.style.display = "none";
}



//Window Click

var modalProductDetails = document.getElementsByClassName("modalProductDetails");

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target == modalShoppingBasket) {
        modalShoppingBasket.style.display = "none";
    }
    else
    {
        for(var i=0;i<modalProductDetails.length;i++)
        {
            if(event.target == modalProductDetails[i])
            {
                modalProductDetails[i].style.display = "none";
            }
        }
    }
}

/*****************************************/

//Product Modal functions

function openProductModal(modalID)
{    
    var productDetailModal = document.getElementById(modalID);
    productDetailModal.style.display = "block";
}

function closeProductModal(modalID) {
       
    var productDetailModal = document.getElementById(modalID);
    productDetailModal.style.display = "none";
}


//Navigation

function launchOrderForm() {
    window.location.replace("orderForm.php");
}
