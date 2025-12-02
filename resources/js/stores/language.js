import Alpine from 'alpinejs';
import idTranslations from '../translations/id.js';
import enTranslations from '../translations/en.js';

const translations = {
    id: idTranslations,
    en: enTranslations
};

Alpine.store('language', {
    // Default to Indonesian
    current: localStorage.getItem('language') || 'id',

    // Initialize
    init() {
        // Update HTML lang attribute
        document.documentElement.lang = this.current;
    },

    // Toggle between Indonesian and English
    toggle() {
        this.current = this.current === 'id' ? 'en' : 'id';
        localStorage.setItem('language', this.current);
        document.documentElement.lang = this.current;

        // Dispatch event for components to react
        window.dispatchEvent(new CustomEvent('language-changed', {
            detail: { language: this.current }
        }));
    },

    // Set specific language
    setLanguage(lang) {
        if (translations[lang]) {
            this.current = lang;
            localStorage.setItem('language', lang);
            document.documentElement.lang = lang;

            window.dispatchEvent(new CustomEvent('language-changed', {
                detail: { language: lang }
            }));
        }
    },

    // Translate a key
    t(key) {
        const translation = translations[this.current][key];
        if (translation) {
            return translation;
        }
        // Fallback to key if translation not found
        console.warn(`Translation missing for key: ${key} in language: ${this.current}`);
        return key;
    },

    // Get current language flag
    getFlag() {
        return this.current === 'id' ? '🇮🇩' : '🇬🇧';
    },

    // Get current language name
    getName() {
        return this.current === 'id' ? 'Indonesia' : 'English';
    }
});
