/* ============================================================
   inaffi.com — Main JavaScript
   ============================================================
   Handles all global UI interactions:
   1.  Mobile sidebar (dashboard hamburger menu)
   2.  Category filter (storefront + dashboard)
   3.  Copy-to-clipboard (outfit share links)
   4.  Stock toggle (AJAX — dashboard product in/out of stock)
   5.  Flash message auto-dismiss
   6.  Image upload preview
   7.  Confirm before delete
   8.  Admin — color pickers live preview
   9.  Admin — tag add/remove (platforms & categories)
   10. Outfit publish/draft toggle (AJAX)
   11. Outfit featured toggle (AJAX)
   ============================================================
   No dependencies. Pure vanilla JS. ES6+.
   ============================================================ */

'use strict';

/* ============================================================
   INIT — run after DOM is ready
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    initMobileSidebar();
    initCategoryFilter();
    initCopyLinks();
    initStockToggles();
    initFlashDismiss();
    initImagePreviews();
    initDeleteConfirms();
    initColorPickers();
    initTagManager();
    initPublishToggles();
    initFeaturedToggles();
});


/* ============================================================
   1. MOBILE SIDEBAR
   ============================================================
   Hamburger button opens/closes the dark sidebar on mobile.
   Clicking the overlay also closes it.
   ============================================================ */
function initMobileSidebar() {
    const hamburger = document.getElementById('hamburger-btn');
    const sidebar   = document.getElementById('dashboard-sidebar');
    const overlay   = document.getElementById('sidebar-overlay');

    if (!hamburger || !sidebar) return;

    function openSidebar() {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('active');
        hamburger.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
        hamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', () => {
        const isOpen = sidebar.classList.contains('open');
        isOpen ? closeSidebar() : openSidebar();
    });

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close on nav link click (route change)
    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
    });
}


/* ============================================================
   2. CATEGORY FILTER
   ============================================================
   Filter pills that show/hide outfit cards or outfit list rows
   by category. Works on both storefront and dashboard.
   No page reload — pure JS DOM filtering.
   ============================================================ */
function initCategoryFilter() {
    const filterList = document.querySelector('[data-filter-list]');
    if (!filterList) return;

    const pills    = filterList.querySelectorAll('[data-filter-pill]');
    const items    = document.querySelectorAll('[data-category]');
    const countEl  = document.getElementById('outfit-count');

    if (!pills.length || !items.length) return;

    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            // Update active pill
            pills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');

            const selected = pill.dataset.filterPill;

            let visible = 0;
            items.forEach(item => {
                const match = selected === 'All' || item.dataset.category === selected;
                item.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            // Update count label if present
            if (countEl) {
                const label = selected === 'All' ? 'outfits' : selected + ' outfits';
                countEl.textContent = `${visible} ${label}`;
            }
        });
    });
}


/* ============================================================
   3. COPY TO CLIPBOARD
   ============================================================
   Any element with data-copy-btn and data-copy-target="#inputId"
   copies the input value to clipboard on click.
   ============================================================ */
function initCopyLinks() {
    document.querySelectorAll('[data-copy-btn]').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.dataset.copyTarget;
            const input    = targetId ? document.querySelector(targetId) : null;
            const text     = input ? input.value : (btn.dataset.copyText || '');

            if (!text) return;

            // Modern clipboard API
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    flashCopySuccess(btn);
                }).catch(() => fallbackCopy(text, btn));
            } else {
                fallbackCopy(text, btn);
            }
        });
    });
}

function fallbackCopy(text, btn) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0;';
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand('copy');
        flashCopySuccess(btn);
    } catch (e) {
        console.warn('Copy failed:', e);
    }
    document.body.removeChild(ta);
}

function flashCopySuccess(btn) {
    const original = btn.textContent;
    btn.textContent = 'Copied!';
    btn.disabled = true;
    setTimeout(() => {
        btn.textContent = original;
        btn.disabled = false;
    }, 2000);
}


/* ============================================================
   4. STOCK TOGGLE (AJAX)
   ============================================================
   Toggle switches on dashboard edit page update in_stock
   status via AJAX POST — no full page reload needed.
   ============================================================ */
function initStockToggles() {
    document.querySelectorAll('[data-stock-toggle]').forEach(toggle => {
        toggle.addEventListener('change', async () => {
            const productId = toggle.dataset.stockToggle;
            const inStock   = toggle.checked ? 1 : 0;
            const label     = toggle.closest('.toggle-wrap')?.querySelector('.toggle-label');

            toggle.disabled = true;

            try {
                const res = await fetch('/ajax/toggle-stock.php', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body:    `product_id=${encodeURIComponent(productId)}&in_stock=${inStock}&csrf_token=${getCsrfToken()}`,
                });

                const data = await res.json();

                if (!data.ok) {
                    // Revert on failure
                    toggle.checked = !toggle.checked;
                    showToast('Could not update stock status. Please try again.', 'error');
                } else {
                    if (label) label.textContent = inStock ? 'In Stock' : 'Out of Stock';
                }
            } catch (err) {
                toggle.checked = !toggle.checked;
                showToast('Network error. Please try again.', 'error');
            } finally {
                toggle.disabled = false;
            }
        });
    });
}


/* ============================================================
   5. FLASH MESSAGE AUTO-DISMISS
   ============================================================
   Flash messages fade out after 5 seconds automatically.
   ============================================================ */
function initFlashDismiss() {
    document.querySelectorAll('.flash').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 500);
        }, 5000);
    });
}


/* ============================================================
   6. IMAGE UPLOAD PREVIEW
   ============================================================
   When a file input changes, shows a preview of the selected
   image before the form is submitted.
   ============================================================ */
function initImagePreviews() {
    document.querySelectorAll('[data-image-input]').forEach(input => {
        const previewId = input.dataset.imageInput;
        const preview   = document.getElementById(previewId);
        const hint      = input.closest('.image-upload-wrap')?.querySelector('.image-upload-hint');

        if (!preview) return;

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file) return;

            // Validate type client-side (server also validates)
            if (!file.type.startsWith('image/')) {
                showToast('Please select an image file (JPG, PNG, WebP).', 'error');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (hint) hint.textContent = file.name + ' selected';
            };
            reader.readAsDataURL(file);
        });
    });
}


/* ============================================================
   7. CONFIRM BEFORE DELETE
   ============================================================
   Any button/link with data-confirm="message" shows a
   confirmation dialog before proceeding.
   ============================================================ */
function initDeleteConfirms() {
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            const msg = el.dataset.confirm || 'Are you sure?';
            if (!window.confirm(msg)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
}


/* ============================================================
   8. ADMIN — COLOR PICKERS LIVE PREVIEW
   ============================================================
   Color picker inputs on /admin page update CSS custom
   properties in real time so the admin can preview changes.
   ============================================================ */
function initColorPickers() {
    document.querySelectorAll('[data-color-var]').forEach(input => {
        if (input.type !== 'color') return;

        const cssVar = input.dataset.colorVar;

        // Apply immediately on load
        document.documentElement.style.setProperty(cssVar, input.value);

        // Apply on change
        input.addEventListener('input', () => {
            document.documentElement.style.setProperty(cssVar, input.value);
        });
    });
}


/* ============================================================
   9. ADMIN — TAG MANAGER (Platforms & Categories)
   ============================================================
   Add/remove items from the platforms or categories list.
   Submits via hidden inputs to the admin form.
   ============================================================ */
function initTagManager() {
    document.querySelectorAll('[data-tag-manager]').forEach(container => {
        const addBtn   = container.querySelector('[data-tag-add]');
        const addInput = container.querySelector('[data-tag-input]');
        const tagList  = container.querySelector('[data-tag-list]');
        const hiddenContainer = container.querySelector('[data-tag-hidden]');

        if (!addBtn || !addInput || !tagList) return;

        addBtn.addEventListener('click', () => {
            const val = addInput.value.trim();
            if (!val) return;

            // Check for duplicate
            const existing = [...tagList.querySelectorAll('[data-tag-value]')]
                .map(el => el.dataset.tagValue.toLowerCase());
            if (existing.includes(val.toLowerCase())) {
                showToast(`"${val}" already exists.`, 'error');
                return;
            }

            addTag(val, tagList, hiddenContainer);
            addInput.value = '';
            addInput.focus();
        });

        // Allow Enter key to add
        addInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addBtn.click();
            }
        });

        // Init remove buttons on existing tags
        tagList.querySelectorAll('[data-tag-remove]').forEach(btn => {
            btn.addEventListener('click', () => removeTag(btn, hiddenContainer));
        });
    });
}

function addTag(value, tagList, hiddenContainer) {
    // Create tag pill
    const tag = document.createElement('span');
    tag.className = 'tag-item';
    tag.dataset.tagValue = value;
    tag.innerHTML = `
        ${escapeHtml(value)}
        <button type="button" class="tag-item__remove" data-tag-remove aria-label="Remove ${escapeHtml(value)}">×</button>
    `;
    tagList.appendChild(tag);

    // Create hidden input for form submission
    if (hiddenContainer) {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = hiddenContainer.dataset.tagHidden + '[]';
        input.value = value;
        input.dataset.tagFor = value;
        hiddenContainer.appendChild(input);
    }

    // Attach remove handler
    tag.querySelector('[data-tag-remove]').addEventListener('click', () => {
        removeTag(tag.querySelector('[data-tag-remove]'), hiddenContainer);
    });
}

function removeTag(btn, hiddenContainer) {
    const tagItem = btn.closest('[data-tag-value]');
    if (!tagItem) return;
    const value = tagItem.dataset.tagValue;

    tagItem.remove();

    if (hiddenContainer) {
        const hiddenInput = hiddenContainer.querySelector(`[data-tag-for="${CSS.escape(value)}"]`);
        if (hiddenInput) hiddenInput.remove();
    }
}


/* ============================================================
   10. PUBLISH / DRAFT TOGGLE (AJAX)
   ============================================================
   Toggle button on dashboard outfit list that switches
   an outfit between published (live) and draft (hidden).
   ============================================================ */
function initPublishToggles() {
    document.querySelectorAll('[data-publish-toggle]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const outfitId  = btn.dataset.publishToggle;
            const published = btn.dataset.published === '1';
            const newState  = published ? 0 : 1;

            btn.disabled = true;
            btn.textContent = '…';

            try {
                const res = await fetch('/ajax/toggle-publish.php', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body:    `outfit_id=${encodeURIComponent(outfitId)}&published=${newState}&csrf_token=${getCsrfToken()}`,
                });

                const data = await res.json();

                if (data.ok) {
                    btn.dataset.published = String(newState);
                    btn.textContent       = newState ? 'Published' : 'Draft';
                    btn.className         = newState
                        ? 'btn btn--sm btn--outline'
                        : 'btn btn--sm btn--ghost';
                    showToast(newState ? 'Outfit is now live!' : 'Outfit moved to draft.', 'success');
                } else {
                    showToast(data.message || 'Could not update. Please try again.', 'error');
                    btn.textContent = published ? 'Published' : 'Draft';
                }
            } catch (err) {
                showToast('Network error. Please try again.', 'error');
                btn.textContent = published ? 'Published' : 'Draft';
            } finally {
                btn.disabled = false;
            }
        });
    });
}


/* ============================================================
   11. FEATURED TOGGLE (AJAX)
   ============================================================
   Star button on outfit edit form / dashboard list that
   marks an outfit as featured on the homepage.
   ============================================================ */
function initFeaturedToggles() {
    document.querySelectorAll('[data-featured-toggle]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const outfitId = btn.dataset.featuredToggle;
            const featured = btn.dataset.featured === '1';
            const newState = featured ? 0 : 1;

            btn.disabled = true;

            try {
                const res = await fetch('/ajax/toggle-featured.php', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body:    `outfit_id=${encodeURIComponent(outfitId)}&featured=${newState}&csrf_token=${getCsrfToken()}`,
                });

                const data = await res.json();

                if (data.ok) {
                    btn.dataset.featured = String(newState);
                    // Update all other featured buttons (only one can be active)
                    if (newState === 1) {
                        document.querySelectorAll('[data-featured-toggle]').forEach(other => {
                            if (other !== btn) {
                                other.dataset.featured = '0';
                                updateFeaturedBtnStyle(other, false);
                            }
                        });
                    }
                    updateFeaturedBtnStyle(btn, newState === 1);
                    showToast(
                        newState ? '⭐ Featured on homepage!' : 'Removed from homepage.',
                        'success'
                    );
                } else {
                    showToast(data.message || 'Could not update.', 'error');
                }
            } catch (err) {
                showToast('Network error. Please try again.', 'error');
            } finally {
                btn.disabled = false;
            }
        });
    });
}

function updateFeaturedBtnStyle(btn, isFeatured) {
    if (isFeatured) {
        btn.textContent = '⭐ Featured';
        btn.className   = 'btn btn--sm btn--gold';
    } else {
        btn.textContent = '☆ Feature';
        btn.className   = 'btn btn--sm btn--ghost';
    }
}


/* ============================================================
   SHARED UTILITIES
   ============================================================ */

/**
 * Get the CSRF token from the page meta tag or hidden input.
 */
function getCsrfToken() {
    const meta  = document.querySelector('meta[name="csrf-token"]');
    const input = document.querySelector('input[name="csrf_token"]');
    return meta?.content || input?.value || '';
}

/**
 * Show a transient toast notification at the bottom of the screen.
 * type: 'success' | 'error' | 'info'
 */
function showToast(message, type = 'success') {
    // Remove any existing toast
    document.querySelector('.toast')?.remove();

    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.textContent = message;
    toast.setAttribute('role', 'alert');

    // Inline styles so toast works without extra CSS class
    Object.assign(toast.style, {
        position:     'fixed',
        bottom:       '24px',
        left:         '50%',
        transform:    'translateX(-50%) translateY(0)',
        background:   type === 'error' ? '#9B2335' : type === 'info' ? '#1A5276' : '#2D6A4F',
        color:        '#FFFFFF',
        padding:      '10px 20px',
        borderRadius: '4px',
        fontSize:     '0.9375rem',
        fontFamily:   'var(--font-body)',
        boxShadow:    '0 4px 12px rgba(0,0,0,0.2)',
        zIndex:       '9999',
        transition:   'opacity 0.3s ease',
        whiteSpace:   'nowrap',
        maxWidth:     'calc(100vw - 48px)',
    });

    document.body.appendChild(toast);

    // Auto remove after 3.5s
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

/**
 * Escape HTML for safe insertion into DOM.
 */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
