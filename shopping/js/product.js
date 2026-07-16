const detail = document.getElementById('productDetail');
const productId = new URLSearchParams(location.search).get('id');

loadProduct();

async function loadProduct() {
    if (!productId) {
        detail.innerHTML = '<p class="notice">상품 ID가 없습니다.</p>';
        return;
    }

    try {
        const { product } = await apiRequest(`/products/${productId}`);
        detail.innerHTML = `
            <img src="${escapeHtml(productImage(product.image_url))}" alt="${escapeHtml(product.name)}">
            <div class="detail-body">
                <h1>${escapeHtml(product.name)}</h1>
                <p>${escapeHtml(product.description)}</p>
                <strong>${formatPrice(product.price)}</strong>
                <div class="buy-row">
                    <input id="quantity" type="number" min="1" value="1">
                    <button id="cartBtn" class="secondary">장바구니 담기</button>
                    <button id="buyBtn" class="primary">구매하기</button>
                </div>
                <p id="message" class="notice"></p>
            </div>
        `;

        document.getElementById('cartBtn').addEventListener('click', () => {
            addToCart(product, quantity());
            document.getElementById('message').textContent = '장바구니에 담았습니다.';
        });

        document.getElementById('buyBtn').addEventListener('click', async () => {
            const message = document.getElementById('message');
            const order = await apiRequest('/orders', {
                method: 'POST',
                body: JSON.stringify({ product_id: product.id, quantity: quantity() }),
            });
            message.textContent = `주문이 생성되었습니다. 주문번호: ${order.order.id}`;
        });
    } catch (error) {
        detail.innerHTML = `<p class="notice">${error.message}</p>`;
    }
}

function quantity() {
    return Math.max(1, Number(document.getElementById('quantity').value || 1));
}
