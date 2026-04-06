{{--
    Composant avatar réutilisable — photo ou initiales
    Props :
      $user  : App\Models\User
      $size  : 'sm' (32px) | 'md' (48px) | 'lg' (80px) | 'xl' (160px)
    Usage :
      <x-avatar :user="$user" size="lg" />
--}}
@props(['user', 'size' => 'md'])

@php
    $dimensions = match($size) {
        'sm'  => '2rem',    // 32px
        'md'  => '3rem',    // 48px
        'lg'  => '5rem',    // 80px
        'xl'  => '10rem',   // 160px
        default => '3rem',
    };
    $fontSize = match($size) {
        'sm'  => '0.75rem',
        'md'  => '1rem',
        'lg'  => '1.5rem',
        'xl'  => '2.5rem',
        default => '1rem',
    };
@endphp

@if($user->profile_photo_url)
    <img
        src="{{ $user->profile_photo_url }}"
        alt="Photo de {{ $user->name }}"
        style="width: {{ $dimensions }}; height: {{ $dimensions }}; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-primary);"
    >
@else
    <div style="
        width: {{ $dimensions }};
        height: {{ $dimensions }};
        border-radius: 50%;
        background: var(--color-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-heading);
        font-size: {{ $fontSize }};
        font-weight: 700;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        flex-shrink: 0;
    ">
        {{ $user->initials }}
    </div>
@endif
