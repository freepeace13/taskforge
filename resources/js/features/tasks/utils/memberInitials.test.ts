import { describe, expect, it } from 'vitest';
import { memberInitials } from '@/features/tasks/utils/memberInitials';

describe('memberInitials', () => {
    it('uses first and last word initials when multiple words', () => {
        expect(memberInitials('Ada Lovelace')).toBe('AL');
    });

    it('uses first two letters for a single word', () => {
        expect(memberInitials('Ada')).toBe('AD');
    });

    it('returns placeholder for empty string', () => {
        expect(memberInitials('')).toBe('?');
    });
});
