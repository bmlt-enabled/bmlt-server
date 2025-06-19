import LocalizedStrings from 'localized-strings';
import { type LocaleObject, setLocale } from 'yup';

import { writable } from 'svelte/store';
import type { Subscriber, Unsubscriber } from 'svelte/store';
import {
  daTranslations,
  daYupLocale,
  deTranslations,
  deYupLocale,
  enTranslations,
  enYupLocale,
  esTranslations,
  esYupLocale,
  faTranslations,
  faYupLocale,
  frTranslations,
  frYupLocale,
  itTranslations,
  itYupLocale,
  plTranslations,
  plYupLocale,
  ruTranslations,
  ruYupLocale,
  svTranslations,
  svYupLocale,
  ptTranslations,
  ptYupLocale
} from '../lang';

/*eslint sort-keys: ["error", "asc", {caseSensitive: false}]*/
const strings = new (LocalizedStrings as any)({
  da: daTranslations,
  de: deTranslations,
  en: enTranslations,
  es: esTranslations,
  fa: faTranslations,
  fr: frTranslations,
  it: itTranslations,
  pl: plTranslations,
  pt: ptTranslations,
  ru: ruTranslations,
  sv: svTranslations
});

export const yupLocales: Record<string, LocaleObject> = {
  da: daYupLocale,
  de: deYupLocale,
  en: enYupLocale,
  es: esYupLocale,
  fa: faYupLocale,
  fr: frYupLocale,
  it: itYupLocale,
  pl: plYupLocale,
  pt: ptYupLocale,
  ru: ruYupLocale,
  sv: svYupLocale
};

const LANGUAGE_STORAGE_KEY = 'bmltLanguage';

class Translations {
  private store = writable(strings);

  constructor() {
    const language = localStorage.getItem(LANGUAGE_STORAGE_KEY) || settings.defaultLanguage;
    strings.setLanguage(language);
    this.store.set(strings);
    setLocale(yupLocales[language] || yupLocales[settings.defaultLanguage]);
  }

  get subscribe(): (run: Subscriber<typeof strings>) => Unsubscriber {
    return this.store.subscribe;
  }

  getLanguage(): string {
    return strings.getLanguage();
  }

  getAvailableLanguages(): string[] {
    return strings.getAvailableLanguages();
  }

  setLanguage(language: string): void {
    strings.setLanguage(language);
    localStorage.setItem(LANGUAGE_STORAGE_KEY, language);
    this.store.set(strings);
  }

  getString(key: string, language?: string): string {
    return strings.getString(key, language ?? this.getLanguage());
  }

  getTranslationsForLanguage(language: string | null = null): Record<string, string> {
    return strings.getContent()[language ?? this.getLanguage()];
  }

  getTranslationsForAllLanguages(): Record<string, Record<string, string>> {
    return strings.getContent();
  }
}

export const translations = new Translations();
