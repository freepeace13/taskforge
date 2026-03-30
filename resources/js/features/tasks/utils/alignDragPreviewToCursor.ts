import type { DragEvent } from 'react';

/**
 * Keeps the browser drag preview under the pointer at the same point the user grabbed the card.
 *
 * Some browsers report `clientX` / `clientY` as 0 on `dragstart`; fall back to the card center.
 */
export function alignDragPreviewToCursor(event: DragEvent<HTMLDivElement>): void {
    const el = event.currentTarget;
    const rect = el.getBoundingClientRect();

    let offsetX = event.clientX - rect.left;
    let offsetY = event.clientY - rect.top;

    if (!Number.isFinite(offsetX) || offsetX < 0 || offsetX > rect.width) {
        offsetX = rect.width / 2;
    }
    if (!Number.isFinite(offsetY) || offsetY < 0 || offsetY > rect.height) {
        offsetY = rect.height / 2;
    }

    event.dataTransfer.setDragImage(el, offsetX, offsetY);
}
