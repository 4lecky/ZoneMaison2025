document.addEventListener('DOMContentLoaded', () => {
    // "Ver más/Ver menos" 
    document.querySelectorAll('.btn-vermas').forEach(btn => {
        const parent = btn.closest('.Descripcion');
        const p = parent ? parent.querySelector('.texto-muro') : null;

        if (!p) return;

        // Check if text is truncated
        if (p.scrollHeight <= p.offsetHeight) {
            btn.style.display = 'none';
            return;
        }

        btn.addEventListener('click', () => {
            const isExpanded = p.classList.contains('expanded');
            
            if (isExpanded) {
                p.classList.remove('expanded');
                btn.querySelector('.text').textContent = 'Ver más';
            } else {
                p.classList.add('expanded');
                btn.querySelector('.text').textContent = 'Ver menos';
            }
        });
    });

    // Edit button confirmation
    const editButtons = document.querySelectorAll('.edit-button');
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const confirmed = confirm('¿Estás seguro de que quieres editar esta publicación?');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });

    // Image modal functionality
    initializeImageModal();
    
    // Add loading effect to images
    addImageLoadingEffect();
});

// Function to confirm deletion
function confirmarEliminacion(id) {
    if (confirm('¿Estás seguro de que quieres eliminar esta publicación? Esta acción no se puede deshacer.')) {
        window.location.href = 'eliminar_publicacion.php?id=' + id;
    }
}

// Initialize image modal functionality
function initializeImageModal() {
    // Get modal elements
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    const closeBtn = document.querySelector('.image-modal-close');
    const downloadBtn = document.getElementById('downloadBtn');
    
    // Make all images clickable
    const images = document.querySelectorAll('.tarjeta img');
    
    images.forEach(function(img) {
        img.addEventListener('click', function() {
            openImageModal(this);
        });
        
        // Add attributes to indicate it's clickable
        img.style.cursor = 'pointer';
        img.title = 'Clic para ampliar';
    });
    
    // Function to open modal
    function openImageModal(img) {
        if (!modal || !modalImg || !modalCaption) return;
        
        modal.style.display = 'block';
        modalImg.src = img.src;
        
        // Create caption with information
        let caption = img.alt || 'Imagen';
        
        // Look for additional information from context
        const tarjeta = img.closest('.tarjeta');
        if (tarjeta) {
            const asunto = tarjeta.querySelector('.Asunto');
            if (asunto) {
                caption = asunto.textContent.trim();
            }
        }
        
        modalCaption.textContent = caption;
        
        // Configure download button
        if (downloadBtn) {
            downloadBtn.onclick = function() {
                downloadImage(img.src, caption);
            };
        }
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }
    
    // Function to close modal
    function closeImageModal() {
        if (!modal) return;
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Reset zoom
        if (modalImg) {
            modalImg.style.transform = 'translate(-50%, -50%) scale(1)';
        }
    }
    
    // Event listeners to close modal
    if (closeBtn) {
        closeBtn.addEventListener('click', closeImageModal);
    }
    
    // Close when clicking outside the image
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeImageModal();
            }
        });
    }
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'block') {
            closeImageModal();
        }
    });
    
    // Function to download image
    function downloadImage(imageSrc, fileName) {
        // Create temporary link
        const link = document.createElement('a');
        link.href = imageSrc;
        link.download = fileName.replace(/[^a-z0-9]/gi, '_') + '.jpg';
        
        // Simulate click to download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Keyboard navigation (optional)
    document.addEventListener('keydown', function(e) {
        if (modal && modal.style.display === 'block' && modalImg) {
            const currentImg = modalImg.src;
            const allImages = Array.from(images);
            const currentIndex = allImages.findIndex(img => img.src === currentImg);
            
            if (e.key === 'ArrowLeft' && currentIndex > 0) {
                // Previous image
                openImageModal(allImages[currentIndex - 1]);
            } else if (e.key === 'ArrowRight' && currentIndex < allImages.length - 1) {
                // Next image
                openImageModal(allImages[currentIndex + 1]);
            }
        }
    });
    
    // Zoom with mouse wheel (optional)
    if (modalImg) {
        modalImg.addEventListener('wheel', function(e) {
            e.preventDefault();
            
            const scale = modalImg.style.transform.match(/scale\(([^)]*)\)/);
            let currentScale = scale ? parseFloat(scale[1]) : 1;
            
            if (e.deltaY < 0) {
                // Zoom in
                currentScale = Math.min(currentScale * 1.1, 3);
            } else {
                // Zoom out
                currentScale = Math.max(currentScale * 0.9, 0.5);
            }
            
            modalImg.style.transform = `translate(-50%, -50%) scale(${currentScale})`;
        });
    }
}

// Function to add loading effect to images
function addImageLoadingEffect() {
    const images = document.querySelectorAll('.tarjeta img');
    
    images.forEach(function(img) {
        // Skip if image is already loaded
        if (img.complete) return;
        
        // Create loading overlay
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'image-loading-overlay';
        loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';
        loadingOverlay.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            z-index: 10;
        `;
        
        // Create spinner
        const spinner = loadingOverlay.querySelector('.loading-spinner');
        spinner.style.cssText = `
            width: 30px;
            height: 30px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #c49e78;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        `;
        
        // Add spinner animation
        if (!document.querySelector('#spinner-style')) {
            const style = document.createElement('style');
            style.id = 'spinner-style';
            style.textContent = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Insert overlay
        const parent = img.parentNode;
        if (parent) {
            parent.style.position = 'relative';
            parent.appendChild(loadingOverlay);
        }
        
        // Hide overlay when image loads
        img.addEventListener('load', function() {
            setTimeout(() => {
                loadingOverlay.style.opacity = '0';
                loadingOverlay.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    if (loadingOverlay.parentNode) {
                        loadingOverlay.parentNode.removeChild(loadingOverlay);
                    }
                }, 300);
            }, 100);
        });
        
        // Handle error case
        img.addEventListener('error', function() {
            if (loadingOverlay.parentNode) {
                loadingOverlay.innerHTML = '<div style="color: #666; font-size: 12px;">Error al cargar</div>';
                setTimeout(() => {
                    if (loadingOverlay.parentNode) {
                        loadingOverlay.parentNode.removeChild(loadingOverlay);
                    }
                }, 2000);
            }
        });
    });
}

// Utility function to safely execute code when DOM is ready
function domReady(fn) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fn);
    } else {
        fn();
    }
}

