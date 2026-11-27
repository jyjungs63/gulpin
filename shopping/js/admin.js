const loginPanel = document.getElementById('loginPanel');
const adminPanel = document.getElementById('adminPanel');
const loginForm = document.getElementById('loginForm');
const productForm = document.getElementById('productForm');
const adminProducts = document.getElementById('adminProducts');
const formMessage = document.getElementById('formMessage');
const loginMessage = document.getElementById('loginMessage');

checkSession();

loginForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    loginMessage.textContent = '';
    const body = Object.fromEntries(new FormData(loginForm));
    try {
        const { user } = await apiRequest('/login', { method: 'POST', body: JSON.stringify(body) });
        if (!isAdminUser(user)) throw new Error('본사 관리자만 이용할 수 있습니다.');
        showAdmin();
    } catch (error) {
        loginMessage.textContent = error.message;
    }
});

document.getElementById('logoutBtn').addEventListener('click', async () => {
    await apiRequest('/logout', { method: 'POST', body: JSON.stringify({}) });
    loginPanel.classList.remove('hidden');
    adminPanel.classList.add('hidden');
});

document.getElementById('resetBtn').addEventListener('click', resetForm);

productForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    formMessage.textContent = '';
    const formData = new FormData(productForm);
    const id = formData.get('id');
    const path = id ? `/products/${id}` : '/products';
    if (id) {
        formData.append('_method', 'PUT');
    }

    try {
        await apiRequest(path, { method: 'POST', body: formData });
        formMessage.textContent = '저장되었습니다.';
        resetForm();
        loadAdminProducts();
    } catch (error) {
        formMessage.textContent = error.message;
    }
});

async function checkSession() {
    try {
        const { user } = await apiRequest('/me');
        if (isAdminUser(user)) {
            showAdmin();
        }
    } catch {
        loginPanel.classList.remove('hidden');
    }
}

function showAdmin() {
    loginPanel.classList.add('hidden');
    adminPanel.classList.remove('hidden');
    loadAdminProducts();
    loadAdminOrders();
}

const ADMIN_ORDER_STATUS = { pending: '결제 대기', paid: '결제 완료', canceled: '결제 취소(환불)' };
const ADMIN_PAYMENT_STATUS = { CONFIRMING: '승인 확인 중', CANCELING: '환불 확인 중', PARTIAL: '부분 환불' };

async function loadAdminOrders() {
    const adminOrders = document.getElementById('adminOrders');
    try {
        const { orders } = await serverCall('SShoppingAdminOrders', {});
        adminOrders.innerHTML = orders.length ? orders.map((order) => `
            <article class="panel order">
                <div class="order-head">
                    <strong>#${escapeHtml(order.id)} · ${escapeHtml(order.user_id)}</strong>
                    <span class="badge badge-${escapeHtml(order.status)}">${escapeHtml(ADMIN_PAYMENT_STATUS[order.payment_status] || ADMIN_ORDER_STATUS[order.status] || order.status)}</span>
                </div>
                <p>${(order.items || []).map((item) => `${escapeHtml(item.name)} × ${Number(item.quantity)}`).join(', ') || '-'}</p>
                <p>${formatPrice(order.amount)} · ${escapeHtml(order.created_at)} ${order.approved_at ? '· 승인 ' + escapeHtml(order.approved_at) : ''}</p>
                <p class="notice">${escapeHtml(order.receiver_name)} · ${escapeHtml(order.receiver_phone)} · (${escapeHtml(order.zip)}) ${escapeHtml(order.addr)}</p>
                <div class="actions">
                    ${['DONE', 'CANCELING'].includes(order.payment_status) ? `<button class="danger" data-cancel="${escapeHtml(order.payment_order_id)}">결제취소(환불)</button>` : ''}
                    ${order.receipt_url ? `<a class="secondary btn-link" href="${escapeHtml(order.receipt_url)}" target="_blank" rel="noopener noreferrer">영수증</a>` : ''}
                </div>
            </article>`).join('') : '<p class="empty">주문이 없습니다.</p>';

        adminOrders.querySelectorAll('[data-cancel]').forEach((button) => {
            button.addEventListener('click', async () => {
                const reason = prompt('결제 취소 사유를 입력하세요. (예: 고객 청약철회, 품절)');
                if (!reason) {
                    return;
                }
                button.disabled = true;
                try {
                    await gpPaymentCall('SPaymentCancel', { orderId: button.dataset.cancel, reason });
                    loadAdminOrders();
                } catch (error) {
                    alert(error.message);
                    button.disabled = false;
                }
            });
        });
    } catch (error) {
        adminOrders.innerHTML = `<p class="empty">${escapeHtml(error.message)}</p>`;
    }
}

function isAdminUser(user) {
    return user && String(user.role) === '9';
}

async function loadAdminProducts() {
    const { products } = await apiRequest('/products');
    adminProducts.innerHTML = products.map((product) => `
        <article class="list-item">
            <img src="${escapeHtml(productImage(product.image_url))}" alt="${escapeHtml(product.name)}">
            <div>
                <h2>${escapeHtml(product.name)}</h2>
                <p>${formatPrice(product.price)}</p>
            </div>
            <div class="actions">
                <button class="secondary" data-edit="${product.id}">수정</button>
                <button class="danger" data-delete="${product.id}">삭제</button>
            </div>
        </article>
    `).join('');

    adminProducts.querySelectorAll('[data-edit]').forEach((button) => {
        button.addEventListener('click', () => {
            const product = products.find((item) => Number(item.id) === Number(button.dataset.edit));
            fillForm(product);
        });
    });

    adminProducts.querySelectorAll('[data-delete]').forEach((button) => {
        button.addEventListener('click', async () => {
            if (!confirm('상품을 삭제할까요?')) {
                return;
            }
            await apiRequest(`/products/${button.dataset.delete}`, { method: 'DELETE' });
            loadAdminProducts();
        });
    });
}

function fillForm(product) {
    const fields = productForm.elements;
    fields.id.value = product.id;
    fields.name.value = product.name;
    fields.price.value = product.price;
    fields.description.value = product.description;
    fields.image_url.value = product.image_url || '';
    fields.image.value = '';
    formMessage.textContent = '수정할 내용을 입력하세요.';
}

function resetForm() {
    productForm.reset();
    productForm.elements.id.value = '';
}
