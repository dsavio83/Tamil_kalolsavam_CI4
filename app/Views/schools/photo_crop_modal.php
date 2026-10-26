<!-- Photo Crop & Upload System (Scoped to Registration Form) -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/vendor/cropperjs/cropper.min.css">
<style>
/* 150x200 3:4 Aspect Ratio Photo Box */
.photo-preview-box {
    width: 150px;
    height: 200px;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transition: border-color 0.2s, box-shadow 0.2s;
    margin: 0 auto;
}
.photo-preview-box:hover {
    border-color: #0d6efd;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.12);
}
.photo-preview-box.photo-required-error {
    border-color: #dc3545;
    background-color: #fff5f5;
    box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.18);
    animation: photoRequiredPulse 1.1s ease-in-out 2;
}
.photo-preview-box.photo-required-error .photo-preview-placeholder {
    color: #dc3545;
}
@keyframes photoRequiredPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
.photo-preview-img {
    width: 150px;
    height: 200px;
    object-fit: cover;
    display: none;
}
.photo-preview-placeholder {
    font-size: 0.8rem;
    color: #6c757d;
    text-align: center;
    padding: 10px;
}
.photo-crop-container {
    max-height: 420px;
    background-color: #1a1a1a;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 6px;
}
.photo-crop-container img {
    max-width: 100%;
    max-height: 400px;
    display: block;
}
@media (max-width: 576px) {
    .photo-crop-container {
        max-height: 300px;
    }
    .photo-crop-container img {
        max-height: 280px;
    }
}
.cropper-view-box,
.cropper-face {
    border-radius: 4px;
}
</style>

<!-- Crop Modal -->
<div class="modal fade" id="photoCropModal" tabindex="-1" aria-labelledby="photoCropModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h5 class="modal-title fs-6 fw-bold mb-0" id="photoCropModalLabel">
                    <i class="bi bi-crop me-2"></i>Crop Photo (3:4 Ratio &bull; 150 &times; 200)
                </h5>
                <button type="button" class="btn-close btn-close-white" aria-label="Close" onclick="cancelPhotoCrop()"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Cropper Viewport -->
                <div class="photo-crop-container mb-3 shadow-inner">
                    <img id="cropperSourceImage" src="" alt="Crop Area">
                </div>

                <!-- Control Toolbar -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 bg-light p-2 rounded border">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Zoom controls">
                        <button type="button" class="btn btn-outline-secondary" id="btnCropZoomIn" title="Zoom In">
                            <i class="bi bi-zoom-in"></i> Zoom In
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCropZoomOut" title="Zoom Out">
                            <i class="bi bi-zoom-out"></i> Zoom Out
                        </button>
                    </div>

                    <div class="btn-group btn-group-sm" role="group" aria-label="Rotate controls">
                        <button type="button" class="btn btn-outline-secondary" id="btnCropRotateLeft" title="Rotate Left 90°">
                            <i class="bi bi-arrow-counterclockwise"></i> Rotate Left
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCropRotateRight" title="Rotate Right 90°">
                            <i class="bi bi-arrow-clockwise"></i> Rotate Right
                        </button>
                    </div>

                    <div>
                        <button type="button" class="btn btn-sm btn-outline-warning" id="btnCropReset" title="Reset Image">
                            <i class="bi bi-arrow-repeat"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- Info note -->
                <div class="d-flex align-items-center justify-content-between mt-2 px-1">
                    <small class="text-muted" id="cropResolutionInfo">
                        <i class="bi bi-info-circle me-1"></i>Move and zoom to adjust the photo within the 3:4 crop box.
                    </small>
                    <span class="badge bg-secondary" id="sourceDimensionBadge">3:4 Aspect</span>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-3 justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelPhotoCrop()">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-sm btn-success fw-bold px-3" id="btnApplyCrop">
                    <i class="bi bi-check2-circle me-1"></i>Crop &amp; Continue
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/vendor/cropperjs/cropper.min.js"></script>
<script>
(function() {
    var cropperInstance = null;
    var cropModalEl = null;
    var cropModal = null;
    var lastSourceDataUrl = null;
    var MIN_WIDTH = 300;
    var MIN_HEIGHT = 400;

    function initElements() {
        cropModalEl = document.getElementById('photoCropModal');
        if (cropModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            cropModal = bootstrap.Modal.getOrCreateInstance(cropModalEl);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initElements);
    } else {
        initElements();
    }

    // Trigger Photo Input from Camera
    window.triggerCameraCapture = function() {
        var camInput = document.getElementById('cameraInput');
        if (camInput) {
            camInput.value = '';
            camInput.click();
        }
    };

    // Trigger Photo Input from Gallery / Files
    window.triggerGallerySelection = function() {
        var gallInput = document.getElementById('galleryInput');
        if (gallInput) {
            gallInput.value = '';
            gallInput.click();
        }
    };

    // Re-open cropper with the existing loaded image
    window.reCropCurrentPhoto = function() {
        if (lastSourceDataUrl) {
            openCropperWithDataUrl(lastSourceDataUrl);
        } else {
            var currentPhoto = document.getElementById('currentPhoto');
            if (currentPhoto && currentPhoto.src && currentPhoto.style.display !== 'none') {
                openCropperWithDataUrl(currentPhoto.src);
            } else {
                window.triggerGallerySelection();
            }
        }
    };

    // Handle File Selection (from camera or gallery)
    window.handlePhotoSelected = function(input) {
        if (!input || !input.files || !input.files[0]) {
            return;
        }

        var file = input.files[0];

        // 1. Validate File Type
        var validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type.toLowerCase())) {
            var ext = (file.name.split('.').pop() || '').toLowerCase();
            if (!['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                showError('Invalid file type. Please select a JPG, PNG, or WEBP photo.');
                input.value = '';
                return;
            }
        }

        // 2. Read and Validate Dimensions
        var reader = new FileReader();
        reader.onload = function(e) {
            var dataUrl = e.target.result;
            var testImg = new Image();
            testImg.onload = function() {
                // Minimum source resolution check (Rule 11: 300x400)
                if (testImg.naturalWidth < MIN_WIDTH || testImg.naturalHeight < MIN_HEIGHT) {
                    showError('The selected photo resolution is too low (' + testImg.naturalWidth + 'x' + testImg.naturalHeight + ' px).\nPlease select a photo of at least 300x400 px.');
                    input.value = '';
                    return;
                }

                lastSourceDataUrl = dataUrl;
                openCropperWithDataUrl(dataUrl, testImg.naturalWidth, testImg.naturalHeight);
            };
            testImg.onerror = function() {
                showError('Could not load or read the selected image file. Please try another photo.');
                input.value = '';
            };
            testImg.src = dataUrl;
        };
        reader.onerror = function() {
            showError('Failed to read the selected file.');
            input.value = '';
        };
        reader.readAsDataURL(file);
    };

    function openCropperWithDataUrl(dataUrl, naturalWidth, naturalHeight) {
        var cropImage = document.getElementById('cropperSourceImage');
        if (!cropImage) return;

        // Destroy prior cropper instance if exists
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }

        cropImage.src = dataUrl;

        // Update badge
        var badge = document.getElementById('sourceDimensionBadge');
        if (badge && naturalWidth && naturalHeight) {
            badge.textContent = naturalWidth + ' \u00D7 ' + naturalHeight + ' px (Source)';
        }

        if (!cropModal) {
            initElements();
        }

        if (cropModal) {
            cropModal.show();
        } else if (typeof window.jQuery !== 'undefined' && cropModalEl) {
            window.jQuery(cropModalEl).modal('show');
        }

        // Initialize Cropper once modal is visible
        setTimeout(function() {
            if (cropperInstance) {
                cropperInstance.destroy();
            }

            cropperInstance = new Cropper(cropImage, {
                aspectRatio: 3 / 4, // STRICT 3:4 FIXED RATIO
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.92,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                checkOrientation: true,
                responsive: true
            });
        }, 300);
    }

    // Cancel Crop Operation
    window.cancelPhotoCrop = function() {
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
        var camInput = document.getElementById('cameraInput');
        var gallInput = document.getElementById('galleryInput');
        if (camInput) camInput.value = '';
        if (gallInput) gallInput.value = '';

        if (cropModal) {
            cropModal.hide();
        } else if (typeof window.jQuery !== 'undefined' && cropModalEl) {
            window.jQuery(cropModalEl).modal('hide');
        }
    };

    // Toolbar Buttons
    document.addEventListener('DOMContentLoaded', function() {
        var btnZoomIn = document.getElementById('btnCropZoomIn');
        if (btnZoomIn) {
            btnZoomIn.addEventListener('click', function() {
                if (cropperInstance) cropperInstance.zoom(0.1);
            });
        }
        var btnZoomOut = document.getElementById('btnCropZoomOut');
        if (btnZoomOut) {
            btnZoomOut.addEventListener('click', function() {
                if (cropperInstance) cropperInstance.zoom(-0.1);
            });
        }
        var btnRotateLeft = document.getElementById('btnCropRotateLeft');
        if (btnRotateLeft) {
            btnRotateLeft.addEventListener('click', function() {
                if (cropperInstance) cropperInstance.rotate(-90);
            });
        }
        var btnRotateRight = document.getElementById('btnCropRotateRight');
        if (btnRotateRight) {
            btnRotateRight.addEventListener('click', function() {
                if (cropperInstance) cropperInstance.rotate(90);
            });
        }
        var btnReset = document.getElementById('btnCropReset');
        if (btnReset) {
            btnReset.addEventListener('click', function() {
                if (cropperInstance) cropperInstance.reset();
            });
        }

        // Apply Crop & Continue
        var btnApply = document.getElementById('btnApplyCrop');
        if (btnApply) {
            btnApply.addEventListener('click', function() {
                if (!cropperInstance) return;

                // Export canvas with 3:4 aspect ratio (300x400 for high quality resampling to 150x200)
                var canvas = cropperInstance.getCroppedCanvas({
                    width: 300,
                    height: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                if (!canvas) {
                    showError('Failed to generate cropped image. Please try again.');
                    return;
                }

                // Generate JPEG data URL
                var croppedDataUrl = canvas.toDataURL('image/jpeg', 0.90);

                // Set hidden form field
                var hiddenField = document.getElementById('cropped_image_data');
                if (hiddenField) {
                    hiddenField.value = croppedDataUrl;
                }

                // Update 150x200 Preview
                var currentPhoto = document.getElementById('currentPhoto');
                var noPhotoMessage = document.getElementById('noPhotoMessage');
                var recropBtn = document.getElementById('btnRecropPhoto');
                var removeBtn = document.getElementById('btnRemovePhoto');

                if (currentPhoto) {
                    currentPhoto.src = croppedDataUrl;
                    currentPhoto.style.display = 'block';
                }
                if (noPhotoMessage) {
                    noPhotoMessage.style.display = 'none';
                }
                if (recropBtn) {
                    recropBtn.style.display = 'inline-block';
                }
                if (removeBtn) {
                    removeBtn.style.display = 'inline-block';
                }

                var photoPreview = document.getElementById('photoPreview');
                if (photoPreview) {
                    photoPreview.classList.remove('photo-required-error');
                }

                // Sync with DataTransfer to userfile input if supported
                try {
                    canvas.toBlob(function(blob) {
                        if (blob && window.DataTransfer) {
                            var dt = new DataTransfer();
                            var file = new File([blob], 'cropped_photo.jpg', { type: 'image/jpeg' });
                            dt.items.add(file);
                            var userfile = document.getElementById('userfile');
                            if (userfile) {
                                userfile.files = dt.files;
                            }
                        }
                    }, 'image/jpeg', 0.90);
                } catch (e) {
                    // Fallback to base64 hidden field
                }

                // Close crop modal
                window.cancelPhotoCrop();
            });
        }
    });

    // Remove photo
    window.removeCurrentPhoto = function() {
        var currentPhoto = document.getElementById('currentPhoto');
        var noPhotoMessage = document.getElementById('noPhotoMessage');
        var userfile = document.getElementById('userfile');
        var camInput = document.getElementById('cameraInput');
        var gallInput = document.getElementById('galleryInput');
        var hiddenField = document.getElementById('cropped_image_data');
        var recropBtn = document.getElementById('btnRecropPhoto');
        var removeBtn = document.getElementById('btnRemovePhoto');

        if (currentPhoto) {
            currentPhoto.src = '';
            currentPhoto.style.display = 'none';
            currentPhoto.onerror = null;
        }
        if (noPhotoMessage) noPhotoMessage.style.display = 'block';
        if (userfile) userfile.value = '';
        if (camInput) camInput.value = '';
        if (gallInput) gallInput.value = '';
        if (hiddenField) hiddenField.value = '';
        if (recropBtn) recropBtn.style.display = 'none';
        if (removeBtn) removeBtn.style.display = 'none';
        lastSourceDataUrl = null;
    };

    // Load photo in edit modal
    window.loadModalPhoto = function(photoUrl) {
        var currentPhoto = document.getElementById('currentPhoto');
        var noPhotoMessage = document.getElementById('noPhotoMessage');
        var recropBtn = document.getElementById('btnRecropPhoto');
        var removeBtn = document.getElementById('btnRemovePhoto');
        var hiddenField = document.getElementById('cropped_image_data');
        var userfile = document.getElementById('userfile');

        if (userfile) userfile.value = '';
        if (hiddenField) hiddenField.value = '';

        if (photoUrl && photoUrl !== 'false' && photoUrl !== 'null' && photoUrl !== null && photoUrl.trim() !== '') {
            var baseUrl = photoUrl.split('?')[0];
            var cacheBuster = Date.now();
            if (currentPhoto) {
                currentPhoto.src = baseUrl + '?v=' + cacheBuster;
                currentPhoto.style.display = 'block';
                currentPhoto.onerror = function() {
                    window.removeCurrentPhoto();
                };
            }
            if (noPhotoMessage) noPhotoMessage.style.display = 'none';
            if (recropBtn) recropBtn.style.display = 'inline-block';
            if (removeBtn) removeBtn.style.display = 'inline-block';
            lastSourceDataUrl = baseUrl;

            var photoPreview = document.getElementById('photoPreview');
            if (photoPreview) {
                photoPreview.classList.remove('photo-required-error');
            }
        } else {
            window.removeCurrentPhoto();
        }
    };

    // Error helper
    function showError(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Photo Error',
                text: msg,
                confirmButtonColor: '#0d6efd'
            });
        } else if (typeof Toastify !== 'undefined') {
            Toastify({
                text: msg,
                duration: 5000,
                gravity: 'top',
                position: 'right',
                style: { background: 'linear-gradient(to right, #ef4444, #f87171)' }
            }).showToast();
        } else {
            alert(msg);
        }
    }
})();
</script>
