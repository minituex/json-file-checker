# Json File Checker

## Overview

This tool allows developers to ensure that all translation files/i18n json files
are complete on a key basis. 
Simply define a base language and compare all other files to this base file.

## Usage

Add the following to your composer.json

`"minituex/json-file-checker": "^1.0`

After updating composer you can now use jcheck as described:

### Options
`-b` defines the base language. Make sure to use the exact name of the file so "en" for en.json and "En" for En.json
`-d` the directory in which the language files are located.

**Example**

`php vendor/minituex/json-file-checker/src/jcheck.php -d frontend/asset/i18n/ -b en`

### Output

No output means you files are all correct, no keys are missing. 

If there are keys missing, the script will exit with code 1 and print a list of
keys by file.

### CI/CD

This script was intended for CI/CD integration. The quickest way is to define a composer script calling jcheck and then
calling this composer script from your CI/CD yaml, but you can also call jcheck directly in your CI/CD.

### Limitations

jcheck can only check for a complete set of keys however it cannot check the translations itself.
It is not meant as tool for translators but an aid for developers to make sure no keys are missed in
any file.