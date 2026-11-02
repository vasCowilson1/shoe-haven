/* Member 4: Cart and Subtotal Calculations */
var voucherDiscount = 0;

function renderCart() {
    var cart = getCart();
    var container = document.getElementById('cartItemsContainer');
    var emptyState = document.getElementById('emptyCartState');
    if (!container) return;

    if (cart.length === 0) {
        // Sample default item if completely fresh
        cart = [{
            id: 1,
            name: "Nike Air Pulse Runner",
            price: 185000,
            size: "EU 41",
            image: "assets/images/products/shoe-1.jpg",
            qty: 1
        }];
        saveCart(cart);
    }

    container.innerHTML = "";
    var subtotal = 0;

    cart.forEach(function(item, index) {
        var itemTotal = item.price * item.qty;
        subtotal += itemTotal;

        container.innerHTML += `
            <div class="sh-cart-item">
                <img src="${item.image}" alt="${item.name}" class="sh-cart-thumb">
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1">${item.name}</h6>
                    <div class="text-secondary small mb-2">Size: <span class="badge bg-light text-dark border">${item.size}</span></div>
                    <div class="fw-bold text-danger">UGX ${item.price.toLocaleString()}</div>
                </div>
                <div class="sh-qty-stepper">
                    <button class="sh-qty-btn" onclick="updateItemQty(${index}, -1)">−</button>
                    <span class="sh-qty-val">${item.qty}</span>
                    <button class="sh-qty-btn" onclick="updateItemQty(${index}, 1)">+</button>
                </div>
                <button class="btn btn-sm btn-outline-danger border-0" onclick="removeItem(${index})">
                    <i class="bi bi-trash3"></i>
                </button>
            </div>
        `;
    });

    // Update totals
    var total = Math.max(0, subtotal - voucherDiscount);
    document.getElementById('summarySubtotal').textContent = "UGX " + subtotal.toLocaleString();
    document.getElementById('summaryDiscount').textContent = "- UGX " + voucherDiscount.toLocaleString();
    document.getElementById('summaryTotal').textContent = "UGX " + total.toLocaleString();

    // Progress bar for UGX 200,000 free shipping
    var progressEl = document.getElementById('deliveryProgress');
    var progressText = document.getElementById('deliveryProgressText');
    if (progressEl && progressText) {
        var pct = Math.min(100, Math.round((subtotal / 200000) * 100));
        progressEl.style.width = pct + "%";
        if (subtotal >= 200000) {
            progressText.innerHTML = "🎉 <strong>Congratulations!</strong> You unlocked FREE Express Delivery to Mbarara!";
            progressEl.className = "progress-bar bg-success";
        } else {
            var needed = 200000 - subtotal;
            progressText.innerHTML = "Add <strong>UGX " + needed.toLocaleString() + "</strong> more for FREE Express Delivery!";
            progressEl.className = "progress-bar bg-warning";
        }
    }
}

function updateItemQty(index, delta) {
    var cart = getCart();
    if (!cart[index]) return;
    cart[index].qty += delta;
    if (cart[index].qty <= 0) {
        cart.splice(index, 1);
    }
    saveCart(cart);
    renderCart();
}

function removeItem(index) {
    var cart = getCart();
    cart.splice(index, 1);
    saveCart(cart);
    renderCart();
    showToast('Item removed from cart');
}

function applyVoucher() {
    var input = document.getElementById('voucherInput');
    var code = input ? input.value.trim().toUpperCase() : '';
    if (code === 'MUSTSTUDENT') {
        voucherDiscount = 20000;
        showToast('✓ Voucher Applied: UGX 20,000 Student Discount!');
        renderCart();
    } else if (code === 'FREESHIP') {
        voucherDiscount = 10000;
        showToast('✓ Voucher Applied: Delivery fee waived!');
        renderCart();
    } else {
        alert('Invalid voucher code. Try "MUSTSTUDENT"');
    }
}

document.addEventListener('DOMContentLoaded', renderCart);
