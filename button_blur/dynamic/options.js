// define available config options
var colors = {
    "gold": "Gold",
    "light gray": "LightGray",
    "dark gray": "DarkGray"
};

var sizes = {
    "small": "small",
    "medium": "medium",
    "large": "large",
    "x-large": "x-large"
}

var types = {
    "Pay with Amazon": "PwA",
    "Pay": "Pay",
    "Login with Amazon": "LwA",
    "Login": "Login",
    "Logo": "A"
}

var languages = {
    "Germany's German": "de_DE",
    "UK English": "en_GB",
    "France's French": "fr_FR",
    "Italy's Italian": "it_IT",
    "Spain's Spanish": "es_ES"
}

var environments = {
    "Sandbox":"sandbox",
    "Live":"live"
}

var regions = {
    "United Kingdom":"uk",
    "Germany":"de"
}

var widgetsjsUrls = {
    "uk" : {
        "live" : "https://static-eu.payments-amazon.com/OffAmazonPayments/uk/lpa/js/Widgets.js",
        "sandbox": "https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js"
    },
    "de": {
        "live" : "https://static-eu.payments-amazon.com/OffAmazonPayments/de/lpa/js/Widgets.js",
        "sandbox": "https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js"
    },
    "us": {
        "live" : "https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js",
        "sandbox":"https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js"
    }
}