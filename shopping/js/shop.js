const grid = document.getElementById('productGrid');
const emptyState = document.getElementById('emptyState');

loadProducts();

async function loadProducts() {
    try {
        const { products } = await apiRequest('/products');
        emptyState.classList.toggle('hidden', products.length > 0);
        grid.innerHTML = products.map(renderProduct).join('');
        grid.querySelectorAll('[data-cart]').forEach((button) => {
            button.addEventListener('click', () => {
                const product = products.find((item) => Number(item.id) === Number(button.dataset.cart));
                addToCart(product);
                button.textContent = '담김';
                setTimeout(() => (button.textContent = '장바구니'), 900);
            });
        });
    } catch (error) {
        emptyState.classList.remove('hidden');
        emptyState.textContent = error.message;
    }
}

function renderProduct(product) {
    return `
        <article class="product-card">
            <a href="product.php?id=${product.id}">
                <img src="${escapeHtml(productImage(product.image_url))}" alt="${escapeHtml(product.name)}">
                <div class="product-body">
                    <h2>${escapeHtml(product.name)}</h2>
                    <p>${escapeHtml(product.description)}</p>
                    <strong>${formatPrice(product.price)}</strong>
                </div>
            </a>
            <button class="secondary" data-cart="${product.id}">장바구니</button>
        </article>
    `;
}
