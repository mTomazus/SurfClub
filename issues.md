# Issues

---

## Products module (`modules/products/controllers/Products.php`)

Found: 2026-05-17

### 1. Unvalidated POST input in `add_to_cart()`
**Line:** ~199  
**Risk:** Type confusion, potential injection  
`$productId = $_POST['product_id'];` — no sanitisation.  
**Fix:** `$productId = (int) post('product_id');`

---

### 2. INNER JOIN on variants hides products without variants — `item()`
**Line:** ~116  
**Risk:** Product page returns "Product not found." for any product with no entries in `products_variants`  
**Fix:** Change `JOIN products_variants v ON p.id = v.product_id` to `LEFT JOIN`

---

### 3. Omniva locker list fetched live on every checkout load — `_get_omniva_lockers()`
**Line:** ~157  
**Risk:** Checkout hangs or breaks if omniva.ee is slow or down. No caching.  
**Fix:** Cache the JSON response to a local file, refresh once per day.

---

### 4. Missing `wishlist` view — `wishlist()`
**Line:** ~1099  
**Risk:** `$this->view('wishlist', $data)` is called but no `wishlist.php` exists in `modules/products/views/`. Page will error.  
**Fix:** Create `modules/products/views/wishlist.php` or change to use the correct template method.

---

### 5. `update_cart()` sends no response
**Line:** ~218  
**Risk:** Called via HTMX (`mx-post`) — no redirect and no output returned, so the cart UI won't refresh correctly on failure edge cases.  
**Fix:** Ensure the method always returns a response (even empty `echo ''`) so HTMX can handle the swap.

---

### 6. `save_product_variants()` — no dedup on create
**Line:** ~695  
**Risk:** If `submit()` is called twice (double-click, retry), duplicate variant rows are inserted for new products. Update path is fine (deletes first).  
**Fix:** On create, check for existing variants before inserting, or use `INSERT IGNORE`.

---

## Status

| # | Issue | Priority | Fixed |
|---|---|---|---|
| 1 | Unvalidated `add_to_cart` POST | High | [x] |
| 2 | INNER JOIN hides variantless products | High | [x] |
| 3 | Omniva live fetch on checkout | Medium | [x] |
| 4 | Missing wishlist view | Medium | [x] |
| 5 | `update_cart` no response | Low | [x] |
| 6 | Duplicate variants on double-submit | Low | [x] |

---

## Products module — Round 2

Found: 2026-05-17

### 7. `cart()` — missing `return` after empty-cart render
**File:** `modules/products/controllers/Products.php` ~line 260  
**Risk:** After rendering the empty-cart template, execution continues — `array_keys([])` runs and `template()` is called a second time, causing a double-render or fatal error.  
**Fix:** Add `return;` after `$this->template('shop_area', $data)` inside the `empty($cart)` block.

---

### 8. `process_order()` — missing table name in `get_where_in()`
**File:** `modules/products/controllers/Products.php` ~line 321  
**Risk:** `$this->model->get_where_in('id', $product_ids)` has no third argument, so it queries the model's default table instead of `products`. Order items record wrong prices or the query fails silently.  
**Fix:** `$this->model->get_where_in('id', $product_ids, 'products')`

---

### 9. `save_product_categories()` — no dedup on create
**File:** `modules/products/controllers/Products.php` ~line 683  
**Risk:** Double-click or retry on product submit inserts duplicate rows in `products_items_categories` for new products. (Variants already fixed in round 1.)  
**Fix:** Check for existing category associations before inserting, mirroring the variant fix.

---

### 10. Unescaped output — `checkout.php` line 25
**File:** `modules/products/views/checkout.php`  
**Risk:** `<?= $product->name ?>` — XSS if product name contains `<script>` or other HTML.  
**Fix:** `<?= out($product->name) ?>`

---

### 11. Unescaped output — `payment_page.php` line 19
**File:** `modules/products/views/payment_page.php`  
**Risk:** `<?= $item->name ?>` — same XSS risk as above.  
**Fix:** `<?= out($item->name) ?>`

---

### 12. `form_submit()` with raw HTML in `checkout.php`
**File:** `modules/products/views/checkout.php` line 75  
**Risk:** Trongate's `form_submit()` escapes the label, so the `<i class="fa fa-check">` icon renders as visible literal text on the button.  
**Fix:** Replace with a plain `<button type="submit">Apmokėti <i class="fa fa-check"></i></button>`.

---

### 13. Four duplicate listing controller methods + views
**File:** `modules/products/controllers/Products.php`  
**Risk:** `index()`, `naujos()`, `surf()`, `beach()` are copy-pasted — identical SQL and logic, only the category slug differs. Four separate views repeat the same grid markup. Any bug fix or style change must be applied four times.  
**Fix:** Consolidate into one private `_listing(string $slug, string $title): void` method and one shared `listing.php` view.

---

### 14. Category IDs hardcoded in `create.php`
**File:** `modules/products/views/create.php` line 34  
**Risk:** `['3' => 'New', '1' => 'Surf', '2' => 'Beach', '4' => 'Best']` — silently breaks if the DB is re-seeded or categories are added/renamed.  
**Fix:** Fetch categories dynamically from `products_categories` in the controller and pass to the view.

---

### 15. Fixed 3 variant slots in `create.php`
**File:** `modules/products/views/create.php`  
**Risk:** Products with more than 3 variants (e.g. XS/S/M/L/XL) cannot be fully entered without editing PHP.  
**Fix:** Add a JS "Add variant" button that appends input rows dynamically.

---

### 16. `$total` used before initialisation in `cart.php`
**File:** `modules/products/views/cart.php`  
**Risk:** `$total +=` inside the foreach loop with no `$total = 0;` before it. Generates a PHP undefined-variable warning and may produce incorrect totals if warnings are suppressed differently.  
**Fix:** Add `$total = 0;` before the foreach.

---

### 17. `payment_page.php` — typo in inline style
**File:** `modules/products/views/payment_page.php` line 2  
**Risk:** `pading-inline:2rem` (missing `d`) — style silently ignored, layout has no side padding.  
**Fix:** `padding-inline:2rem`

---

### 18. Category dropdown always defaults to `'new'` in `create.php`
**File:** `modules/products/views/create.php` line 35  
**Risk:** `$selected_key = 'new'` is hardcoded — when editing an existing product the current categories are not pre-selected in the dropdown.  
**Fix:** Pass the product's current categories from the controller and use them as `$selected_key`.

---

## Status

| # | Issue | Priority | Fixed |
|---|---|---|---|
| 7 | `cart()` double-render on empty cart | High | [x] |
| 8 | `process_order()` missing table name | High | [x] |
| 9 | `save_product_categories()` no dedup | Medium | [x] |
| 10 | XSS — `checkout.php` product name | Medium | [x] |
| 11 | XSS — `payment_page.php` item name | Medium | [x] |
| 12 | `form_submit()` raw HTML icon | Low | [x] |
| 13 | 4 duplicate listing methods/views | Low | [x] |
| 14 | Hardcoded category IDs in `create.php` | Low | [x] |
| 15 | Fixed 3 variant slots in `create.php` | Low | [x] |
| 16 | `$total` undefined in `cart.php` | Low | [x] |
| 17 | Typo `pading-inline` in payment page | Low | [x] |
| 18 | Category dropdown ignores current value | Low | [x] |
