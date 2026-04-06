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

        {{-- Bouton démarrage — nécessaire sur mobile (geste utilisateur requis) --}}
        <div x-show="!cameraStarted && !cameraFailed" style="margin-bottom: 1.5rem;">
            <button @click="startCamera()"
                    class="btn-primary"
                    style="width: 100%; padding: 1rem; font-size: 1rem;">
                Activer la caméra
            </button>
            <p style="font-size: 0.7rem; color: var(--color-text-muted); margin-top: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">
                Appuyez pour démarrer le scan
            </p>
        </div>

        <div x-show="cameraStarted" class="scan-frame" style="margin-bottom: 1.5rem;">
            <video id="qr-video" autoplay playsinline></video>
            <div class="scan-line"></div>
        </div>
        <p x-show="cameraStarted" style="font-size: 0.75rem; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.1em;">
            Placez le QR code dans le cadre
        </p>
        <div x-show="error" class="alert-error" style="margin-top: 1rem;" x-text="error"></div>
    </div>

    {{-- Saisie manuelle : toujours visible si caméra indisponible, sinon DEV uniquement --}}
    <div x-show="!result && (cameraFailed{{ app()->isLocal() ? ' || true' : '' }})"
         style="margin-top: 2rem; padding: 1rem; border: 1px dashed var(--color-warning); border-radius: 0.5rem;">
        <p style="font-size: 0.7rem; color: var(--color-warning); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.75rem;">
            {{ app()->isLocal() ? '⚠ Mode DEV — Saisie manuelle' : 'Saisie manuelle du QR code' }}
        </p>
        <input type="text"
               x-model="manualToken"
               placeholder="Coller le qr_token du membre..."
               style="width: 100%; background: var(--color-bg-soft); border: 1px solid var(--color-border); color: var(--color-text); padding: 0.5rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; margin-bottom: 0.5rem; font-family: monospace;">
        <button @click="manualToken && validate(manualToken)"
                class="btn-primary"
                style="width: 100%; padding: 0.5rem;">
            Valider manuellement
        </button>
    </div>

    {{-- Résultat valide --}}
    <div class="scan-result scan-result-valid"
         x-cloak
         x-show="result === 'valid'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        {{-- Photo membre — vérification visuelle --}}
        <div style="margin-bottom: 1rem;">
            <template x-if="photoUrl">
                <img :src="photoUrl" :alt="memberName"
                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;
                            border: 3px solid #22C55E; margin: 0 auto; display: block;">
            </template>
            <template x-if="!photoUrl">
                <div style="width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.2);
                            display: flex; align-items: center; justify-content: center;
                            font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700;
                            color: white; margin: 0 auto; border: 3px solid #22C55E;"
                     x-text="memberInitials">
                </div>
            </template>
        </div>

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
         x-cloak
         x-show="result === 'invalid'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        {{-- Photo membre même en cas de refus (aide à identifier la personne) --}}
        <template x-if="photoUrl">
            <img :src="photoUrl" :alt="memberName"
                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;
                        border: 3px solid #EF4444; margin: 0 auto 0.75rem; display: block; opacity: 0.8;">
        </template>

        <div class="scan-result-icon">✗</div>
        <div class="scan-result-name" x-text="memberName || 'Accès refusé'"></div>
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
        memberInitials: '',
        photoUrl: '',
        failureReason: '',
        gymName: '',
        error: null,
        cameraFailed: false,
        cameraStarted: false,
        scanning: false,
        videoEl: null,
        canvasEl: null,
        animFrameId: null,
        manualToken: '',

        init() {
            this.canvasEl = document.createElement('canvas');
        },

        async startCamera() {
            if (!window.isSecureContext && location.hostname !== 'localhost') {
                this.error = 'La caméra nécessite HTTPS. Utilisez la saisie manuelle ci-dessous.';
                this.cameraFailed = true;
                return;
            }

            if (!navigator.mediaDevices?.getUserMedia) {
                this.error = 'Caméra non supportée par ce navigateur. Utilisez la saisie manuelle.';
                this.cameraFailed = true;
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                this.cameraStarted = true;
                await this.$nextTick();
                this.videoEl = document.getElementById('qr-video');
                this.videoEl.srcObject = stream;
                this.videoEl.addEventListener('loadedmetadata', () => this.scan());
            } catch (e) {
                if (e.name === 'NotAllowedError') {
                    this.error = 'Permission caméra refusée. Autorisez dans les paramètres du navigateur.';
                } else {
                    this.error = 'Caméra inaccessible (' + e.name + '). Utilisez la saisie manuelle.';
                }
                this.cameraFailed = true;
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
                const res = await fetch('{{ route("gym.scan.validate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ qr_token: qrToken }),
                });
                const data = await res.json();
                this.memberName     = data.data?.member_name ?? '';
                this.memberInitials = data.data?.member_initials ?? '?';
                this.photoUrl       = data.data?.photo_url ?? '';
                this.gymName        = data.data?.gym_name ?? '';

                if (res.ok && data.data?.status === 'valid') {
                    this.result = 'valid';
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
            this.result         = null;
            this.memberName     = '';
            this.memberInitials = '';
            this.photoUrl       = '';
            this.failureReason  = '';
            this.animFrameId    = requestAnimationFrame(() => this.scan());
        },
    };
}
</script>
@endpush
