/* Member 5: Checkout & Payment Actions */
function selectPaymentMethod(element, type) {
    document.querySelectorAll('.sh-payment-option').forEach(function(o) { o.classList.remove('active'); });
    element.classList.add('active');
    var momoBox = document.getElementById('momoInputBox');
    if (momoBox) {
        momoBox.style.display = (type === 'momo' || type === 'airtel') ? 'block' : 'none';
    }
}

function submitCheckout(e) {
    e.preventDefault();
    var btn = document.getElementById('btnSubmitOrder');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Dispatches USSD Prompt...';
    }
    setTimeout(function() {
        // Clear cart
        localStorage.removeItem('sh_cart');
        window.location.href = "order-confirmation.html";
    }, 2000);
}
