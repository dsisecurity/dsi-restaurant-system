let cart = [];

function addToCart(id,name,price){

cart.push({
id:id,
name:name,
price:price,
qty:1
});

localStorage.setItem("cart",JSON.stringify(cart));

alert("Producto agregado");

function eliminarItem(index){

    fetch("update_cart_ajax.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"index="+index+"&change=-999"
    })
    .then(res=>res.json())
    .then(data=>actualizarCarrito(data));
}
}