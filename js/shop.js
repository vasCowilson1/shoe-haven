/* Member 3: Shop Interactions */
function selectSize(element, size) {
    var parent = element.closest('.sh-card-sizes') || element.closest('.sh-detail-sizes');
    if (!parent) return;
    parent.querySelectorAll('.sh-size-chip').forEach(function(c) { c.classList.remove('active'); });
    element.classList.add('active');
    element.setAttribute('data-selected-size', size);
}

function quickAddToCart(button, id, name, price, img) {
    var card = button.closest('.sh-product-card');
    var activeSizeEl = card ? card.querySelector('.sh-size-chip.active') : null;
    var size = activeSizeEl ? activeSizeEl.textContent.trim() : 'EU 41';
    
    addToCart({
        id: id,
        name: name,
        price: price,
        size: size,
        image: img,
        qty: 1
    });

    button.innerHTML = '<i class="bi bi-check2"></i> Added';
    button.classList.add('btn-success');
    button.classList.remove('btn-sh-orange');
    setTimeout(function() {
        button.innerHTML = '+ Add to Bag';
        button.classList.remove('btn-success');
        button.classList.add('btn-sh-orange');
    }, 1500);
}
