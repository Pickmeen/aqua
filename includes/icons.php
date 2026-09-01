<?php
/**
 * Bibliothèque d'icônes SVG (trait, thème plongée/océan) affichées en ligne
 * pour rester nettes à toute résolution, sans dépendre d'images externes.
 *
 * Les clés utilisées par la table `formations` (child, medal, first-dive,
 * whistle, fin, compass, depth-gauge, anchor) sont stables : ne pas les
 * renommer sans migrer la base.
 */

function icon_names(): array
{
    return [
        // --- Formations (référencées en base) ---
        'child'       => '<circle cx="12" cy="6" r="3"/><path d="M6 21v-5a6 6 0 0 1 12 0v5"/><path d="M9 21v-3"/><path d="M15 21v-3"/>',
        'medal'       => '<circle cx="12" cy="15" r="5.5"/><path d="M9 10 6 3h3l3 6 3-6h3l-3 7"/><path d="M12 12.5 13.3 15l2.5.3-1.9 1.7.5 2.5-2.4-1.3-2.4 1.3.5-2.5-1.9-1.7 2.5-.3z"/>',
        'first-dive'  => '<circle cx="8" cy="6" r="2.4"/><path d="M4 21c0-4 2-6 4-6.5M8 14.5c2 .5 3 2 3 4.5M8 14.5V11l4-2 3 2.5M15 9l3 1.5-1 3.5"/>',
        'whistle'     => '<circle cx="8" cy="14" r="5"/><path d="M13 12h6a2 2 0 0 1 0 4h-3"/><path d="M8 11v-1a2 2 0 0 1 2-2h2"/>',
        'fin'         => '<path d="M5 20c3-9 7-15 14-16-2 6-1 10 2 13-5 2-9 1-11-1-1 1-2 2-5 4z"/>',
        'compass'     => '<circle cx="12" cy="12" r="9"/><path d="M15 9l-2 6-6 2 2-6z"/>',
        'depth-gauge' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/><path d="M12 3v1.5M21 12h-1.5M3 12h1.5M12 21v-1.5"/>',
        'anchor'      => '<circle cx="12" cy="5" r="2"/><path d="M12 7v14"/><path d="M6 12H2a10 10 0 0 0 10 10 10 10 0 0 0 10-10h-4"/><path d="M8 12h8"/>',

        // --- Interface ---
        'bubbles'      => '<circle cx="8" cy="15" r="3"/><circle cx="15" cy="8" r="4"/><circle cx="17" cy="17" r="1.6"/>',
        'facebook'     => '<path d="M15 8h2V4h-2a4 4 0 0 0-4 4v2H9v4h2v6h4v-6h2.5l.5-4H15V8.5c0-.3.2-.5.5-.5z"/>',
        'mail'         => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'pin'          => '<path d="M12 22s7-6.5 7-12a7 7 0 0 0-14 0c0 5.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/>',
        'clock'        => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
        'calendar'     => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'chevron-left' => '<path d="M15 6l-6 6 6 6"/>',
        'chevron-right'=> '<path d="M9 6l6 6-6 6"/>',
        'chevron-down' => '<path d="M6 9l6 6 6-6"/>',
        'arrow-right'  => '<path d="M5 12h14"/><path d="M13 6l6 6-6 6"/>',
        'arrow-down'   => '<path d="M12 5v14"/><path d="M6 13l6 6 6-6"/>',
        'arrow-up'     => '<path d="M12 19V5"/><path d="M6 11l6-6 6 6"/>',
        'wave'         => '<path d="M2 12c2-2 4-2 6 0s4 2 6 0 4-2 6 0 4 2 6 0"/>',
        'download'     => '<path d="M12 3v12"/><path d="M7 11l5 5 5-5"/><path d="M4 21h16"/>',
        'external'     => '<path d="M14 4h6v6"/><path d="M20 4 11 13"/><path d="M18 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h5"/>',
        'euro'         => '<path d="M18 6.5A7 7 0 0 0 8 10a7 7 0 0 0 10 7.5"/><path d="M4 10h9M4 14h9"/>',
        'users'        => '<circle cx="9" cy="8" r="3.4"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 5.4a3.4 3.4 0 0 1 0 6.2"/><path d="M18 14.6a6.5 6.5 0 0 1 3.5 5.4"/>',
        'shield'       => '<path d="M12 3l8 3v6c0 5-3.4 8.3-8 9.6C7.4 20.3 4 17 4 12V6z"/><path d="M9 12l2 2 4-4"/>',
        'sparkle'      => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/><path d="M18.5 16.5l.7 2 2 .7-2 .7-.7 2-.7-2-2-.7 2-.7z"/>',
        'sonar'        => '<circle cx="12" cy="12" r="2"/><path d="M12 12 19 5"/><path d="M4.5 12a7.5 7.5 0 0 1 7.5-7.5"/><path d="M1.5 12A10.5 10.5 0 0 1 12 1.5"/>',
        'sun'          => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4"/>',
        'moon'         => '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5z"/>',
        'menu'         => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close'        => '<path d="M6 6l12 12M18 6L6 18"/>',
        'plus'         => '<path d="M12 5v14M5 12h14"/>',
        'edit'         => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'trash'        => '<path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>',
        'logout'       => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',
        'check'        => '<path d="M20 6 9 17l-5-5"/>',
    ];
}

function render_icon(string $name, string $class = 'icon'): void
{
    $paths = icon_names();
    $path = $paths[$name] ?? $paths['bubbles'];
    echo '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $path . '</svg>';
}
