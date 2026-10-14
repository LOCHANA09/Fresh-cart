const API = 'php';
const money = n => '₹' + Number(n).toFixed(2);
const escapeHTML = value => String(value ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
async function request(url, options={}) {
  const response = await fetch(url, {headers:{'Content-Type':'application/json'}, ...options});
  const data = await response.json().catch(()=>({error:'Unexpected server response. Check PHP and database setup.'}));
  if (!response.ok) throw new Error(data.error || 'Request failed.');
  return data;
}
function showMessage(message, error=false) {
  const el=document.getElementById('message'); if(!el) return;
  el.textContent=message; el.classList.toggle('error',error);
}
async function updateCartCount() {
  try { const data=await request(`${API}/cart.php`); document.querySelectorAll('[data-cart-count]').forEach(el=>el.textContent=data.count); }
  catch(e) { /* The main page will display API errors when it loads. */ }
}
async function loadProducts() {
  const container=document.getElementById('products'); if(!container) return;
  try {
    const params=new URLSearchParams({q:document.getElementById('q').value,category:document.getElementById('category').value});
    const data=await request(`${API}/products.php?${params}`);
    const select=document.getElementById('category'); const selected=select.value;
    if(data.categories && select.options.length===1) data.categories.forEach(c=>{const o=document.createElement('option');o.value=c;o.textContent=c;select.appendChild(o)});
    select.value=data.categories?.includes(selected)?selected:'All';
    container.innerHTML=data.products.length?data.products.map(p=>`<article class="product-card"><div class="product-emoji" aria-hidden="true">${escapeHTML(p.emoji)}</div><p class="tag">${escapeHTML(p.category)}</p><h2>${escapeHTML(p.name)}</h2><p class="description">${escapeHTML(p.description)}</p><div class="card-bottom"><strong>${money(p.price)}</strong><button type="button" data-add="${Number(p.id)}">Add to cart</button></div></article>`).join(''):'<section class="empty"><h2>No products found</h2><p>Try another search.</p></section>';
  } catch(e) { container.innerHTML='<p>Could not load products. Start Apache/MySQL and import database.sql.</p>'; showMessage(e.message,true); }
}
async function loadCart() {
 const container=document.getElementById('cart-content'); if(!container) return;
 try {
  const data=await request(`${API}/cart.php`);
  if(!data.items.length){container.innerHTML='<section class="empty"><p>Your cart is empty.</p><a class="button-link" href="index.html">Browse groceries</a></section>';return;}
  container.innerHTML=`<div class="table-wrap"><table><thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead><tbody>${data.items.map(p=>`<tr><td>${escapeHTML(p.name)}</td><td>${money(p.price)}</td><td>${p.quantity}</td><td>${money(p.line_total)}</td><td><button class="danger" data-remove="${Number(p.id)}">Remove</button></td></tr>`).join('')}</tbody></table></div><p class="total">Total: ${money(data.total)}</p><a class="button-link" href="checkout.html">Proceed to checkout</a>`;
 } catch(e) {container.innerHTML='';showMessage(e.message,true);}
}
document.addEventListener('click',async event=>{
 const add=event.target.closest('[data-add]'), remove=event.target.closest('[data-remove]');
 try {
  if(add){await request(`${API}/cart.php`,{method:'POST',body:JSON.stringify({add_id:Number(add.dataset.add)})});showMessage('Product added to cart.');await updateCartCount();}
  if(remove){await request(`${API}/cart.php`,{method:'DELETE',body:JSON.stringify({remove_id:Number(remove.dataset.remove)})});await loadCart();await updateCartCount();showMessage('Product removed from cart.');}
 } catch(e){showMessage(e.message,true);}
});
document.addEventListener('DOMContentLoaded',()=>{
 updateCartCount();
 const search=document.getElementById('search-form'); if(search) {search.addEventListener('submit',e=>{e.preventDefault();loadProducts()});document.getElementById('reset-search').addEventListener('click',()=>{document.getElementById('q').value='';document.getElementById('category').value='All';loadProducts()});loadProducts();}
 loadCart();
 const form=document.getElementById('checkout-form'); if(form) form.addEventListener('submit',async e=>{
  e.preventDefault();const body=Object.fromEntries(new FormData(form).entries());
  try{const data=await request(`${API}/checkout.php`,{method:'POST',body:JSON.stringify(body)});form.classList.add('hidden');const success=document.getElementById('order-success');success.classList.remove('hidden');success.innerHTML=`<div class="product-emoji">✅</div><h2>Thank you, ${escapeHTML(data.customer_name)}!</h2><p>Your demo order <strong>#${data.order_id}</strong> was saved successfully.</p><p>Order total: <strong>${money(data.total)}</strong></p><p>No real payment or delivery is processed.</p><a class="button-link" href="index.html">Back to FreshCart</a>`;await updateCartCount();}
  catch(err){showMessage(err.message,true);}
 });
});