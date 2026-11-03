import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

function updateCartBadge(count) {
    const cartLink = document.querySelector('.sh-cart-icon');
    if (!cartLink) return;

    let badge = cartLink.querySelector('.sh-cart-badge');
    if (count < 1) {
        badge?.remove();
        return;
    }

    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'sh-cart-badge';
        cartLink.append(badge);
    }

    badge.textContent = String(count);
}

function showCartToast(message, count, isError = false) {
    const toast = document.getElementById('sh-toast');
    if (!toast) return;

    const countElement = toast.querySelector('.sh-toast-count');
    const icon = toast.querySelector('.sh-toast-icon i');
    toast.querySelector('.sh-toast-message').textContent = message;
    countElement.style.display = Number.isInteger(count) ? '' : 'none';
    if (Number.isInteger(count)) {
        countElement.querySelector('span').textContent = String(count);
        updateCartBadge(count);
    }
    icon.className = isError ? 'bi bi-exclamation-circle-fill' : 'bi bi-check-circle-fill';
    toast.querySelector('.sh-toast-icon').style.color = isError ? '#dc3545' : '#4caf50';

    clearTimeout(window.shCartToastTimer);
    toast.classList.remove('sh-toast-hide');
    toast.style.display = 'flex';
    requestAnimationFrame(() => toast.classList.add('sh-toast-show'));
    window.shCartToastTimer = setTimeout(() => {
        toast.classList.remove('sh-toast-show');
        toast.classList.add('sh-toast-hide');
        setTimeout(() => {
            toast.style.display = 'none';
            toast.classList.remove('sh-toast-hide');
        }, 400);
    }, 3500);
}

document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('.js-cart-add-form')) return;

    event.preventDefault();
    const button = form.querySelector('button[type="submit"]');
    if (button) button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        const result = await response.json();

        if (!response.ok) {
            const firstError = Object.values(result.errors ?? {}).flat()[0];
            showCartToast(firstError ?? result.message ?? 'Unable to add this product to your cart.', null, true);
            return;
        }

        showCartToast(result.message, result.count);
    } catch {
        showCartToast('Unable to add this product to your cart. Please try again.', null, true);
    } finally {
        if (button) button.disabled = false;
    }
});
