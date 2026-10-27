/**
 * face-login.js  —  Face Recognition Login Handler
 *
 * FLOW 1: Triggered from the signin page "Login with Face" button.
 *   1. Opens face modal → loads face-api models.
 *   2. Loads all registered face descriptors from /api/face-login-data.
 *   3. Runs a detection loop — matches live camera feed against descriptors.
 *   4. On match → calls /face-login (POST) which:
 *        a. Logs user in via session
 *        b. Creates Passport token
 *        c. Saves attendance check-in (face_recognition method)
 *        d. Returns redirect URL
 *   5. Stores token in localStorage → window.location redirects.
 *
 * FLOW 2 (post-login): Handled separately in header.blade.php.
 *   - Uses /api/attendance/face-checkin via the faceCheckInModal.
 */

'use strict';

// ---------------------------------------------------------------------------
// Module-level state
// ---------------------------------------------------------------------------
let loginFaceStream            = null;
let loginFaceMatcher           = null;
let loginFaceDetectionInterval = null;
let isLoginFaceModelsLoaded    = false;

// ---------------------------------------------------------------------------
// Entry point: wire up the "Login with Face" button
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    const faceLoginBtn = document.getElementById('faceLoginBtn');
    if (faceLoginBtn) {
        faceLoginBtn.addEventListener('click', openFaceLoginModal);
    }
});

// ---------------------------------------------------------------------------
// Model loading
// ---------------------------------------------------------------------------
async function loadLoginFaceModels() {
    // Try /public/models first (local, fast). CDN as fallback.
    const LOCAL_URL = '/models';
    const CDN_URL   = 'https://justadudewhohacks.github.io/face-api.js/models';

    async function tryLoad(url) {
        await faceapi.nets.ssdMobilenetv1.loadFromUri(url);
        await faceapi.nets.faceLandmark68Net.loadFromUri(url);
        await faceapi.nets.faceRecognitionNet.loadFromUri(url);
    }

    try {
        await tryLoad(LOCAL_URL);
    } catch (_) {
        console.warn('[face-login] Local models not found, loading from CDN...');
        await tryLoad(CDN_URL);
    }

    isLoginFaceModelsLoaded = true;
    console.log('[face-login] Models loaded successfully.');
}

// ---------------------------------------------------------------------------
// Build FaceMatcher from all registered employee faces
// ---------------------------------------------------------------------------
async function buildFaceMatcher() {
    updateLoginStatus('Loading registered faces…', true);

    let result;
    try {
        const res = await fetch('/api/face-login-data', { cache: 'no-cache' });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        result = await res.json();
    } catch (err) {
        console.error('[face-login] Failed to fetch face data:', err);
        updateLoginStatus('Cannot load face database. Check network.', false);
        return false;
    }

    if (!result.status || !result.data || result.data.length === 0) {
        updateLoginStatus('No registered faces found. Contact HR/Admin.', false);
        return false;
    }

    updateLoginStatus('Processing ' + result.data.length + ' registered face(s)…', true);

    const labeledDescriptors = [];

    for (const user of result.data) {
        try {
            const photoUrl = user.face_photo.startsWith('http')
                ? user.face_photo
                : '/storage/' + user.face_photo;

            const img       = await faceapi.fetchImage(photoUrl);
            const detection = await faceapi
                .detectSingleFace(img, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (detection) {
                labeledDescriptors.push(
                    new faceapi.LabeledFaceDescriptors(String(user.id), [detection.descriptor])
                );
                console.log('[face-login] Loaded face for user:', user.name, '(id=' + user.id + ')');
            } else {
                console.warn('[face-login] No face detected in photo for:', user.name);
            }
        } catch (err) {
            console.warn('[face-login] Could not process face for user', user.name, ':', err.message);
        }
    }

    if (labeledDescriptors.length === 0) {
        updateLoginStatus('Failed to extract any face descriptors. Re-upload photos.', false);
        return false;
    }

    // Distance threshold 0.5 = good balance between accuracy and false positives
    loginFaceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.5);
    console.log('[face-login] FaceMatcher built with', labeledDescriptors.length, 'descriptor(s).');
    return true;
}

// ---------------------------------------------------------------------------
// Camera helpers
// ---------------------------------------------------------------------------
async function startLoginCamera() {
    const video = document.getElementById('login-face-video');
    try {
        loginFaceStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: 300, height: 300 }
        });
        video.srcObject = loginFaceStream;
        return new Promise(resolve => {
            video.onloadedmetadata = () => resolve(true);
            video.onerror          = () => resolve(false);
        });
    } catch (err) {
        console.error('[face-login] Camera error:', err);
        updateLoginStatus('Camera access denied or unavailable.', false);
        return false;
    }
}

function stopFaceLoginCamera() {
    if (loginFaceDetectionInterval) {
        clearInterval(loginFaceDetectionInterval);
        loginFaceDetectionInterval = null;
    }
    if (loginFaceStream) {
        loginFaceStream.getTracks().forEach(t => t.stop());
        loginFaceStream = null;
    }
}

// ---------------------------------------------------------------------------
// Status helper
// ---------------------------------------------------------------------------
function updateLoginStatus(message, showSpinner = false) {
    const msgEl     = document.getElementById('login-camera-message');
    const spinnerEl = document.getElementById('login-camera-spinner');
    if (msgEl) msgEl.innerText = message;
    if (spinnerEl) spinnerEl.style.display = showSpinner ? 'inline-block' : 'none';
}

// ---------------------------------------------------------------------------
// Open face-login modal and start the full recognition pipeline
// ---------------------------------------------------------------------------
async function openFaceLoginModal() {
    // Reset canvas
    const canvas = document.getElementById('login-face-canvas');
    if (canvas) canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

    const modal = new bootstrap.Modal(document.getElementById('faceLoginModal'));
    modal.show();

    updateLoginStatus('Initializing AI models…', true);

    // 1. Load face-api models (only once per page load)
    if (!isLoginFaceModelsLoaded) {
        try {
            await loadLoginFaceModels();
        } catch (err) {
            updateLoginStatus('Failed to load AI models. Refresh and try again.', false);
            return;
        }
    }

    // 2. Start camera
    updateLoginStatus('Starting camera…', true);
    const cameraOk = await startLoginCamera();
    if (!cameraOk) return;

    // 3. Build face matcher (cached after first build)
    if (!loginFaceMatcher) {
        const matcherOk = await buildFaceMatcher();
        if (!matcherOk) {
            stopFaceLoginCamera();
            setTimeout(() => {
                modal.hide();
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'No registered faces found. Please log in with email & password.', 'error');
                }
            }, 1000);
            return;
        }
    }

    updateLoginStatus('Position your face in front of the camera…', false);

    // 4. Run detection loop
    const video = document.getElementById('login-face-video');
    let isVerifying  = false;
    let unknownCount = 0;
    let confirmed    = false;  // guard: ensure check-in fires only once

    loginFaceDetectionInterval = setInterval(async () => {
        if (isVerifying || confirmed || !loginFaceMatcher || !loginFaceStream) return;
        isVerifying = true;

        try {
            const detection = await faceapi
                .detectSingleFace(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (detection) {
                // Find all users whose face matches the current camera feed
                let matches = [];
                const maxDistance = 0.5; // The threshold used when building loginFaceMatcher

                loginFaceMatcher.labeledDescriptors.forEach(ld => {
                    let minDistance = 1.0;
                    ld.descriptors.forEach(desc => {
                        const dist = faceapi.euclideanDistance(detection.descriptor, desc);
                        if (dist < minDistance) minDistance = dist;
                    });
                    
                    if (minDistance < maxDistance) {
                        matches.push({ label: ld.label, distance: minDistance });
                    }
                });

                if (matches.length > 1) {
                    // Multiple users matched the same face!
                    confirmed = true;
                    clearInterval(loginFaceDetectionInterval);
                    loginFaceDetectionInterval = null;
                    stopFaceLoginCamera();
                    modal.hide();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire(
                            'Multiple Profiles Detected', 
                            'This face is associated with multiple users. Please log in using email and password, or update your face photo.', 
                            'warning'
                        );
                    }
                } else if (matches.length === 1) {
                    const bestMatch = matches[0];
                    confirmed = true;
                    clearInterval(loginFaceDetectionInterval);
                    loginFaceDetectionInterval = null;

                    updateLoginStatus('Face recognised! Logging you in…', true);
                    console.log('[face-login] Matched user id:', bestMatch.label, 'distance:', bestMatch.distance);
                    await performFaceLogin(bestMatch.label, modal);
                } else {
                    unknownCount++;
                    updateLoginStatus('Analysing… Please hold still (' + unknownCount + '/5)', true);

                    if (unknownCount >= 5) {
                        clearInterval(loginFaceDetectionInterval);
                        loginFaceDetectionInterval = null;
                        stopFaceLoginCamera();
                        modal.hide();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Login Failed', 'Face not recognised. Please log in with email & password.', 'error');
                        }
                    }
                }
            } else {
                updateLoginStatus('No face detected. Look straight at the camera.', false);
                unknownCount = Math.max(0, unknownCount - 0.5);
            }
        } catch (err) {
            console.error('[face-login] Detection error:', err);
        }

        isVerifying = false;
    }, 1000);
}

// ---------------------------------------------------------------------------
// FLOW 1: Perform the actual login + attendance check-in via backend
// ---------------------------------------------------------------------------
async function performFaceLogin(userId, modalInstance) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    updateLoginStatus('Logging in and recording attendance… Please wait.', true);
    stopFaceLoginCamera();

    try {
        const res = await fetch('/face-login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ user_id: Number(userId) })
        });

        let data;
        try {
            data = await res.json();
        } catch (_) {
            throw new Error('Invalid server response');
        }

        if (!res.ok || !data.status) {
            const errMsg = data.error || data.message || 'Login failed. Try email & password.';
            modalInstance.hide();
            if (typeof Swal !== 'undefined') {
                Swal.fire('Login Failed', errMsg, 'error');
            }
            return;
        }

        // ✅ Store auth token and branch info for subsequent API calls
        if (data.token) {
            localStorage.setItem('authToken', data.token);
        }
        if (data.user) {
            const selectedId = data.user.branch_id || data.user.id;
            if (selectedId) {
                localStorage.setItem('selectedSubAdminId', String(selectedId));
                // Sync to server session so server-side Blade templates also pick it up
                try {
                    await fetch('/set-subadmin-session', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: 'subAdminId=' + encodeURIComponent(selectedId) + '&_token=' + encodeURIComponent(csrfToken)
                    });
                } catch (_) { /* non-critical */ }
            }
        }

        updateLoginStatus('✓ Logged in! Attendance recorded. Redirecting…', false);

        // Redirect (small delay so the user sees the success message)
        setTimeout(() => {
            window.location.href = data.redirect || '/dashboard';
        }, 500);

    } catch (err) {
        console.error('[face-login] performFaceLogin error:', err);
        modalInstance.hide();
        if (typeof Swal !== 'undefined') {
            Swal.fire('Error', 'System error during face login. Please try email & password.', 'error');
        }
    }
}
