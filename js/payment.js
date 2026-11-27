function gpPaymentCall(functionName, otherData) {
  const endpoint = new URL(resolveServerMethod("SMethods.php"), getServerUrl());
  return fetch(endpoint, {
    method: "POST",
    credentials: "same-origin",
    headers: { "X-Gulpin-Payment": "1" },
    body: toRequestBody({ functionName, otherData }),
  }).then(async (response) => {
    const result = await response.json().catch(() => null);
    if (!response.ok || !result || !result.success) {
      throw new Error(result?.error || "결제 요청에 실패했습니다.");
    }
    return result.success;
  });
}

async function gpPay(source, id, customer = {}) {
  const prepared = await gpPaymentCall("SPaymentPrepare", { source, id });
  const db = getServerDbType();
  const pageUrl = (name) => {
    const url = new URL("../payment/" + name, getServerUrl());
    url.searchParams.set("source", source);
    if (db === "sqlite") url.searchParams.set("db", db);
    return url;
  };
  const successUrl = pageUrl("success.php");
  if (prepared.resumeOrderId) {
    successUrl.searchParams.set("orderId", prepared.resumeOrderId);
    location.assign(successUrl.href);
    return;
  }
  if (typeof TossPayments !== "function") {
    throw new Error("결제 모듈을 불러오지 못했습니다. 새로고침 후 다시 시도해 주세요.");
  }
  successUrl.searchParams.set("returnToken", prepared.returnToken);

  const phone = String(customer.phone || "").replace(/\D/g, "");
  const payment = TossPayments(prepared.clientKey).payment({ customerKey: TossPayments.ANONYMOUS });
  try {
    await payment.requestPayment({
      method: "CARD",
      amount: { currency: "KRW", value: prepared.amount },
      orderId: prepared.orderId,
      orderName: prepared.orderName,
      successUrl: successUrl.href,
      failUrl: pageUrl("fail.php").href,
      customerName: customer.name || undefined,
      customerMobilePhone: /^\d{10,11}$/.test(phone) ? phone : undefined,
      card: { useEscrow: false, flowMode: "DEFAULT", useCardPoint: false, useAppCardOnly: false },
    });
  } catch (error) {
    if (["USER_CANCEL", "PAY_PROCESS_CANCELED"].includes(error?.code)) {
      throw new Error("결제를 취소했습니다.");
    }
    throw new Error("결제창을 열지 못했습니다. 주문 내역에서 다시 시도해 주세요.");
  }
}
