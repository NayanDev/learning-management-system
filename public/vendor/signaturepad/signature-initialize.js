// Initialize Signature Pad
let signaturePad;
let isSignatureSaved = false;

document.addEventListener('DOMContentLoaded', function() {
    initializeSignaturePad();
    setupEventListeners();
});

function initializeSignaturePad() {
    const canvas = document.getElementById('signature-pad');
    // Deteksi ukuran layar
    const isMobile = window.innerWidth <= 768; // Anggap 768px sebagai batas ukuran mobile
    
    // Set ukuran kanvas sesuai perangkat
    if (isMobile) {
        canvas.width = window.innerWidth * 0.9;  // 90% dari lebar layar
        canvas.height = 500;  // Ukuran yang lebih kecil untuk mobile
    } else {
        canvas.width = 400;  // Lebar lebih besar untuk desktop
        canvas.height = 300;  // Sesuaikan dengan ukuran desktop
    }
    
    if (!canvas) return;
    
    signaturePad = new SignaturePad(canvas, {
        backgroundColor: null,
        penColor: 'rgba(11, 11, 170, 0.91)',
        velocityFilterWeight: 0.7,
        minWidth: 0.5,
        maxWidth: 2.5,
        throttle: 16,
        minDistance: 5,
    });
    
    // Listen for signature changes
    signaturePad.addEventListener("afterUpdateStroke", function() {
        isSignatureSaved = false;
        updateSignatureStatus();
    });
    
    resizeCanvas();
}

function setupEventListeners() {
    // Handle signature method change
    const signatureMethodInputs = document.querySelectorAll('input[name="signature_method"]');
    signatureMethodInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'draw') {
                document.getElementById('draw-signature-section').style.display = 'block';
                document.getElementById('upload-signature-section').style.display = 'none';
            } else {
                document.getElementById('draw-signature-section').style.display = 'none';
                document.getElementById('upload-signature-section').style.display = 'block';
            }
        });
    });
    
    // Resize canvas on window resize
    window.addEventListener("resize", resizeCanvas);
}

function resizeCanvas() {
    const canvas = document.getElementById('signature-pad');
    if (!canvas || !signaturePad) return;
    
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
}

function clearSignature() {
    if (signaturePad) {
        signaturePad.clear();
        document.getElementById('signature-data').value = '';
        document.getElementById('signature-preview-draw').innerHTML = '';
        isSignatureSaved = false;
        updateSignatureStatus();
    }
}

function previewSignature() {
    if (!signaturePad || signaturePad.isEmpty()) {
        showNotification("Please draw a signature first.", "warning");
        return;
    }
    
    const previewDiv = document.getElementById('signature-preview-draw');
    
    try {
        // Generate SVG preview
        const svgData = signaturePad.toSVG();
        previewDiv.innerHTML = `
            <div class="mt-3">
                <small class="text-muted">SVG Preview:</small><br>
                <div class="border rounded p-2 bg-white" style="max-width: 100%; margin: 10px auto;">
                    ${svgData}
                </div>
            </div>
        `;
    } catch (error) {
        // Fallback to PNG preview
        const dataURL = signaturePad.toDataURL('image/svg+xml');
        previewDiv.innerHTML = `
            <div class="mt-3">
                <small class="text-muted">PNG Preview:</small><br>
                <img style="max-width: 100%; height: auto;" src="${dataURL}" alt="Signature Preview" class="current-signature">
                <br><small class="text-info">⚠️ Raster format (fixed size)</small>
            </div>
        `;
    }
}

function saveSignature() {
    if (!signaturePad || signaturePad.isEmpty()) {
        showNotification("Please draw a signature first.", "warning");
        return;
    }
    
    // Get signature data as SVG (preferred format)
    try {
        const svgData = signaturePad.toSVG();
        const svgBase64 = 'data:image/svg+xml;base64,' + btoa(svgData);
        document.getElementById('signature-data').value = svgBase64;
        isSignatureSaved = true;
        
        updateSignatureStatus();
        showNotification("Signature saved as SVG format!", "success");
    } catch (error) {
        // Fallback to PNG if SVG fails
        console.warn('SVG generation failed, using PNG fallback:', error);
        const dataURL = signaturePad.toDataURL('image/svg+xml');
        document.getElementById('signature-data').value = dataURL;
        isSignatureSaved = true;
        
        updateSignatureStatus();
        showNotification("Signature saved as PNG format!", "success");
    }
}

function updateSignatureStatus() {
    const statusDiv = document.getElementById('signature-status');
    if (!statusDiv) return;
    
    if (signaturePad && signaturePad.isEmpty()) {
        statusDiv.innerHTML = '<small class="text-muted">Canvas is empty</small>';
    } else if (isSignatureSaved) {
        const signatureData = document.getElementById('signature-data').value;
        const format = signatureData.includes('svg+xml') ? 'SVG' : 'PNG';
        const formatClass = format === 'SVG' ? 'success' : 'info';
        statusDiv.innerHTML = `<small class="text-${formatClass}"><i class="ti ti-check"></i> Signature saved as ${format}</small>`;
    } else {
        statusDiv.innerHTML = '<small class="text-warning"><i class="ti ti-alert-triangle"></i> Please save your signature</small>';
    }
}

function previewUploadedSignature(input) {
    const preview = document.getElementById('upload-preview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div>
                    <small class="text-muted">Preview:</small><br>
                    <img src="${e.target.result}" alt="Signature Preview" class="current-signature">
                </div>
            `;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Override form submission to validate signature first
document.addEventListener('DOMContentLoaded', function() {
    const originalSoftSubmit = window.softSubmit;
    
    window.softSubmit = function(formId, reloadList, callback = false) {
        // Only validate for profile form
        if (formId === 'form-maccount') {
            const signatureMethod = document.querySelector('input[name="signature_method"]:checked').value;
            
            if (signatureMethod === 'draw') {
                if (!signaturePad.isEmpty() && !isSignatureSaved) {
                    showNotification("Please save your signature first by clicking 'Save as SVG' button.", "warning");
                    return;
                }
            } else if (signatureMethod === 'upload') {
                const fileInput = document.querySelector('input[name="signature_file"]');
                if (fileInput.files.length === 0) {
                    showNotification("Please select a signature file to upload.", "warning");
                    return;
                }
            }
        }
        
        // Call original softSubmit function
        return originalSoftSubmit.call(this, formId, reloadList, callback);
    };
});