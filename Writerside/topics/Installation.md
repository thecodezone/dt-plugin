# Installation

1. Click the "Use this template button" in the top-right corner of the GitHub page.
1. Edit the `bin/.setup.sh` script, updating each variable to match your plugin.
1. Run `bin/.setup.sh`
1. Edit `composer.json` to update the `name`, `description`, and `author` fields.
1. Replace the `DT//Plugin` namespace with your namespace in the `composer.json` `autoload` and `extra.wpify-scoper`
   sections.
1. Edit `package.json` to update the `name` and `description` fields.
1. Edit `version-control.json` with your plugin information.
1. Run `composer install` to install PHP dependencies.
1. Run `npm install` to install JS dependencies.
1. Run `npm run dev` to compile assets for development.
1. Commit and push your changes a new GitHub repository.
1. Open the WordPress admin and activate your plugin.
