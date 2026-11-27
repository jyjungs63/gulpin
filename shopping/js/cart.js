const cartList = document.getElementById('cartList');
const checkout = document.getElementById('checkout');
const checkoutForm = document.getElementById('checkoutForm');
const cartMessage = document.getElementById('cartMessage');
const agreeAll = document.getElementById('agreeAll');
const agreeItems = Array.from(document.querySelectorAll('.agree-item'));

// 화면 표시용 — 실제 결제금액은 서버(SShoppingCreateOrder)가 같은 규칙으로 다시 계산한다
const SHIPPING_FEE = 4500;
const FREE_SHIPPING_FROM = 100000;

renderCart();
checkLogin();

agreeAll.addEventListener('change', () => agreeItems.forEach((item) => (item.checked = agreeAll.checked)));
agreeItems.forEach((item) => item.addEventListener('change', () => {
    agreeAll.checked = agreeItems.every((entry) => entry.checked);
}));

checkoutForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const cart = getCart();
    if (cart.length === 0) {
        cartMessage.textContent = '장바구니가 비어 있습니다.';
        return;
    }
    if (!agreeItems.every((item) => item.checked)) {
        cartMessage.textContent = '필수 항목에 모두 동의해 주세요.';
        return;
    }

    const form = new FormData(checkoutForm);
    const payBtn = document.getElementById('payBtn');
    payBtn.disabled = true;
    cartMessage.textContent = '주문서를 저장하고 결제창을 여는 중입니다…';
    let order;
    try {
        const payload = {
            items: JSON.stringify(cart.map((item) => ({ product_id: item.id, quantity: item.quantity }))),
            receiver_name: form.get('receiver_name'),
            receiver_phone: form.get('receiver_phone'),
            zip: form.get('zip'),
            addr: form.get('addr'),
            agree: '1',
        };
        const stored = await checkoutAttempt(payload);
        ({ order } = await apiRequest('/orders', {
            method: 'POST',
            body: JSON.stringify({ ...payload, checkout_token: stored.token }),
        }));
        try {
            sessionStorage.setItem('gulpin_checkout', JSON.stringify({ ...stored, orderId: order.id }));
        } catch {}
        if (order.status !== 'pending') throw new Error('이미 처리된 주문입니다.');
        await gpPay('shop', order.id, { name: form.get('receiver_name'), phone: form.get('receiver_phone') });
    } catch (error) {
        cartMessage.textContent = error.message;
        if (order) {
            const link = document.createElement('a');
            link.href = 'orders.php';
            link.textContent = ' 주문 내역에서 다시 결제하기';
            cartMessage.append(link);
        } else {
            payBtn.disabled = false;
        }
    }
});

async function checkoutAttempt(payload) {
    const hex = (bytes) => Array.from(bytes, (byte) => byte.toString(16).padStart(2, '0')).join('');
    const token = hex(crypto.getRandomValues(new Uint8Array(16)));
    const stored = { token };
    try {
        const input = new TextEncoder().encode(JSON.stringify(payload));
        stored.fingerprint = hex(new Uint8Array(await crypto.subtle.digest('SHA-256', input)));
        const previous = JSON.parse(sessionStorage.getItem('gulpin_checkout') || 'null');
        if (previous?.fingerprint === stored.fingerprint) stored.token = previous.token;
        stored.cart = localStorage.getItem('shopping_cart');
        sessionStorage.setItem('gulpin_checkout', JSON.stringify(stored));
    } catch {}
    return stored;
}

async function checkLogin() {
    let user = null;
    try {
        ({ user } = await apiRequest('/me'));
    } catch {
        user = null;
    }
    document.getElementById('loginNotice').classList.toggle('hidden', !!user);
    checkoutForm.classList.toggle('hidden', !user);
}

function renderCart() {
    const cart = getCart();
    checkout.classList.toggle('hidden', cart.length === 0);
    if (cart.length === 0) {
        cartList.innerHTML = '<p class="empty">장바구니가 비어 있습니다.</p>';
        return;
    }

    cartList.innerHTML = cart.map((item) => `
        <article class="list-item">
            <img src="${escapeHtml(productImage(item.image_url))}" alt="${escapeHtml(item.name)}">
            <div>
                <h2>${escapeHtml(item.name)}</h2>
                <p>${formatPrice(item.price)} · ${Number(item.quantity)}개 = ${formatPrice(item.price * item.quantity)}</p>
            </div>
            <button class="danger" data-remove="${Number(item.id)}">삭제</button>
        </article>
    `).join('');

    cartList.querySelectorAll('[data-remove]').forEach((button) => {
        button.addEventListener('click', () => {
            saveCart(cart.filter((item) => Number(item.id) !== Number(button.dataset.remove)));
            renderCart();
        });
    });

    const subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const shipping = subtotal < FREE_SHIPPING_FROM ? SHIPPING_FEE : 0;
    document.getElementById('sumSubtotal').textContent = formatPrice(subtotal);
    document.getElementById('sumShipping').textContent = formatPrice(shipping);
    document.getElementById('sumTotal').textContent = formatPrice(subtotal + shipping);
    document.getElementById('payBtn').textContent = `${formatPrice(subtotal + shipping)} 결제하기`;
}
