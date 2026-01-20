// js/shop.js

function toggleCart(btn, id) {
    event.preventDefault();
    event.stopPropagation();

    const action = btn.classList.contains('add') ? 'add' : 'remove';

    fetch(`cart.php?action=${action}&id=${id}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            updateCartCounter(data.count);
            if (action === 'add') {
                btn.classList.remove('add');
                btn.classList.add('remove');
                btn.innerText = 'Usuń';
                btn.style.backgroundColor = '#dc3545';
            } else {
                btn.classList.remove('remove');
                btn.classList.add('add');
                btn.innerText = 'Do koszyka';
                btn.style.backgroundColor = '#28a745';
            }
        })
        .catch(error => console.error('Błąd:', error));
}

function updateCartItem(input, id) {
    event.stopPropagation();

    const ilosc = input.value;
    if (ilosc < 1) return;

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('id', id);
    formData.append('ilosc', ilosc);
    formData.append('ajax', 1);

    fetch('cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Jeśli serwer ograniczył ilość (bo brak towaru), zaktualizuj input
        if (data.new_qty && data.new_qty != ilosc) {
            input.value = data.new_qty;
        }

        // Jeśli jest komunikat o braku towaru
        if (data.alert) {
            alert(data.alert);
        }

        updateCartCounter(data.count);
        
        const itemTotalEl = document.getElementById(`item-total-${id}`);
        const cartSumEl = document.getElementById(`cart-sum`);
        const itemInfoEl = document.getElementById(`item-info-${id}`);

        if (itemTotalEl) itemTotalEl.innerHTML = data.item_total_text;
        if (cartSumEl) cartSumEl.innerText = data.cart_sum + " zł";
        if (itemInfoEl && data.item_unit_info) itemInfoEl.innerText = data.item_unit_info;
    })
    .catch(error => console.error('Błąd:', error));
}

function removeCartItem(btn, id) {
    event.preventDefault();
    event.stopPropagation();

    if(!confirm('Czy na pewno usunąć ten produkt?')) return;

    fetch(`cart.php?action=remove&id=${id}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            updateCartCounter(data.count);
            const row = document.getElementById(`cart-row-${id}`);
            if (row) row.remove();
            
            const cartSumEl = document.getElementById(`cart-sum`);
            if (cartSumEl) cartSumEl.innerText = data.cart_sum + " zł";

            if (data.count === 0) location.reload();
        });
}

function updateCartCounter(count) {
    const counter = document.getElementById('cart-counter');
    if (counter) {
        counter.innerText = count;
        counter.style.display = count > 0 ? 'flex' : 'none';
    }
}

function goToProduct(url) {
    window.location.href = url;
}

function stopProp(event) {
    event.stopPropagation();
}