# Assets

## Installing assets
Installing your assets by running the following command: 

`npm install`

## Building assets
Run the following command to compile your JavaScript and CSS. 

`npm run build` or `vite build`

## Building assets during development
To build assets during development, run the following command:

`npm run dev` or `vite`

## Editing assets
Editable asset files are in the`resources/css` and `resources/js` folders. See the [vite.js](https://vitejs.dev/) documentation for more information.

## Whitelisting assets
Assets are blacklisted by default. You must manually add assets to the `allowed_styles` and `allowed_scripts` configuration value in `config/assets.php` in order for them be included. 

```php
'assets' => [
    'allowed_styles' => [
        'your-style',
        'jquery',
    ],
    'allowed_scripts' =>[
        'your-script',
        'tailwind',
    ],
    ...
```

## Adding global JavaScript variables in PHP
You can share global variables with JavaScript.  

```php
'javascript_global_scope' => '$my_plugin',
'javascript_globals' => [
     'hello' => 'world'
]
```

In the example above, the variable window.$my_plugin.hello would be set to `world`.



