/* ============================================================
   inaffi.com — Outfit Form JavaScript
   ============================================================
   Handles the dynamic product rows in Create / Edit outfit forms.

   Features:
   1.  Add product row (up to MAX_PRODUCTS limit)
   2.  Remove product row
   3.  Re-number rows after add/remove
   4.  Image preview per product row
   5.  Client-side form validation before submit
   6.  Drag-to-reorder product rows (display_order)
   7.  URL validation for affiliate links
   ============================================================
   No dependencies. Pure vanilla JS. ES6+.
   ============================================================ */

'use strict';

/* ============================================================
   CONFIG — injected from PHP via data attributes on the form
   ============================================================ */
const outfitFormEl  = document.getElementById('outfit-form');
if (!outfitFormEl) {
    // Not on the outfit form page — stop here
    throw new Error('outfit-form.js loaded on non-outfit-form page');
}

const MAX_PRODUCTS  = parseInt(outfitFormEl.dataset.maxProducts || '15', 10);
const PLATFORMS     = JSON.parse(outfitFormEl.dataset.platforms  || '[]');

/* ============================================================
   INIT
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    initProductRows();
    initAddProductBtn();
    initFormValidation();
    initDragToReorder();
    updateRowNumbers();
    updateAddBtnVisibility();
});


/* ============================================================
   1. INIT EXISTING PRODUCT ROWS
   ============================================================
   Attach event listeners to rows already rendered by PHP
   (e.g. when editing an existing outfit).
   ============================================================ */
function initProductRows() {
    document.querySelectorAll('.product-row').forEach(row => {
        attachRowListeners(row);
    });
}


/* ============================================================
   2. ADD PRODUCT ROW
   ============================================================ */
function initAddProductBtn() {
    const addBtn = document.getElementById('add-product-btn');
    if (!addBtn) return;

    addBtn.addEventListener('click', () => {
        const rows = document.querySelectorAll('.product-row');
        if (rows.length >= MAX_PRODUCTS) {
            showFormError(`You can add a maximum of ${MAX_PRODUCTS} products per outfit.`);
            return;
        }

        const index   = rows.length;      // 0-based index for input names
        const rowNum  = index + 1;        // 1-based display number
        const row     = buildProductRow(index, rowNum);

        document.getElementById('products-list').appendChild(row);
        attachRowListeners(row);
        updateRowNumbers();
        updateAddBtnVisibility();

        // Scroll the new row into view smoothly
        row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Focus the first input
        row.querySelector('input')?.focus();
    });
}


/* ============================================================
   3. BUILD A NEW PRODUCT ROW (HTML)
   ============================================================ */
function buildProductRow(index, rowNum) {
    const platformOptions = PLATFORMS.map(p =>
        `<option value="${escHtml(p)}">${escHtml(p)}</option>`
    ).join('');

    const row = document.createElement('div');
    row.className = 'product-row';
    row.dataset.rowIndex = String(index);

    row.innerHTML = `
        <div class="product-row__header">
            <span class="product-row__num">Product ${rowNum}</span>
            <button type="button" class="product-row__remove" aria-label="Remove product ${rowNum}">×</button>
        </div>

        <div class="product-row__grid">
            <div class="form-group">
                <label class="form-label">
                    Product Name <span class="required">*</span>
                </label>
                <input
                    type="text"
                    name="products[${index}][name]"
                    class="form-input"
                    placeholder="e.g. Floral Kurti"
                    maxlength="255"
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label">
                    Platform <span class="required">*</span>
                </label>
                <select name="products[${index}][platform]" class="form-select" required>
                    <option value="">Select platform…</option>
                    ${platformOptions}
                </select>
            </div>

            <div class="form-group full-width">
                <label class="form-label">
                    Affiliate Link <span class="required">*</span>
                </label>
                <input
                    type="url"
                    name="products[${index}][affiliate_url]"
                    class="form-input"
                    placeholder="https://www.amazon.in/..."
                    required
                >
                <p class="form-hint">Paste your affiliate URL from Amazon Associates, Flipkart Affiliate, etc.</p>
            </div>

            <div class="form-group">
                <label class="form-label">Price (optional)</label>
                <input
                    type="text"
                    name="products[${index}][price]"
                    class="form-input"
                    placeholder="₹1,499"
                    maxlength="50"
                >
                <p class="form-hint">Stored for your reference — not shown publicly.</p>
            </div>

            <div class="form-group">
                <label class="form-label">Product Image (optional)</label>
                <div class="image-upload-wrap" style="padding: 12px;">
                    <input
                        type="file"
                        name="products[${index}][image]"
                        accept="image/*"
                        data-image-input="product-preview-${index}"
                    >
                    <img
                        id="product-preview-${index}"
                        class="image-upload-preview"
                        src=""
                        alt="Preview"
                        style="display:none; max-height:120px;"
                    >
                    <p class="image-upload-hint">JPG, PNG, WebP — auto-compressed</p>
                </div>
            </div>

            <input type="hidden" name="products[${index}][display_order]" class="display-order-input" value="${index}">
        </div>

        <div class="product-row__stock">
            <label class="toggle-label text-muted text-sm">
                ⓘ Stock status is managed automatically
            </label>
        </div>
    `;

    return row;
}


/* ============================================================
   4. ATTACH LISTENERS TO A ROW
   ============================================================ */
function attachRowListeners(row) {
    // Remove button
    const removeBtn = row.querySelector('.product-row__remove');
    if (removeBtn) {
        removeBtn.addEventListener('click', () => {
            // Must keep at least 1 product
            const rows = document.querySelectorAll('.product-row');
            if (rows.length <= 1) {
                showFormError('An outfit must have at least one product.');
                return;
            }
            row.remove();
            reindexRows();
            updateRowNumbers();
            updateAddBtnVisibility();
        });
    }

    // Image preview
    const fileInput = row.querySelector('input[type="file"]');
    if (fileInput) {
        const previewId = fileInput.dataset.imageInput;
        const preview   = previewId ? document.getElementById(previewId) : null;
        const hint      = row.querySelector('.image-upload-hint');

        if (fileInput && preview) {
            fileInput.addEventListener('change', () => {
                const file = fileInput.files?.[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    showFormError('Please select a valid image file.');
                    fileInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (hint) hint.textContent = file.name;
                };
                reader.readAsDataURL(file);
            });
        }
    }
}


/* ============================================================
   5. RE-INDEX ROWS AFTER REMOVE
   ============================================================
   After a row is removed, re-number the name attributes
   (products[0], products[1], ...) so PHP receives a clean array.
   ============================================================ */
function reindexRows() {
    document.querySelectorAll('.product-row').forEach((row, newIndex) => {
        row.dataset.rowIndex = String(newIndex);

        // Re-map all input/select names
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/products\[\d+\]/, `products[${newIndex}]`);
        });

        // Re-map image preview IDs
        const fileInput = row.querySelector('input[type="file"]');
        if (fileInput) {
            const oldPreviewId = fileInput.dataset.imageInput;
            const newPreviewId = `product-preview-${newIndex}`;

            if (oldPreviewId !== newPreviewId) {
                const preview = document.getElementById(oldPreviewId);
                if (preview) preview.id = newPreviewId;
                fileInput.dataset.imageInput = newPreviewId;
                fileInput.name = `products[${newIndex}][image]`;
            }
        }

        // Update display_order hidden input
        const orderInput = row.querySelector('.display-order-input');
        if (orderInput) orderInput.value = String(newIndex);
    });
}


/* ============================================================
   6. UPDATE ROW NUMBERS (visual labels)
   ============================================================ */
function updateRowNumbers() {
    document.querySelectorAll('.product-row').forEach((row, i) => {
        const label = row.querySelector('.product-row__num');
        if (label) label.textContent = `Product ${i + 1}`;

        const removeBtn = row.querySelector('.product-row__remove');
        if (removeBtn) removeBtn.setAttribute('aria-label', `Remove product ${i + 1}`);
    });
}


/* ============================================================
   7. SHOW / HIDE ADD BUTTON
   ============================================================ */
function updateAddBtnVisibility() {
    const addBtn  = document.getElementById('add-product-btn');
    const maxNote = document.getElementById('max-products-note');
    if (!addBtn) return;

    const count = document.querySelectorAll('.product-row').length;

    if (count >= MAX_PRODUCTS) {
        addBtn.style.display = 'none';
        if (maxNote) maxNote.style.display = 'block';
    } else {
        addBtn.style.display = 'block';
        if (maxNote) maxNote.style.display = 'none';
    }
}


/* ============================================================
   8. CLIENT-SIDE FORM VALIDATION
   ============================================================ */
function initFormValidation() {
    if (!outfitFormEl) return;

    outfitFormEl.addEventListener('submit', (e) => {
        clearFormErrors();

        let valid = true;

        // Title
        const title = outfitFormEl.querySelector('[name="title"]');
        if (title && !title.value.trim()) {
            markFieldError(title, 'Outfit title is required.');
            valid = false;
        }

        // Category
        const category = outfitFormEl.querySelector('[name="category"]');
        if (category && !category.value) {
            markFieldError(category, 'Please select a category.');
            valid = false;
        }

        // Products
        const rows = document.querySelectorAll('.product-row');
        if (rows.length === 0) {
            showFormError('Please add at least one product.');
            valid = false;
        }

        rows.forEach((row, i) => {
            const nameInput = row.querySelector(`[name="products[${i}][name]"]`);
            const urlInput  = row.querySelector(`[name="products[${i}][affiliate_url]"]`);
            const platInput = row.querySelector(`[name="products[${i}][platform]"]`);

            if (nameInput && !nameInput.value.trim()) {
                markFieldError(nameInput, 'Product name is required.');
                valid = false;
            }

            if (platInput && !platInput.value) {
                markFieldError(platInput, 'Please select a platform.');
                valid = false;
            }

            if (urlInput) {
                if (!urlInput.value.trim()) {
                    markFieldError(urlInput, 'Affiliate link is required.');
                    valid = false;
                } else if (!isValidUrl(urlInput.value.trim())) {
                    markFieldError(urlInput, 'Please enter a valid URL starting with http:// or https://');
                    valid = false;
                }
            }
        });

        if (!valid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = outfitFormEl.querySelector('.form-error');
            firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
}

function markFieldError(field, message) {
    field.style.borderColor = 'var(--color-error)';
    const err = document.createElement('p');
    err.className = 'form-error';
    err.textContent = message;
    field.parentNode.appendChild(err);
}

function clearFormErrors() {
    outfitFormEl.querySelectorAll('.form-error').forEach(el => el.remove());
    outfitFormEl.querySelectorAll('[style*="border-color"]').forEach(el => {
        el.style.borderColor = '';
    });
}

function showFormError(message) {
    let errBox = document.getElementById('form-error-box');
    if (!errBox) {
        errBox = document.createElement('div');
        errBox.id = 'form-error-box';
        errBox.className = 'flash flash--error';
        outfitFormEl.prepend(errBox);
    }
    errBox.textContent = message;
    errBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function isValidUrl(str) {
    try {
        const url = new URL(str);
        return url.protocol === 'http:' || url.protocol === 'https:';
    } catch {
        return false;
    }
}


/* ============================================================
   9. DRAG-TO-REORDER
   ============================================================
   Makes product rows draggable so creators can reorder them.
   Updates hidden display_order inputs on drop.
   ============================================================ */
function initDragToReorder() {
    const list = document.getElementById('products-list');
    if (!list) return;

    let dragEl   = null;
    let dragOver = null;

    list.addEventListener('dragstart', (e) => {
        dragEl = e.target.closest('.product-row');
        if (!dragEl) return;
        dragEl.style.opacity = '0.5';
        e.dataTransfer.effectAllowed = 'move';
    });

    list.addEventListener('dragend', () => {
        if (dragEl) dragEl.style.opacity = '';
        dragEl  = null;
        dragOver = null;
        list.querySelectorAll('.product-row').forEach(r => r.classList.remove('drag-over'));
        syncDisplayOrder();
    });

    list.addEventListener('dragover', (e) => {
        e.preventDefault();
        const target = e.target.closest('.product-row');
        if (!target || target === dragEl) return;

        if (dragOver !== target) {
            list.querySelectorAll('.product-row').forEach(r => r.classList.remove('drag-over'));
            target.classList.add('drag-over');
            dragOver = target;
        }

        const rect = target.getBoundingClientRect();
        const mid  = rect.top + rect.height / 2;

        if (e.clientY < mid) {
            list.insertBefore(dragEl, target);
        } else {
            list.insertBefore(dragEl, target.nextSibling);
        }
    });

    // Make rows draggable
    list.querySelectorAll('.product-row').forEach(row => {
        row.setAttribute('draggable', 'true');
    });

    // Observer: when new row added, make it draggable too
    const observer = new MutationObserver(() => {
        list.querySelectorAll('.product-row:not([draggable])').forEach(row => {
            row.setAttribute('draggable', 'true');
        });
    });
    observer.observe(list, { childList: true });
}

function syncDisplayOrder() {
    document.querySelectorAll('.product-row').forEach((row, i) => {
        const orderInput = row.querySelector('.display-order-input');
        if (orderInput) orderInput.value = String(i);
    });
    reindexRows();
    updateRowNumbers();
}


/* ============================================================
   UTILITY
   ============================================================ */
function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}
