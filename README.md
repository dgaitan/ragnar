# Ragnar
A plugin for WordPress Developers. This help you to build your custom theme or plugins with super powers. Only Extend the Ragnar Class into your Main Theme or Plugin class, and you'll have a lot of classes that help you to build post types, taxonomies, etc... with super powers.

```php
class MyTheme extends Ragnar {

    /**
     * Define your theme or plugin slug to translations
     * 
     * @var string
     */
    private $slug = 'my-slug';

    public function __construct() {
        parent::__construct(); // Intitalize Ragnar

        // Add here your custom methods

    }
}

// Initialize your theme or plugin
function MyTheme() {
    return MyTheme::instance();
}

MyTheme();

// You will can use this globally.
MyTheme()->users->all();
MyTheme()->users->get( 1 );
MyTheme()->posts->where( array( 'posts_per_page' => -1 ) );
MyTheme()->posts->get( 20 );
MyTheme()->helpers->any_helper_registered_before( $some_value );
```

Read more about how to use it in the ragnar wiki
