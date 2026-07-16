const CART_KEY = 'shopping_cart';

async function apiRequest(path, options = {}) {
    const method = String(options.method || 'GET').toUpperCase();
    const body = parseRequestBody(options.body);
    const parts = path.split('/').filter(Boolean);

    if (path === '/products' && method === 'GET') {
        return serverCall('SShoppingListProducts', {});
    }
    if (parts[0] === 'products' && parts[1] && method === 'GET') {
        return serverCall('SShoppingGetProduct', { id: parts[1] });
    }
    if (path === '/products' && method === 'POST') {
        return serverCall('SShoppingSaveProduct', body);
    }
    if (parts[0] === 'products' && parts[1] && (method === 'PUT' || method === 'POST')) {
        return serverCall('SShoppingSaveProduct', withId(body, parts[1]));
    }
    if (parts[0] === 'products' && parts[1] && method === 'DELETE') {
        return serverCall('SShoppingDeleteProduct', { id: parts[1] });
    }
    if (path === '/orders' && method === 'POST') {
        return serverCall('SShoppingCreateOrder', body);
    }
    if (path === '/login' && method === 'POST') {
        const result = await loginCall(body);
        return result;
    }
    if (path === '/logout' && method === 'POST') {
        deleteLocalStorage('infochaitalk');
        return serverCall('SShoppingLogout', {});
    }
    if (path === '/me' && method === 'GET') {
        return serverCall('SShoppingMe', {});
    }

    throw new Error('지원하지 않는 API 요청입니다.');
}

function serverCall(functionName, data) {
    const payload = data instanceof FormData ? data : { functionName, otherData: normalizePayloadData(data) };
    if (payload instanceof FormData) {
        payload.set('functionName', functionName);
    }

    return new Promise((resolve, reject) => {
        CallAjax(
            'SMethods.php',
            'POST',
            payload,
            (response) => {
                if (response && response.error) {
                    reject(new Error(response.error));
                    return;
                }
                resolve(response);
            },
            (error) => reject(new Error(readAjaxError(error)))
        );
    });
}

function normalizePayloadData(data) {
    if (!data || Object.keys(data).length === 0) {
        return { _: '' };
    }

    return data;
}

function loginCall(data) {
    return new Promise((resolve, reject) => {
        CallAjax(
            'SMethods.php',
            'POST',
            {
                functionName: 'Slogon',
                Email: data.username,
                Password: data.password,
            },
            (response) => {
                if (!response || !response.success) {
                    reject(new Error('로그인에 실패했습니다.'));
                    return;
                }

                const user = response.success[0];
                saveLocalStorage('infochaitalk', user);
                resolve({ user });
            },
            (error) => reject(new Error(readAjaxError(error)))
        );
    });
}

function parseRequestBody(body) {
    if (!body) {
        return {};
    }
    if (body instanceof FormData) {
        return body;
    }
    if (typeof body === 'string') {
        return JSON.parse(body || '{}');
    }
    return body;
}

function withId(data, id) {
    if (data instanceof FormData) {
        data.set('id', id);
        return data;
    }

    return { ...data, id };
}

function readAjaxError(error) {
    if (error && error.responseJSON && error.responseJSON.error) {
        return error.responseJSON.error;
    }
    if (error && error.responseText) {
        try {
            const parsed = JSON.parse(error.responseText);
            return parsed.error || parsed['result:'] || error.responseText;
        } catch {
            return error.responseText;
        }
    }
    return '요청에 실패했습니다.';
}

function formatPrice(value) {
    return Number(value).toLocaleString('ko-KR') + '원';
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));
}

function getCart() {
    return JSON.parse(localStorage.getItem(CART_KEY) || '[]');
}

function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateCartCount();
}

function addToCart(product, quantity = 1) {
    const cart = getCart();
    const item = cart.find((entry) => Number(entry.id) === Number(product.id));
    if (item) {
        item.quantity += quantity;
    } else {
        cart.push({
            id: Number(product.id),
            name: product.name,
            price: Number(product.price),
            image_url: product.image_url,
            quantity,
        });
    }
    saveCart(cart);
}

function updateCartCount() {
    const count = getCart().reduce((sum, item) => sum + item.quantity, 0);
    const badge = document.getElementById('cartCount');
    if (badge) {
        badge.textContent = String(count);
    }
}

function productImage(url) {
    return url || 'https://images.unsplash.com/photo-1557821552-17105176677c?auto=format&fit=crop&w=900&q=80';
}

updateCartCount();
