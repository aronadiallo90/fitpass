@extends('layouts.gym')
@section('title', 'Scanner QR Code')

@push('styles')
<style>
    #qr-video { width: 100%; height: 100%; object-fit: cover; }
    @keyframes scan-line {
        0%   { top: 10%; }
        100% { top: 90%; }
    }
    .scan-line {
        position: absolute;
        left: 1rem; right: 1rem;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--color-primary), transparent);
        animation: scan-line 2s ease-in-out infinite alternate;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">Scanner</h1>
    <a href="{{ route('gym.dashboard') }}" class="btn-ghost">← Retour</a>
</div>

<div style="max-width: 400px; margin: 0 auto;" x-data="qrScanner()">

    {{-- Zone de scan --}}
    <div x-show="!result" style="text-align: center;">
        <div class="scan-frame" style="margin-bottom: 1.5rem;">
            <video id="qr-video" autoplay playsinline></video>
            <div class="scan-line"></div>
        </div>
        <p style="font-size: 0.75rem; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.1em;">
            Placez le QR code dans le cadre
        </p>
        <div x-show="error" class="alert-error" style="margin-top: 1rem;" x-text="error"></div>
    </div>

    {{-- Résultat valide --}}
    <div class="scan-result scan-result-valid"
         x-show="result === 'valid'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="scan-result-icon">✓</div>
        <div class="scan-result-name" x-text="memberName"></div>
        <div class="scan-result-status">Entrée validée</div>
        <div style="font-size: 0.875rem; color: rgba(255,255,255,0.7);" x-text="gymName"></div>
        <button class="btn-outline" @click="reset()"
                style="border-color: white; color: white; margin-top: 1.5rem;">
            Scanner à nouveau
        </button>
    </div>

    {{-- Résultat invalide --}}
    <div class="scan-result scan-result-invalid"
         x-show="result === 'invalid'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="scan-result-icon">✗</div>
        <div class="scan-result-name">Accès refusé</div>
        <div class="scan-result-status" x-text="failureReason"></div>
        <button class="btn-outline" @click="reset()"
                style="border-color: white; color: white; margin-top: 1.5rem;">
            Scanner à nouveau
        </button>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
function qrScanner() {
    return {
        result: null,
        memberName: '',
        failureReason: '',
        gymName: '',
        error: null,
        scanning: false,
        videoEl: null,
        canvasEl: null,
        animFrameId: null,

        async init() {
            this.videoEl = document.getElementById('qr-video');
            this.canvasEl = document.createElement('canvas');
            await this.startCamera();
        },

        async startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                this.videoEl.srcObject = stream;
                this.videoEl.addEventListener('loadedmetadata', () => this.scan());
            } catch (e) {
                this.error = 'Accès caméra refusé. Autorisez la caméra dans les paramètres.';
            }
        },

        scan() {
            if (this.result) return;
            const ctx = this.canvasEl.getContext('2d');
            this.canvasEl.width  = this.videoEl.videoWidth;
            this.canvasEl.height = this.videoEl.videoHeight;
            ctx.drawImage(this.videoEl, 0, 0);
            const img  = ctx.getImageData(0, 0, this.canvasEl.width, this.canvasEl.height);
            const code = jsQR(img.data, img.width, img.height, { inversionAttempts: 'dontInvert' });
            if (code) {
                this.validate(code.data);
            } else {
                this.animFrameId = requestAnimationFrame(() => this.scan());
            }
        },

        async validate(qrToken) {
            try {
                const res = await fetch('/api/v1/checkins/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer {{ auth()->user()->gym?->api_token ?? "" }}',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ qr_token: qrToken }),
                });
                const data = await res.json();
                if (res.ok && data.data?.status === 'valid') {
                    this.result      = 'valid';
                    this.memberName  = data.data.member_name ?? 'Membre';
                    this.gymName     = data.data.gym_name ?? '';
                } else {
                    this.result        = 'invalid';
                    this.failureReason = data.message ?? 'QR code invalide';
                }
                // Auto-reset après 4 secondes
                setTimeout(() => this.reset(), 4000);
            } catch {
                this.result        = 'invalid';
                this.failureReason = 'Erreur réseau — réessayez';
                setTimeout(() => this.reset(), 3000);
            }
        },

        reset() {
            this.result        = null;
            this.memberName    = '';
            this.failureReason = '';
            this.animFrameId   = requestAnimationFrame(() => this.scan());
        },
    };
}
</script>
@endpush
