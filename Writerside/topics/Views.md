# Views

<tip>
See <a href="https://platesphp.com/engine/overview/">Plates</a> for full documentation on views.
</tip>

## View folder
Views are in the `resources/views` folder.

## Creating layouts
See [resources/views/layouts/plugin.php](https://github.com/thecodezone/dt-plugin/blob/main/resources/views/layouts/plugin.php) for an example layout.

## Creating views
See [resources/views/hello.php](https://github.com/thecodezone/dt-plugin/blob/main/resources/views/hello.php) for an example view which extends that layout. 

## Rendering views
Call a view file in your controller by returning the `template()` helper. 

See the [HelloController](https://github.com/thecodezone/dt-plugin/blob/5826eb7349eab9b46e97cba9d7f9a4e04b226604/src/Controllers/HelloController.php#L41). The first parameter takes the view name. The second parameter takes the data that you would like to pass to the view. . 

### Template service
The above example uses the template helper, which uses the [template service](https://github.com/thecodezone/dt-plugin/blob/main/src/Services/Template.php) to render `wp_head` and `wp_foot` and include your asset files.   

### Rendering views without `wp_head` and `wp_foot`
To return partial views for AJAX or wp-admin responses, use the `view()` helper. 

See the [GeneralSettingsController](https://github.com/thecodezone/dt-plugin/blob/5826eb7349eab9b46e97cba9d7f9a4e04b226604/src/Controllers/Admin/GeneralSettingsController.php#L22) which uses the `view()` helper to render the admin views without the full template. 