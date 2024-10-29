=== Administrative Shortcodes ===
Contributors: shazdeh
Plugin Name: Administrative Shortcodes
Tags: shortcode, php, code, wpmu, context, conditional, conditional-tags, loop, date, schedule, content
Requires at least: 3.1
Tested up to: 5.7.1
Stable tag: 0.3.4

A set of shortcodes for the website administrators.

== Description ==

A set of shortcodes for the website administrators.

= if =
Using this shortcode you can limit the display of the text or shortcodes to specific page(s) on your website. You can use all the <a href="http://codex.wordpress.org/Conditional_Tags">conditional tags</a> WordPress provides. Checkout examples below.

Show text only on the homepage:
<pre><code>[if is_front_page] The text [/if]</code></pre>

Show text only on the About page of the site:
<pre><code>[if is_page="about"] The text [/if]</code></pre>

Show only on the category archive view:
<pre><code>[if is_category] The text [/if]</code></pre>

You can add "not_" before the conditional tag to reverse the logic, example:
Show on all pages of the site except the homepage:
<pre><code>[if not_is_front_page] The text [/if]</code></pre>

Using multiple parameters, the content is displayed when either of the conditions are met ("OR" comparison), for example, show text on both category and tag archive pages:
<pre><code>[if is_category is_tag] The text [/if]</code></pre>

To set multiple conditions you can nest the shortcode, for example show text only on homepage AND if the user is logged in:
<pre><code>[if is_user_logged_in][if is_front_page] The text [/if][/if]</code></pre>

Show a link to wordpress.org site, only on single post pages and only on mobile devices:
<pre><code>[if wp_is_mobile][if is_single] <a href="http://wordpress.org/">WordPress</a> [/if][/if]</code></pre>

= switch_to_blog =
Run shortcodes in the context of another website in the network. This shortcode is only available in multisite installations (WP Network). For example, list posts from another blog:
<pre><code>[switch_to_blog id="10"] [list_posts limit="10"] [/switch_to_blog]</code></pre>

= iterator =
Display text or shortcodes only if repeated a certain number of times. Requires an "id" parameter which should be unique for that shortcode (can be anything you want).
<pre><code>[iterator id="my-ads" repeat="5"] My ad codes [/iterator]</code></pre>
After every 5th call to that shortcode, render the output.

= get_template =
Load a template file into the page. The referenced file is loaded from child theme if it exists, if not from the parent theme. Example, load includes/slider.php template file from the theme:
<pre><code>[get_template slug="includes/slider"]</code></pre>

= scheduler =
Show text or shortcodes only if the specified date has passed. Example, show the text only if it's after Christmas of 2016:
<pre><code>[scheduler date="December 25, 2016"] Text [/scheduler]</code></pre>

= date =
Shows the current date or time in the specified format (http://codex.wordpress.org/Formatting_Date_and_Time). Uses Date Format (in Settings > General) by default.
<pre><code>[date format="F j, Y"]</code></pre>

= loginoutlink =
Display a link to login page if user is not logged-in, or a logout page if they are. Parameters:

* login : "Log in" text
* logout : "Log out" text
* redirect : optional URL to redirect to on login or logout
 
= login_form =
Display the login form.

= custom_field =
Display a custom field from a post. Parameters:

* key : name of the custom field to show. This parameter is required.
* post_id : the ID of the post to display the custom field from; by default uses the current post in the loop
* before : show a text to display before the value of the field
* after : show a text after the value of the field

= disable =
Wrap any content or shortcode with `[disable]` shortcode to prevent that piece of content from being rendered. Perfect for debugging shortcodes.

= the_id =
Shows the current post ID; useful when you need to debug WP Loops.

= Path shortcodes =
[home_url] : returns the homepage URL
[get_template_directory] : Absolute path to the parent theme directory
[get_template_directory_uri] : URL to the directory of the parent theme
[get_stylesheet_directory] : Absolute path to the directory of the child theme
[get_stylesheet_directory_uri] : URL to the directory of the child theme

= rand shortcodes =
Show a random number between two numbers. Parameters:
* min : minimum value (default 0)
* max : maximum value (default 100)

= get_var =
Retrieve a value from $_GET or $_POST variables.
To get a URL query string:
<pre><code>[get_var get="myvar"]</code></pre>
To get a value from POST:
<pre><code>[get_var post="myvar"]</code></pre>


== Installation ==

1. Upload the the plugin directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enjoy!