/** Two-letter initials for avatar placeholders (no image URLs in this app). */
export function memberInitials(name: string): string {
    const parts = name.trim().split(/\s+/).filter(Boolean);
    if (parts.length >= 2) {
        const first = parts[0]?.[0];
        const last = parts[parts.length - 1]?.[0];
        if (first && last) {
            return (first + last).toUpperCase();
        }
    }

    const compact = name.replace(/\s+/g, '');
    return compact.slice(0, 2).toUpperCase() || '?';
}
