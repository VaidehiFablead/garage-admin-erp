/**
 * face-upload-validator.js
 * Validates face photos for Staff Management
 * - Detects faces with minimum 60% confidence
 * - Shows validation badges with confidence scores
 * - Prevents upload of unclear faces
 */

'use strict';

let isModelsLoaded = false;

/**
 * Load face-api models (called once)
 */
async function loadFaceModels() {
    if (isModelsLoaded) return;
    
    try {
        const LOCAL_URL = '/models';
        const CDN_URL   = 'https://justadudewhohacks.github.io/face-api.js/models';
        
        try {
            await faceapi.nets.ssdMobilenetv1.loadFromUri(LOCAL_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(LOCAL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(LOCAL_URL);
        } catch (_) {
            console.warn('[face-validator] Local models not found, loading from CDN...');
            await faceapi.nets.ssdMobilenetv1.loadFromUri(CDN_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(CDN_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(CDN_URL);
        }
        
        isModelsLoaded = true;
        console.log('[face-validator] Models loaded successfully.');
    } catch (err) {
        console.error('[face-validator] Failed to load models:', err);
        throw err;
    }
}

/**
 * Show face detection badge with confidence score
 * @param {string} badgeId - ID of badge element
 * @param {number} confidence - Confidence score (0-100)
 * @param {string} state - 'hidden', 'loading', 'success', 'warning', 'error'
 */
function fuvShowBadge(badgeId, confidence = 0, state = 'hidden') {
    const badge = document.getElementById(badgeId);
    if (!badge) return;
    
    if (state === 'hidden') {
        badge.style.display = 'none';
        return;
    }
    
    badge.style.display = 'block';
    
    let html = '';
    let color = '#1b2850';
    let bgColor = '#f0f4ff';
    
    if (state === 'loading') {
        html = '<i class="fas fa-spinner fa-spin me-1"></i> Analyzing...';
        bgColor = '#f8f9fa';
    } else if (state === 'success') {
        html = `<i class="fas fa-check-circle me-1" style="color:#28a745;"></i> Face detected (${confidence}% confidence)`;
        color = '#28a745';
        bgColor = '#f0fdf4';
    } else if (state === 'warning') {
        html = `<i class="fas fa-exclamation-triangle me-1" style="color:#ffc107;"></i> Low confidence (${confidence}%, need 60%)`;
        color = '#ff9f43';
        bgColor = '#fff8f0';
    } else if (state === 'error') {
        html = '<i class="fas fa-times-circle me-1" style="color:#dc3545;"></i> No face detected';
        color = '#dc3545';
        bgColor = '#fdf0f0';
    }
    
    badge.innerHTML = html;
    badge.style.color = color;
    badge.style.background = bgColor;
    badge.style.borderColor = color;
}

/**
 * Validate a face photo
 * @param {File|Blob} imageFile - The image file to validate
 * @param {string} badgeId - ID of badge element to update
 * @returns {Promise<boolean>} - True if face is valid (≥60% confidence), false otherwise
 */
async function fuvValidate(imageFile, badgeId = 'face-detection-badge') {
    try {
        // Load models if not already loaded
        if (!isModelsLoaded) {
            fuvShowBadge(badgeId, 0, 'loading');
            await loadFaceModels();
        }
        
        fuvShowBadge(badgeId, 0, 'loading');
        
        // Read the image
        const url = URL.createObjectURL(imageFile);
        const img = await faceapi.fetchImage(url);
        
        // Detect faces with 50% minimum confidence for detection
        const detection = await faceapi
            .detectSingleFace(img, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
            .withFaceLandmarks()
            .withFaceDescriptor();
        
        URL.revokeObjectURL(url);
        
        if (!detection) {
            // No face detected
            fuvShowBadge(badgeId, 0, 'error');
            console.warn('[face-validator] No face detected in image');
            return false;
        }
        
        // Get confidence score (0-1, convert to 0-100)
        const confidence = Math.round(detection.detection.score * 100);
        const CONFIDENCE_THRESHOLD = 60; // Minimum 60% confidence required
        
        if (confidence >= CONFIDENCE_THRESHOLD) {
            // ✅ Face is valid
            fuvShowBadge(badgeId, confidence, 'success');
            console.log('[face-validator] Face validated with ' + confidence + '% confidence');
            return true;
        } else {
            // ⚠️ Face detected but low confidence
            fuvShowBadge(badgeId, confidence, 'warning');
            console.warn('[face-validator] Face detected but low confidence: ' + confidence + '%');
            return false;
        }
        
    } catch (err) {
        console.error('[face-validator] Validation error:', err);
        fuvShowBadge(badgeId, 0, 'error');
        return false;
    }
}

/**
 * Build face matcher from stored descriptors
 * Used for face matching/comparison
 * @param {Array} descriptors - Array of face descriptors
 * @returns {FaceMatcher} - FaceMatcher instance for comparing faces
 */
function fuvBuildMatcher(labeledDescriptors) {
    if (!labeledDescriptors || labeledDescriptors.length === 0) {
        return null;
    }
    
    // Distance threshold 0.5 = balance between accuracy and false positives
    const matcher = new faceapi.FaceMatcher(labeledDescriptors, 0.5);
    console.log('[face-validator] FaceMatcher built with', labeledDescriptors.length, 'descriptor(s).');
    return matcher;
}

/**
 * Get face descriptor from an image (for matching)
 * @param {string} imageUrl - URL of the image
 * @returns {Promise<Float32Array>} - Face descriptor or null if no face found
 */
async function fuvGetDescriptor(imageUrl) {
    try {
        if (!isModelsLoaded) {
            await loadFaceModels();
        }
        
        const img = await faceapi.fetchImage(imageUrl);
        const detection = await faceapi
            .detectSingleFace(img, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
            .withFaceLandmarks()
            .withFaceDescriptor();
        
        return detection ? detection.descriptor : null;
    } catch (err) {
        console.error('[face-validator] Error getting descriptor:', err);
        return null;
    }
}

/**
 * Compare two face descriptors
 * @param {Float32Array} descriptor1 - First face descriptor
 * @param {Float32Array} descriptor2 - Second face descriptor
 * @returns {number} - Distance (0-1, lower = more similar)
 */
function fuvCompareFaces(descriptor1, descriptor2) {
    if (!descriptor1 || !descriptor2) {
        return 1.0; // Maximum distance if either is missing
    }
    
    return faceapi.euclideanDistance(descriptor1, descriptor2);
}

console.log('[face-validator] Module loaded. Ready for face validation.');
