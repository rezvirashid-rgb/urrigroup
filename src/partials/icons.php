<?php
/** Inline SVG icon set (stroke icons inherit currentColor). */
function icon(string $name): string {
    $s = 'viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"';
    switch ($name) {
        case 'plane':    return "<svg $s><path d=\"M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z\"/></svg>";
        case 'box':      return "<svg $s><path d=\"M2 3h20v5H2z\"/><path d=\"M4 8v11a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V8\"/><path d=\"M9 12h6\"/></svg>";
        case 'building': return "<svg $s><path d=\"M3 21h18\"/><path d=\"M5 21V7l7-4 7 4v14\"/><path d=\"M9 21v-6h6v6\"/><path d=\"M9 10h.01M15 10h.01\"/></svg>";
        case 'chart':    return "<svg $s><path d=\"M3 3v18h18\"/><path d=\"M7 14l3-3 3 3 5-6\"/></svg>";
        case 'monitor':  return "<svg $s><rect x=\"4\" y=\"4\" width=\"16\" height=\"12\" rx=\"2\"/><path d=\"M2 20h20\"/><path d=\"M8 12l2-2-2-2M12 12h4\"/></svg>";
        case 'mail':     return "<svg $s><rect x=\"3\" y=\"5\" width=\"18\" height=\"14\" rx=\"2\"/><path d=\"m3 7 9 6 9-6\"/></svg>";
        case 'phone':    return "<svg $s><path d=\"M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.4-1.2a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z\"/></svg>";
        case 'globe':    return "<svg $s><circle cx=\"12\" cy=\"12\" r=\"10\"/><path d=\"M2 12h20\"/><path d=\"M12 2a15.3 15.3 0 0 1 0 20 15.3 15.3 0 0 1 0-20z\"/></svg>";
        case 'menu':     return "<svg $s class=\"icon-open\"><line x1=\"3\" y1=\"6\" x2=\"21\" y2=\"6\"/><line x1=\"3\" y1=\"12\" x2=\"21\" y2=\"12\"/><line x1=\"3\" y1=\"18\" x2=\"21\" y2=\"18\"/></svg>";
        case 'close':    return "<svg $s class=\"icon-close\"><line x1=\"6\" y1=\"6\" x2=\"18\" y2=\"18\"/><line x1=\"6\" y1=\"18\" x2=\"18\" y2=\"6\"/></svg>";
        case 'facebook': return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.7l-.4 2.9h-2.3v7A10 10 0 0 0 22 12z"/></svg>';
        case 'instagram':return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>';
        case 'linkedin': return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.7h.05c.53-1 1.83-2 3.76-2C20.3 8.7 22 10.5 22 14v7h-4v-6.2c0-1.5 0-3.4-2.06-3.4-2.07 0-2.39 1.6-2.39 3.3V21H9z"/></svg>';
        case 'logo':     return '<svg viewBox="0 0 64 64" width="34" height="34" aria-hidden="true" focusable="false"><rect width="64" height="64" rx="14" fill="#120B24"/><path d="M18 16 v18 a14 14 0 0 0 28 0 V16" fill="none" stroke="#4902BC" stroke-width="7" stroke-linecap="round"/><path d="M34 44 h12 m0 0 l-6 -6 m6 6 l-6 6" fill="none" stroke="#FFD319" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }
    return '';
}
