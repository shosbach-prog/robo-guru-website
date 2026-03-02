/**
 * RG Document Preview - Enhanced BuddyBoss Document Viewer
 * Features: PDF thumbnails, lightbox preview, zoom controls
 */
(function() {
    'use strict';

    // Wait for PDF.js to load
    if (typeof pdfjsLib === 'undefined') {
        console.warn('PDF.js not loaded, retrying...');
        setTimeout(arguments.callee, 500);
        return;
    }

    // Configure PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = rgDocPreview.pdfWorkerSrc;

    // State
    let currentPdf = null;
    let currentPage = 1;
    let totalPages = 1;
    let currentScale = 1.0;
    let currentUrl = '';
    const processedItems = new Set();

    // DOM Elements (initialized later)
    let lightbox, canvas, ctx;

    /**
     * Find PDF URL from various sources
     */
    function findPdfUrl(item, link) {
        // Try multiple sources for the PDF URL
        const sources = [
            link.getAttribute('data-preview'),
            link.getAttribute('data-full-preview'),
            link.getAttribute('data-text-preview'),
            link.getAttribute('href'),
            item.querySelector('.download_file a')?.href
        ];

        for (const url of sources) {
            if (url && (url.includes('.pdf') || url.includes('download') || url.includes('document'))) {
                return url;
            }
        }
        return null;
    }

    /**
     * Initialize PDF thumbnails for document list
     */
    function initThumbnails() {
        // Find all document items - try multiple selectors for different BuddyBoss versions
        const selectors = [
            '.media-folder_items.ac-document-list',
            '.media-folder_items[data-id]',
            '.document-data-table .media-folder_items',
            '.bp-document-listing .media-folder_items',
            '#bp-media-document-container .media-folder_items',
            '.bb-document-folder .media-folder_items',
            '.document-data-table-head ~ div .media-folder_items',
            // ReadyLaunch grid items
            '.lg-grid-1-5.media-folder_items',
            '.bb-documents-list .media-folder_items',
            // Generic fallback
            'div[data-id][class*="media-folder"]',
            'div[class*="media-folder_items"]'
        ];

        let pdfItems = [];
        for (const sel of selectors) {
            try {
                const found = document.querySelectorAll(sel);
                if (found.length > 0) {
                    pdfItems = found;
                    console.log('RG Doc Preview: Found', found.length, 'items with selector:', sel);
                    break;
                }
            } catch(e) {
                // Invalid selector, skip
            }
        }

        // Also try to find by data-extension attribute
        if (pdfItems.length === 0) {
            const extLinks = document.querySelectorAll('a[data-extension="pdf"]');
            if (extLinks.length > 0) {
                console.log('RG Doc Preview: Found', extLinks.length, 'PDF extension links');
                const items = [];
                extLinks.forEach(link => {
                    const parent = link.closest('.media-folder_items') || link.closest('[data-id]') || link.parentElement?.parentElement;
                    if (parent && !items.includes(parent)) {
                        items.push(parent);
                    }
                });
                pdfItems = items;
            }
        }

        // Debug: log all classes on the page that contain 'document' or 'folder'
        if (pdfItems.length === 0) {
            const allElements = document.querySelectorAll('[class*="document"], [class*="folder"], [class*="media"]');
            const classes = new Set();
            allElements.forEach(el => {
                el.className.split(' ').forEach(c => {
                    if (c.includes('document') || c.includes('folder') || c.includes('media')) {
                        classes.add(c);
                    }
                });
            });
            console.log('RG Doc Preview: Available classes on page:', Array.from(classes).join(', '));
        }

        console.log('RG Doc Preview: Total document items found:', pdfItems.length);

        pdfItems.forEach(item => {
            // Skip if already processed
            const itemId = item.getAttribute('data-id');
            if (itemId && processedItems.has(itemId)) return;

            const link = item.querySelector('.media-folder_name, a[data-extension]');
            if (!link) return;

            // Check extension
            const extension = link.getAttribute('data-extension');
            const title = link.getAttribute('data-document-title') || link.textContent?.trim() || '';
            const isPdf = extension === 'pdf' || title.toLowerCase().endsWith('.pdf');

            if (!isPdf) return;

            // Get PDF URL
            const pdfUrl = findPdfUrl(item, link);
            if (!pdfUrl) {
                console.log('RG Doc Preview: No PDF URL found for', title);
                return;
            }

            console.log('RG Doc Preview: Processing PDF', title, pdfUrl);

            // Mark as processed
            if (itemId) processedItems.add(itemId);

            // Mark as PDF item
            item.setAttribute('data-extension', 'pdf');
            item.classList.add('rg-pdf-item');

            // Check if thumbnail already exists
            if (item.querySelector('.rg-pdf-thumb')) return;

            // Create thumbnail container
            const thumbWrap = document.createElement('div');
            thumbWrap.className = 'rg-pdf-thumb';
            thumbWrap.innerHTML = '<div class="rg-thumb-loading"></div>';

            // Insert at the beginning
            const iconEl = item.querySelector('.media-folder_icon');
            if (iconEl) {
                iconEl.parentNode.insertBefore(thumbWrap, iconEl);
            } else {
                item.insertBefore(thumbWrap, item.firstChild);
            }
            item.classList.add('has-pdf-thumb');

            // Load thumbnail
            loadPdfThumbnail(pdfUrl, thumbWrap, title);

            // Add click handler for lightbox (prevent default theatre)
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const downloadUrl = item.querySelector('.download_file a')?.href || pdfUrl;
                openLightbox(pdfUrl, title || 'PDF Dokument', downloadUrl);
            }, true);
        });
    }

    /**
     * Load PDF thumbnail into element
     */
    async function loadPdfThumbnail(url, container, title) {
        try {
            console.log('RG Doc Preview: Loading thumbnail for', title);

            // Configure PDF.js loading task
            const loadingTask = pdfjsLib.getDocument({
                url: url,
                withCredentials: true // For authenticated documents
            });

            const pdf = await loadingTask.promise;
            const page = await pdf.getPage(1);

            // Calculate scale to fit in thumbnail (max 100px width)
            const desiredWidth = 100;
            const originalViewport = page.getViewport({ scale: 1 });
            const scale = desiredWidth / originalViewport.width;
            const viewport = page.getViewport({ scale });

            const thumbCanvas = document.createElement('canvas');
            thumbCanvas.width = viewport.width;
            thumbCanvas.height = viewport.height;

            const thumbCtx = thumbCanvas.getContext('2d');
            await page.render({
                canvasContext: thumbCtx,
                viewport: viewport
            }).promise;

            container.innerHTML = '';
            container.appendChild(thumbCanvas);
            console.log('RG Doc Preview: Thumbnail loaded for', title);
        } catch (err) {
            console.warn('RG Doc Preview: Failed to load thumbnail for', title, err.message);
            // Show PDF icon as fallback
            container.innerHTML = `
                <div class="rg-pdf-icon">
                    <svg width="32" height="40" viewBox="0 0 32 40" fill="none">
                        <rect x="1" y="1" width="30" height="38" rx="2" fill="#FEE2E2" stroke="#EF4444" stroke-width="2"/>
                        <text x="16" y="24" text-anchor="middle" fill="#EF4444" font-size="10" font-weight="bold">PDF</text>
                    </svg>
                </div>
            `;
        }
    }

    /**
     * Open PDF lightbox
     */
    async function openLightbox(url, title, downloadUrl) {
        if (!lightbox || !canvas) return;

        currentUrl = downloadUrl || url;
        currentPage = 1;
        currentScale = 1.0;

        // Show lightbox
        lightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Set title
        lightbox.querySelector('.rg-pdf-lightbox__title').textContent = title;

        // Show loading
        const loading = lightbox.querySelector('.rg-pdf-lightbox__loading');
        loading.classList.remove('hidden');

        try {
            currentPdf = await pdfjsLib.getDocument(url).promise;
            totalPages = currentPdf.numPages;

            updatePageInfo();
            await renderPage(currentPage);

            loading.classList.add('hidden');
        } catch (err) {
            console.error('Failed to load PDF:', err);
            loading.innerHTML = '<span style="color:#ef4444;">PDF konnte nicht geladen werden</span>';
        }
    }

    /**
     * Close lightbox
     */
    function closeLightbox() {
        if (!lightbox) return;
        lightbox.style.display = 'none';
        document.body.style.overflow = '';
        currentPdf = null;
    }

    /**
     * Render specific page
     */
    async function renderPage(pageNum) {
        if (!currentPdf || !canvas || !ctx) return;

        const page = await currentPdf.getPage(pageNum);
        const viewport = page.getViewport({ scale: currentScale * 1.5 });

        canvas.width = viewport.width;
        canvas.height = viewport.height;

        await page.render({
            canvasContext: ctx,
            viewport: viewport
        }).promise;

        updatePageInfo();
        updateNavButtons();
    }

    /**
     * Update page info display
     */
    function updatePageInfo() {
        const current = lightbox.querySelector('.rg-pdf-page-current');
        const total = lightbox.querySelector('.rg-pdf-page-total');
        const zoom = lightbox.querySelector('.rg-pdf-lightbox__zoom');

        if (current) current.textContent = currentPage;
        if (total) total.textContent = totalPages;
        if (zoom) zoom.textContent = Math.round(currentScale * 100) + '%';
    }

    /**
     * Update navigation button states
     */
    function updateNavButtons() {
        const prevBtn = lightbox.querySelector('.rg-pdf-lightbox__prev');
        const nextBtn = lightbox.querySelector('.rg-pdf-lightbox__next');

        if (prevBtn) prevBtn.disabled = currentPage <= 1;
        if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
    }

    /**
     * Handle lightbox actions
     */
    function handleAction(action) {
        switch (action) {
            case 'close':
                closeLightbox();
                break;
            case 'prev':
                if (currentPage > 1) {
                    currentPage--;
                    renderPage(currentPage);
                }
                break;
            case 'next':
                if (currentPage < totalPages) {
                    currentPage++;
                    renderPage(currentPage);
                }
                break;
            case 'zoom-in':
                if (currentScale < 3) {
                    currentScale += 0.25;
                    renderPage(currentPage);
                }
                break;
            case 'zoom-out':
                if (currentScale > 0.5) {
                    currentScale -= 0.25;
                    renderPage(currentPage);
                }
                break;
            case 'download':
                if (currentUrl) {
                    window.open(currentUrl, '_blank');
                }
                break;
        }
    }

    /**
     * Initialize event listeners
     */
    function initEvents() {
        if (!lightbox) return;

        // Button clicks
        lightbox.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-action]');
            if (btn) {
                e.preventDefault();
                handleAction(btn.getAttribute('data-action'));
            }
        });

        // Backdrop click
        lightbox.querySelector('.rg-pdf-lightbox__backdrop').addEventListener('click', closeLightbox);

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (lightbox.style.display !== 'flex') return;

            switch (e.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                    handleAction('prev');
                    break;
                case 'ArrowRight':
                    handleAction('next');
                    break;
                case '+':
                case '=':
                    handleAction('zoom-in');
                    break;
                case '-':
                    handleAction('zoom-out');
                    break;
            }
        });
    }

    /**
     * Watch for dynamic content (AJAX loaded documents)
     */
    function initMutationObserver() {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    // Re-init thumbnails when new content is added
                    setTimeout(initThumbnails, 100);
                }
            });
        });

        const container = document.querySelector('.document-data-table') || document.body;
        observer.observe(container, {
            childList: true,
            subtree: true
        });
    }

    /**
     * Initialize on DOM ready
     */
    function init() {
        console.log('RG Doc Preview: Initializing...');

        // Initialize lightbox elements
        lightbox = document.getElementById('rg-pdf-lightbox');
        canvas = document.getElementById('rg-pdf-canvas');
        ctx = canvas ? canvas.getContext('2d') : null;

        initThumbnails();
        initEvents();
        initMutationObserver();

        console.log('RG Doc Preview: Initialized');
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // Small delay to ensure all elements are rendered
        setTimeout(init, 100);
    }

    // Also run after delays to catch AJAX content
    setTimeout(init, 1000);
    setTimeout(init, 3000);

    // Listen for BuddyBoss AJAX events
    document.addEventListener('bp_document_page_updated', init);
    jQuery && jQuery(document).on('bp_ajax_request_completed', init);
})();
