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
        await apiRequest('/login', { method: 'POST', body: JSON.stringify(body) });
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
}

function isAdminUser(user) {
    return user && (String(user.role) === '9' || user.user === 'admin' || user.id === 'admin' || user.id === 'chaitalk');
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
