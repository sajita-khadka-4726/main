module.exports = {
    "app/**/*.php": [
        "./vendor/bin/pint",
        "./vendor/bin/phpstan analyse --memory-limit=2G",
    ],
    "config/**/*.php": [
        "./vendor/bin/pint",
        "./vendor/bin/phpstan analyse --memory-limit=2G",
    ],
    "routes/**/*.php": [
        "./vendor/bin/pint",
        "./vendor/bin/phpstan analyse --memory-limit=2G",
    ],
    "tests/**/*.php": [
        "./vendor/bin/pint",
        "./vendor/bin/phpstan analyse --memory-limit=2G",
    ],
};
