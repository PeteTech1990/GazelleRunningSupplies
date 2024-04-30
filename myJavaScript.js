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

var productImages = document.getElementsByClassName("productImage");
var productSpans = document.getElementsByClassName("productDetails");

for(var i=0;i<productImages.length;i++){
    productImages[i].onclick = openProductModal;
}

for(var i=0;i<productSpans.length;i++){
    productSpans[i].onclick = openProductModal;
}

// Get the anchor tag that opens a modal
var shoppingBasket = document.getElementById("shoppingBasketLink");

// Get the <span> element that closes the modal
var span1 = document.getElementsByClassName("close")[0];

// Get the <span> element that closes the modal
var span2 = document.getElementsByClassName("close")[1];


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

function openProductModal()
{
    modalProductDetails.style.display = "block";
}

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