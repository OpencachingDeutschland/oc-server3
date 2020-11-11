// By default, Klaro will load the config from  a global "klaroConfig" variable.
// You can change this by specifying the "data-config" attribute on your
// script take, e.g. like this:
// <script src="klaro.js" data-config="myConfigVariableName" />
// You can also disable auto-loading of the consent notice by adding
// data-no-auto-load=true to the script tag.
var klaroConfig = {
    // You can customize the ID of the DIV element that Klaro will create
    // when starting up. If undefined, Klaro will use 'klaro'.
    elementID: 'klaro',

    // How Klaro should store the user's preferences. It can be either 'cookie'
    // (the default) or 'localStorage'.
    storageMethod: 'cookie',

    // You can customize the name of the cookie that Klaro uses for storing
    // user consent decisions. If undefined, Klaro will use 'klaro'.
    cookieName: 'klaroConsent',

    // You can also set a custom expiration time for the Klaro cookie.
    // By default, it will expire after 120 days.
    cookieExpiresAfterDays: 365,

    // You can change to cookie domain for the consent manager itself.
    // Use this if you want to get consent once for multiple matching domains.
    // If undefined, Klaro will use the current domain.
    //cookieDomain: '.github.com',

    // Put a link to your privacy policy here (relative or absolute).
    privacyPolicy: '/page/datenschutzerklaerung/',

    // Defines the default state for applications (true=enabled by default).
    default: false,

    // If "mustConsent" is set to true, Klaro will directly display the consent
    // manager modal and not allow the user to close it before having actively
    // consented or declines the use of third-party apps.
    mustConsent: true,

    // Show "accept all" to accept all apps instead of "ok" that only accepts
    // required and "default: true" apps
    acceptAll: true,

    // replace "decline" with cookie manager modal
    hideDeclineAll: false,

    // You can define the UI language directly here. If undefined, Klaro will
    // use the value given in the global "lang" variable. If that does
    // not exist, it will use the value given in the "lang" attribute of your
    // HTML tag. If that also doesn't exist, it will use 'en'.
    //lang: 'de',

    // You can overwrite existing translations and add translations for your
    // app descriptions and purposes. See `src/translations/` for a full
    // list of translations that can be overwritten:
    // https://github.com/KIProtect/klaro/tree/master/src/translations

    // Example config that shows how to overwrite translations:
    // https://github.com/KIProtect/klaro/blob/master/src/configs/i18n.js
    translations: {
        // If you erase the "consentModal" translations, Klaro will use the
        // bundled translations.
        de: {
            consentModal: {
                description:
                    'Hier haben wir unsere eingesetzten Cookies zusammengefasst und erklärt.',
            },
            acceptSelected: "Speichern",
            ocSession: {
                description: 'Speichert aktuelle Sitzungsdaten wie die ausgewählte Sprache und den Login-Status.',
            },
            klaroConsent: {
                description: 'Das Klaro Consent Tool speichert die gegenwärtige Cookie Auswahl in einem Cookie.',
            },

            ocgmconfig: {
                description: 'Speichert die Einstellungen der Karte.',
            },
            googleAnalytics: {
                description: 'Wir erfassen Besucherzahlen um daraus Rückschlüsse für die Weiterentwicklung der Plattform zu generieren.',
            },
            purposes: {
                analytics: 'Besucher-Statistiken',
                comfort: 'Komfort',
                required: 'Technisch notwendig',
            },
        },
        en: {
            consentModal: {
                description:
                    'Here we have summarized and explained the cookies we use.',
            },
            acceptSelected: 'Save',
            ocSession: {
                description: 'Saves current session data like the selected language or your login status.',
            },
            klaroConsent: {
                description: 'The Klaro Consent Tool stores the current cookie selection in a cookie.',
            },
            ocgmconfig: {
                description: 'Saves oc map settings.',
            },
            googleAnalytics: {
                description: 'We record visitor statistics to generate conclusions for the further development of the OC site.',
            },
            purposes: {
                analytics: 'Analytics',
                comfort: 'Comfort',
                required: 'Technical required',
            },
        },
    },

    // This is a list of third-party apps that Klaro will manage for you.
    apps: [
        {
            name: 'ocSession',
            title: 'Session data',
            required: true,
            purposes: ['required'],
            cookies: ['ocdeudata', 'ocdeuhttps_session'],
        },
        {
            name: 'klaroConsent',
            title: 'Klaro Consent Tool',
            required: true,
            purposes: ['required'],
        },
        {
            name: 'ocgmconfig',
            title: 'Map configuration',
            purposes: ['comfort'],
            cookies: ['ocgmconfig','ocgmlastpos','ocgmfilter', 'ocgmfilter_saved'],
        },
        {
            name : 'googleAnalytics',
            title : 'Google Analytics',
            purposes : ['analytics'],
            cookies : ['_ga','_gid','_gat'],
            optOut: false,
        }
    ],
};
