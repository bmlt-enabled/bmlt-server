import { describe, it, expect } from 'vitest';
import { hasValidCoordinates, stripLegacyFieldSeparator } from '../lib/utils';

describe('stripLegacyFieldSeparator', () => {
  it('should strip legacy separator and return value', () => {
    expect(stripLegacyFieldSeparator('Bus Lines#@-@#16')).toBe('16');
    expect(stripLegacyFieldSeparator('Train Lines#@-@#Green Line D')).toBe('Green Line D');
    expect(stripLegacyFieldSeparator('Boat Line#@-@#Steamship Authority')).toBe('Steamship Authority');
  });

  it('should return unchanged value when no separator present', () => {
    expect(stripLegacyFieldSeparator('16')).toBe('16');
    expect(stripLegacyFieldSeparator('Green Line D')).toBe('Green Line D');
    expect(stripLegacyFieldSeparator('Regular value')).toBe('Regular value');
  });

  it('should handle null and undefined values', () => {
    expect(stripLegacyFieldSeparator(null)).toBe('');
    expect(stripLegacyFieldSeparator(undefined)).toBe('');
  });

  it('should handle empty string', () => {
    expect(stripLegacyFieldSeparator('')).toBe('');
  });

  it('should trim whitespace from extracted value', () => {
    expect(stripLegacyFieldSeparator('Bus Lines#@-@#  16  ')).toBe('16');
    expect(stripLegacyFieldSeparator('Train Lines#@-@#   Green Line D   ')).toBe('Green Line D');
  });

  it('should handle multiple separators by using only the first split', () => {
    // split with limit 2 means only first occurrence is split
    expect(stripLegacyFieldSeparator('First#@-@#Second#@-@#Third')).toBe('Second');
  });

  it('should return original value if nothing after separator', () => {
    expect(stripLegacyFieldSeparator('Bus Lines#@-@#')).toBe('Bus Lines#@-@#');
  });
});

describe('hasValidCoordinates', () => {
  it('should accept manually entered string coordinates', () => {
    expect(hasValidCoordinates('40.7', '-74')).toBe(true);
  });

  it('should accept numeric coordinates', () => {
    expect(hasValidCoordinates(40.7128, -74.006)).toBe(true);
  });

  it('should accept coordinates at the valid range boundaries', () => {
    expect(hasValidCoordinates(-90, 180)).toBe(true);
    expect(hasValidCoordinates('90', '-180')).toBe(true);
  });

  it('should reject out-of-range coordinates', () => {
    expect(hasValidCoordinates('91', '0.1')).toBe(false);
    expect(hasValidCoordinates('10', '181')).toBe(false);
  });

  it('should reject blank, whitespace, and non-numeric input', () => {
    expect(hasValidCoordinates('', '')).toBe(false);
    expect(hasValidCoordinates('   ', '10')).toBe(false);
    expect(hasValidCoordinates('abc', '10')).toBe(false);
  });

  it('should reject the 0,0 null-island coordinate', () => {
    expect(hasValidCoordinates(0, 0)).toBe(false);
    expect(hasValidCoordinates('0', '0')).toBe(false);
  });
});
