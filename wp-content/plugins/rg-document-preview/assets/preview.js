/**
 * RG Document Preview - Enhanced BuddyBoss Document Viewer
 * Features: PDF thumbnails, lightbox preview, zoom controls
 */
(function() {
    'use strict';

    // Wait for PDF.js to load
    if (typeof pdfjsLib === 'undefined') {
        console.warn('PDF.js not loaded');
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

    // DOM Elements
    const lightbox = document.getElementById('rg-pdf-lightbox');
    const canvas = document.getElementById('rg-pdf-canvas');
    const ctx = canvas ? canvas.getContext('2d') : null;

    /**
     * Initialize PDF thumbnails for document list
     */
    function initThumbnails() {
        const pdfItems = document.querySelectorAll('.media-folder_items.ac-document-list');

        pdfItems.forEach(item => {
            const link = item.querySelector('.media-folder_name');
            if (!link) return;

            const extension = link.getAttribute('data-extension');
            if (extension !== 'pdf') return;

            // Get PDF URL
            const pdfUrl = link.getAttribute('data-preview') || link.getAttribute('href');
            if (!pdfUrl || !pdfUrl.includes('.pdf')) return;

            // Mark as PDF item
            item.setAttribute('data-extension', 'pdf');

            // Create thumbnail container
            const thumbWrap = document.createElement('div');
            thumbWrap.className = 'rg-pdf-thumb';
            thumbWrap.innerHTML = '<div class="rg-thumb-loading"></div>';
            item.insertBefore(thumbWrap, item.firstChild);
            item.classList.add('has-pdf-thumb');

            // Load thumbnail
            loadPdfThumbnail(pdfUrl, thumbWrap);

            // Add click handler for lightbox
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const title = link.getAttribute('data-document-title') || 'PDF Dokument';
                const downloadUrl = item.querySelector('.download_file a')?.href || pdfUrl;
                openLightbox(pdfUrl, title, downloadUrl);
            });
        });
    }

    /**
     * Load PDF thumbnail into element
     */
    async function loadPdfThumbnail(url, container) {
        try {
            const pdf = await pdfjsLib.getDocument(url).promise;
            const page = await pdf.getPage(1);

            const scale = 0.3;
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
        } catch (err) {
            console.warn('Failed to load PDF thumbnail:', err);
            container.innerHTML = '<i class="bb-icon-l bb-icon-file-pdf" style="font-size:24px;color:#ef4444;"></i>';
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
        initThumbnails();
        initEvents();
        initMutationObserver();
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Also run after a delay to catch AJAX content
    setTimeout(init, 1000);
})();
