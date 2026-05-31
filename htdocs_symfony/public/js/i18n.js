/**
 * t(key, params) — look up a translated string from window.OCI18n.
 *
 * Strings are emitted server-side by Twig via | trans and baked into
 * window.OCI18n in the correct locale before any JS module runs.
 * Placeholders use %name% syntax (not ICU {name} or Twig {{ name }}).
 *
 * Falls back to the key itself so untranslated strings still render.
 */
export function t(key, params = {}) {
    let s = window.OCI18n?.[key] ?? key;
    for (const [k, v] of Object.entries(params))
        s = s.replace(`%${k}%`, v);
    return s;
}
