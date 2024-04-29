namespace gazelleRunningSupplies;

public class Product
{
    

    private int $productID;
    private string $productName;
    private string $productImagePath;
    private float $price;
    private int $stock;

    function __construct(int $productID, string $productName, float $price, int $stock)
    {
        $this->productID = $productID;
        $this->productName = $productName;
        $this->price = $price;
        $this->stock = $stock;
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

    function getSpan()
    {
        echo '<span class="productSpan" ><img class="productImage" id="shoe1Image"/><span class="productDetails" id="shoe1Details"><p id="productName">Product Name</p><p id="productPrice">Product Price</p></span><button class="uiButton" id="addShoe1Basket">Add To Basket</button></span>';
    }
}

