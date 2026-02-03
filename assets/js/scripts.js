document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('product-grid')) {
        loadProducts();
    }

    const loginLink = document.getElementById('login-link');
    if (loginLink) {
        loginLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'customer/login.php';
        });
    }

    const registerLink = document.getElementById('register-link');
    if (registerLink) {
        registerLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'customer/register.php';
        });
    }
});

async function loadProducts() {
    try {
        const response = await fetch('get_products.php');
        const products = await response.json();
        displayProducts(products);
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

function displayProducts(products) {
    const grid = document.getElementById('product-grid');
    grid.innerHTML = '';

    products.forEach(product => {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
            <img src="${product.image_path}" alt="${product.name}">
            <div class="content">
                <h3>${product.name}</h3>
                <p>$${product.price}</p>
                <a href="#" class="btn" onclick="buyNow(${product.id})">Buy Now</a>
            </div>
        `;
        grid.appendChild(card);
    });
}

function buyNow(id) {
    window.location.href = 'place_order.php?product_id=' + id;
}

function showMessage(message, type = 'info') {
    alert(message);
}

function redirectTo(url) {
    window.location.href = url;
}