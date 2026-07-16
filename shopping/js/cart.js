const cartList = document.getElementById('cartList');
const checkoutBtn = document.getElementById('checkoutBtn');
const cartMessage = document.getElementById('cartMessage');

renderCart();

checkoutBtn.addEventListener('click', async () => {
    const cart = getCart();
    if (cart.length === 0) {
        cartMessage.textContent = '장바구니가 비어 있습니다.';
        return;
    }

    try {
        for (const item of cart) {
            await apiRequest('/orders', {
                method: 'POST',
                body: JSON.stringify({ product_id: item.id, quantity: item.quantity }),
            });
        }
        saveCart([]);
        renderCart();
        cartMessage.textContent = '주문이 생성되었습니다.';
    } catch (error) {
        cartMessage.textContent = error.message;
    }
});

function renderCart() {
    const cart = getCart();
    checkoutBtn.disabled = cart.length === 0;
    if (cart.length === 0) {
        cartList.innerHTML = '<p class="empty">장바구니가 비어 있습니다.</p>';
        return;
    }

    cartList.innerHTML = cart.map((item) => `
        <article class="list-item">
            <img src="${escapeHtml(productImage(item.image_url))}" alt="${escapeHtml(item.name)}">
            <div>
                <h2>${escapeHtml(item.name)}</h2>
                <p>${formatPrice(item.price)} · ${item.quantity}개</p>
            </div>
            <button class="danger" data-remove="${item.id}">삭제</button>
        </article>
    `).join('');

    cartList.querySelectorAll('[data-remove]').forEach((button) => {
        button.addEventListener('click', () => {
            saveCart(cart.filter((item) => Number(item.id) !== Number(button.dataset.remove)));
            renderCart();
        });
    });
}
