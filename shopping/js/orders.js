const orderList = document.getElementById('orderList');
const orderMessage = document.getElementById('orderMessage');

// 주문 상태 (Server/SMethods.php SShoppingCreateOrder, payment_policy.php gpPaymentSetSource)
const ORDER_STATUS = { pending: '결제 대기', paid: '결제 완료', canceled: '결제 취소(환불)' };
const PAYMENT_STATUS = { CONFIRMING: '승인 확인 중', CANCELING: '환불 확인 중', PARTIAL: '부분 환불' };

loadOrders();

async function loadOrders() {
    try {
        const { orders } = await serverCall('SShoppingMyOrders', {});
        orderList.innerHTML = orders.length ? orders.map(renderOrder).join('') : '<p class="empty">주문 내역이 없습니다.</p>';
        orderList.querySelectorAll('[data-pay]').forEach((button) => {
            button.addEventListener('click', async () => {
                const order = orders.find((entry) => String(entry.id) === button.dataset.pay);
                button.disabled = true;
                try {
                    await gpPay('shop', order.id, { name: order.receiver_name, phone: order.receiver_phone });
                } catch (error) {
                    orderMessage.textContent = error.message;
                    button.disabled = false;
                }
            });
        });
    } catch (error) {
        orderList.innerHTML = `<p class="empty">${escapeHtml(error.message)} <a href="../index.php">로그인</a></p>`;
    }
}

function renderOrder(order) {
    const items = (order.items || []).map((item) => `${escapeHtml(item.name)} × ${Number(item.quantity)}`).join(', ');
    return `
        <article class="panel order">
            <div class="order-head">
                <strong>주문번호 ${escapeHtml(order.id)}</strong>
                <span class="badge badge-${escapeHtml(order.status)}">${escapeHtml(PAYMENT_STATUS[order.payment_status] || ORDER_STATUS[order.status] || order.status)}</span>
            </div>
            <p>${items || '-'}</p>
            <p>결제금액 <strong>${formatPrice(order.amount)}</strong> (배송비 ${formatPrice(order.shipping_fee)}) · 주문일 ${escapeHtml(order.created_at)}</p>
            <p class="notice">${escapeHtml(order.receiver_name)} · ${escapeHtml(order.receiver_phone)} · (${escapeHtml(order.zip)}) ${escapeHtml(order.addr)}</p>
            <div class="actions">
                ${order.status === 'pending' ? `<button class="primary" data-pay="${escapeHtml(order.id)}">${order.payment_status === 'CONFIRMING' ? '승인 상태 확인' : '결제하기'}</button>` : ''}
                ${order.receipt_url ? `<a class="secondary btn-link" href="${escapeHtml(order.receipt_url)}" target="_blank" rel="noopener noreferrer">영수증</a>` : ''}
            </div>
        </article>`;
}
