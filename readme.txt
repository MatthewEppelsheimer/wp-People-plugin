TODO::: Remove this line when file is finished
=== People === 
Contributors: Kevin Lenihan
Donate link: http://example.com/
Tags: people, post type
Requires at least: 3.0.1
Tested up to: 3.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates a post type for people and provides useful filters to easily adapt the plugin to suite your needs.
    
== Description ==

People creates a post type that is designed to have the standard fields for most users, but also allows advanced users to easily adapt the plugin to suite their needs.

By default this plugin supports these fields for a Person:
Name ( Uses WordPress's prebuilt title field )
Title
Email
Bio ( Uses WordPress's prebuilt content field )
Brief Bio ( Uses WordPress's prebuilt excerpt field )
Order: Number used to sort the people (0 = High Priority 10 = Low Priority)

The following functions are made explicitly for the users to ... um ... use:
People_Post_Type::get_person() (or, if you don't like classes, people_get_person() does the same thing).
People_Post_Type::list_people( $args, $callback ) (or if you still don't like classes, people_list_people( $args, $callback ) will work).
People_Post_Type::render_single_person() (or if you are bent on not using classes, use people_render_single_person() ).

Next we have a list of filter and action hooks that allow users to modify this plugin severely without having to change plugin files, making updating a breeze. This is just a list of the hooks with a brief description of what they are for; an example for how to use them is farther below:

'people_create_metaboxes': Used to add the actually metabox
'people_atts': used to add a field to the array returned from People_Post_Type::get_person()
'people_item_callback': used to set a user defined default for how to render an person in a list of people.
'people_single_callback': used to set a user defined default for how to render a single person

'people_title_metabox_render': used to add fields to the title metabox
'people_email_metabox_render': used to add fields to the email metabox

Available Shortcodes
[people] : renders the list of people

== Installation ==

1. Upload `people-post-type/` to the `/wp-content/plugins/` directory
2. Activate the plugin, 'People', through the 'Plugins' menu in WordPress

== Adding your own Metabox ==

Note: Adding Metaboxes does require familiarality with WordPress coding and should only be done by those who are comfortable with filter and action hooks.
Examples for how to create metaboxes are in people-post-type/lib/defaults.php
Here is the basic template:

1. add_action( 'people_create_metaboxes', 
        function() {
            // replace the ~...~ with your info
            add_meta_box( '~metabox_id~', __( '~Metabox Display Name~', 'people' ), 'render_people_~metabox_name~_metabox', 'people', 'normal', 'high' );
        }
    );
    
2. function render_people_~metabox_name~_metabox() {
        // add code to render actual html
    }

3. add_action( 'save_post',
        function( $post_id ) {
            // add the code to save your new field(s)
        }
    );

4. add_filter( 'people_atts',
        function( $arr, $id ) {
            $arr['~meta_field_name~'] = ~meta_field_value~;
            return $arr;
        },
        2,
        2
    );


== Using the Shortcode [people] ==

You can simply use the shortcode [people] to generate a list of all people using the default display function. However, if this you want to display a person differently because you don't like the style of the default display or if you added meta fields that you want displayed, we have created a simple way to make your own display function.

1. add_filter('people_item_callback', 
    function(){
        $person = People_Post_Type::get_person(); // use this line to get an array of useful information about the person
        
        // html code for a single person
        // IMPORTANT: must return html as string. DO NOT echo html
        // Echoing the html will cause the shortcode to fail.
        /*
            Examples:
            ?> <div class=test> </div> <?php  BAD
            echo '<div class=test> </div>'; BAD
            return '<div class=test> </div>' GOOD
        */
    }
    );

