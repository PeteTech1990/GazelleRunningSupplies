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


/**https://www.w3schools.com/howto/howto_css_modals.asp**/
// Get the modal
var modalProductDetails = document.getElementById("modalProductDetails");

// Get the modal
var modalShoppingBasket = document.getElementById("modalShoppingBasket");

// Get the image that opens a modal
var itemImage = document.getElementById("shoe1Image");

// Get the span that opens a modal
var itemDetails = document.getElementById("shoe1Details");

// Get the anchor tag that opens a modal
var shoppingBasket = document.getElementById("shoppingBasketLink");

// Get the <span> element that closes the modal
var span1 = document.getElementsByClassName("close")[0];

// Get the <span> element that closes the modal
var span2 = document.getElementsByClassName("close")[1];

// When the user clicks on the button, open the modal
itemDetails.onclick = function () {

    modalProductDetails.style.display = "block";
}

// When the user clicks on the button, open the modal
itemImage.onclick = function () {
    
    modalProductDetails.style.display = "block";
}

shoppingBasket.onclick = function () {

    modalShoppingBasket.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span1.onclick = function () {
    modalProductDetails.style.display = "none";
}

// When the user clicks on <span> (x), close the modal
span2.onclick = function () {
    modalShoppingBasket.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target == modal) {
        modalProductDetails.style.display = "none";
        modalShoppingBasket.style.display = "none";
    }
}

/*****************************************/

function closeModal() {
    modalProductDetails.style.display = "none";
    modalShoppingBasket.style.display = "none";
}

function launchOrderForm() {
    window.location.replace("orderForm.php");
}

function launchInvoice() {
    window.location.replace("invoice.php");
}