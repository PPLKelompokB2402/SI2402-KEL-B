@props(['name', 'size' => 18])

<svg {{ $attributes->merge(['class' => 'ui-icon', 'width' => $size, 'height' => $size]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('grid')
            <rect x="3.5" y="3.5" width="7" height="7" rx="1"></rect><rect x="13.5" y="3.5" width="7" height="7" rx="1"></rect><rect x="3.5" y="13.5" width="7" height="7" rx="1"></rect><rect x="13.5" y="13.5" width="7" height="7" rx="1"></rect>
            @break
        @case('shield')
            <path d="M12 3 20 6v5c0 5.2-3.4 8.4-8 10-4.6-1.6-8-4.8-8-10V6l8-3Z"></path>
            @break
        @case('wallet')
            <path d="M4 6.5h14a2 2 0 0 1 2 2v10H5a2 2 0 0 1-2-2v-12a2 2 0 0 1 2-2h12"></path><path d="M20 10h-5a2 2 0 0 0 0 4h5"></path><path d="M15 12h.01"></path>
            @break
        @case('alert')
            <path d="m12 3 10 18H2L12 3Z"></path><path d="M12 9v5"></path><path d="M12 17h.01"></path>
            @break
        @case('briefcase')
            <rect x="3" y="7" width="18" height="14" rx="2"></rect><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M3 12h18"></path>
            @break
        @case('store')
            <path d="M3 10 5 4h14l2 6"></path><path d="M4 10v10h16V10"></path><path d="M4 10a2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0"></path><path d="M9 20v-5h6v5"></path>
            @break
        @case('users')
            <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="10" cy="7" r="4"></circle><path d="M20 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            @break
        @case('file')
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path><path d="M14 2v6h6"></path><path d="M8 13h8M8 17h8"></path>
            @break
        @case('logout')
            <path d="M10 17l5-5-5-5"></path><path d="M15 12H3"></path><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"></path>
            @break
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path>
            @break
        @case('user')
            <circle cx="12" cy="8" r="4"></circle><path d="M4 21v-1a7 7 0 0 1 14 0v1"></path>
            @break
        @case('image')
            <rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-9 9"></path>
            @break
        @case('bell')
            <path d="M6 9a6 6 0 0 1 12 0c0 6 2.5 7.5 2.5 7.5h-17S6 15 6 9Z"></path><path d="M10 20a2 2 0 0 0 4 0"></path>
            @break
        @case('flag')
            <path d="M5 21V4"></path><path d="M5 4h11l-2 4 2 4H5"></path>
            @break
        @case('star')
            <path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"></path>
            @break
        @case('trash')
            <path d="M4 7h16"></path><path d="M10 11v6M14 11v6"></path><path d="M6 7l1 13h10l1-13"></path><path d="M9 7V4h6v3"></path>
            @break
        @case('eye')
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle>
            @break
        @case('pin')
            <path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21Z"></path><circle cx="12" cy="9.5" r="2.5"></circle>
            @break
        @case('chat')
            <path d="M4 5h16v11H8l-4 4V5Z"></path><path d="M8 9h8M8 12h5"></path>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"></path>
            @break
        @case('x')
            <path d="M6 6l12 12M18 6 6 18"></path>
            @break
        @case('search')
            <circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path>
            @break
        @case('check')
            <path d="m5 12 4.5 4.5L19 7"></path>
            @break
        @case('arrow-left')
            <path d="M19 12H5"></path><path d="m11 6-6 6 6 6"></path>
            @break
    @endswitch
</svg>
