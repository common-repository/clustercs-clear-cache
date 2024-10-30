<?php
/**
 * Plugin Name:       ClusterCS Clear Cache
 * Plugin URI:        https://www.clustercs.com
 * Description:       Clear NGINX cache using ClusterCS control panel
 * Version:           1.0.1
 * Author:            SoftDreams
 * Author URI:        https://softdreams.eu
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       clustercs-clear-cache
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if(!class_exists('CLUSTER_CS_CLEAR_CACHE'))
{
    class CLUSTER_CS_CLEAR_CACHE
    {
        CONST plugin_slug = 'cluster-cs-clear-cache-plugin';
        CONST plugin_name = 'CCS Clear Cache';

        function defines()
        {
            defined('ccs_clear_cache_dir')  ||  define('ccs_clear_cache_dir', plugin_dir_path( __FILE__ ));
            defined('ccs_clear_cache_inc')  ||  define('ccs_clear_cache_inc', ccs_clear_cache_dir . 'inc/');
            defined('ccs_clear_cache_plugin_url') || define('ccs_clear_cache_plugin_url', plugin_dir_url( __FILE__ ));
            defined('ccs_clear_cache_images')  ||  define('ccs_clear_cache_images', ccs_clear_cache_plugin_url . 'img/');
        }

        function __construct()
        {
            $this->defines();

            add_action('admin_bar_menu', array(__CLASS__, 'cluster_cs_clear_cache_page_admin_bar'), 95);
            add_action('admin_menu', array(__CLASS__, 'cluster_cs_clear_cache_page'));
            add_action('admin_init', array(__CLASS__, 'ccs_plugin_init'));

            add_action('admin_init', array(__CLASS__, 'check_css_response'));
            add_action('admin_init', array(__CLASS__, 'load_style'));

            add_action('wp_enqueue_scripts', array(__CLASS__, 'check_css_response'));
            add_action('wp_enqueue_style', array(__CLASS__, 'load_style'));

            add_action('wp_enqueue_scripts', array(__CLASS__, 'load_style'));

            add_action('wp_ajax_check_cache_url', array(__CLASS__, 'check_cache_url'));
            add_action('wp_ajax_nopriv_check_cache_url', array(__CLASS__, 'check_cache_url'));

            add_action('wp_ajax_clear_cache_ajax', array(__CLASS__, 'clear_cache_ajax'));
            add_action('wp_ajax_nopriv_clear_cache_ajax', array(__CLASS__, 'clear_cache_ajax'));

            if(get_option("cache_url") !== '')
            {
                add_action('post_updated', array(__CLASS__, 'ccs_save_post_action'));
            }

            add_option('clear_error');
        }

        static function ccs_plugin_init()
        {
            register_setting(self::plugin_slug, 'cache_url');
            register_setting(self::plugin_slug, 'check_option');

            add_settings_section('ccs-general', 'General Settings', array(__CLASS__, 'general_settings_callback'), self::plugin_slug);

            add_settings_field('cache_url', 'Clear cache path: <span class="info-enable-cache first-span" >?</span>', array(__CLASS__, 'cache_url_callback'), self::plugin_slug, 'ccs-general');
            add_settings_field('check_option', 'Automatically delete page/post cache on edit: <span class="info-enable-cache second-span" >?</span>', array(__CLASS__, 'check_option_callback'), self::plugin_slug, 'ccs-general');
        }

        function clear_cache_ajax()
        {
            self::clear_cache_static();
        }

        function check_cache_url()
        {
            check_ajax_referer('ccs_clear_cache_nonce', 'security');

            $url = sanitize_text_field($_POST['target_url']);

            if($url !== '')
            {
                if(substr($url, 0, 1) != '/')
                {
                    $url = '/' . $url;
                }

                $cache_url = get_site_url() . $url . '/json';

                $get_json = file_get_contents($cache_url);

                $json = json_decode($get_json, true);

                if($json['cc_paths'] !== null)
                {
                    esc_html_e('true', 'text_domain');
                }
                else
                {
                    esc_html_e('false', 'text_domain');
                }
            }

            wp_die();
        }

        static function check_url_cache_response()
        {
            $url = get_option("cache_url");

            if(substr($url, 0, 1) != '/')
            {
                $url = '/' . $url;
            }

            $cache_url = get_site_url() . $url . '/json';

            $get_json = wp_remote_get($cache_url);

            $json = json_decode($get_json['body'], true);

            if($json['cc_paths'] !== null)
            {
                update_option('clear_error', '');

                return true;
            }
            else
            {
                update_option('clear_error', 2);

                return false;
            }
        }

        static function load_style()
        {
            wp_enqueue_style('ccsstyle', ccs_clear_cache_plugin_url . 'css/ccsstyle.css', [], true);
        }

        static function check_css_response()
        {
            wp_enqueue_script('custom_js', ccs_clear_cache_plugin_url . 'js/custom_script.js', array( 'jquery' ), null, true);

            wp_localize_script('custom_js', 'ajax_object', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'ajax_nonce' => wp_create_nonce('ccs_clear_cache_nonce'),
                    'clear_error' => get_option('clear_error'),
                    'url_cc' => get_option("cache_url"))
            );
        }

        static function general_settings_callback()
        {
            esc_html_e('General settings for clearing NGNIX cache using ClusterCS control panel', 'text_domain');
        }

        static function cache_url_callback()
        {
            ?>
            <input type="text" id="cache_url" name="cache_url" value="<?php esc_html_e(get_option("cache_url")); ?>" style="width: 50%; padding: 10px;" placeholder="Please paste the path from the ClusterCS clear cache rule" />
            <img id="success-icon" class="hide-item" src="<?php esc_html_e(ccs_clear_cache_images); ?>check.png" style="width:35px; position:absolute;" />
            <img id="error-icon" class="hide-item" src="<?php esc_html_e(ccs_clear_cache_images); ?>error.png" style="width:35px; position:absolute;" />
            <?php
        }

        static function check_option_callback()
        {
            $default_value = 0;

            $checked_value = get_option('check_option');

            if($checked_value !== '')
            {
                $default_value = $checked_value;
            }

            ?>
            <input type="radio" <?php esc_html_e(checked('0', $default_value, false)); ?> name="check_option" value="0" /> Yes <br>
            <input type="radio" <?php esc_html_e(checked('1', $default_value, false)); ?> name="check_option" value="1" /> No

            <?php
        }

        static function cluster_cs_clear_cache_page_admin_bar($admin_bar)
        {
            if(!current_user_can('edit_posts'))
            {
                return;
            }

            $error = '';
            $check = false;

            if(get_option("cache_url") != '')
            {
                $check = self::check_url_cache_response();
            }

            if($check)
            {
                $class = 'clear_cache_menu_item';
            }
            else
            {
                update_option('clear_error', '');

                $error = ' <span class="notification-bubble-toolbar">1</span>';
                $class = 'clear_cache_not_available';
            }

            $plugin_icon = '<img src="'.ccs_clear_cache_images.'logo_ccs.svg" style="vertical-align:middle; margin-right:5px; margin-top:6px; float: left" />';

            $args = array(
                'id'    => 'ccs-clear-cache-menu',
                'title' => $plugin_icon . self::plugin_name . $error,
                'href'  => get_admin_url(null, 'admin.php?page=' . self::plugin_slug),
                'meta'  => array(
                    'title' => self::plugin_name
                ),
            );

            $admin_bar->add_menu($args);

            $node_args = array(
                'parent'    => 'ccs-clear-cache-menu',
                'title' => 'Clear Site Cache',
                'href'  => "#",
                'id'  => "clear-cache-ajax",
                'meta'  => array(
                    'class' => $class,
                    'title' => ($check == true ? "Clear Site Cache" : "Clear Cache not available")
                )
            );

            $admin_bar->add_menu($node_args);
        }

        static function cluster_cs_clear_cache_page()
        {
            if(!current_user_can('edit_posts'))
            {
                return;
            }

            $check = false;
            $error = '';

            if(get_option("cache_url") != '')
            {
                $check = self::check_url_cache_response();
            }

            if($check === false)
            {
                update_option('clear_error', '');

                $error = ' <span class="notification-bubble-toolbar">1</span>';
            }

            add_menu_page(
                self::plugin_name,
                self::plugin_name . $error,
                'manage_options',
                self::plugin_slug,
                array(__CLASS__, 'display_options_page'),
                ccs_clear_cache_images.'logo_ccs.svg',
                null
            );
        }

        static function display_options_page()
        {
            include_once(ccs_clear_cache_inc.'options.php');
        }

        static function ccs_save_post_action($post_id = 0)
        {
            if(wp_is_post_revision($post_id))
            {
                echo 'righ';
                return;
            }
            else
            {
                if(intval(get_option('check_option')) === 0)
                {
                    self::clear_cache_static(true);
                }
            }
        }

        static function clear_cache_static($all = null)
        {
            if(wp_doing_ajax())
            {
                check_ajax_referer('ccs_clear_cache_nonce', 'security');
            }

            if(get_option("cache_url") !== '')
            {
                global $post;

                $url_cache = get_option("cache_url");

                if(substr(get_option("cache_url"), 0, 1) != '/')
                {
                    $url_cache = '/' . $url_cache;
                }

                $cache_url = get_site_url() . $url_cache . '/json';

                $get_json = file_get_contents($cache_url);
                $json = json_decode($get_json, true);

                $base_url = $json['cc_paths']['base_url'];

                $errors = [];

                foreach($json['cc_paths']['server_paths'] as $single_server_path)
                {
                    $args = array(
                        'timeout' => 10,
                        'sslverify' => true,
                        'headers' => [
                            'cc_url' => ($all === true ? get_the_permalink($post->ID) : '')
                        ]
                    );

                    $request = wp_remote_get($base_url.$single_server_path, $args);

                    if($request['response']['code'] != 200)
                    {
                        update_option('clear_error', 1);
                        $errors [] = true;

                    }
                    elseif($request['response']['code'] == 200)
                    {
                        update_option('clear_error', '');
                    }
                }

                if($all === null)
                {
                    if(in_array(true, $errors))
                    {
                        esc_html_e('false', 'text_domain');
                    }
                    elseif(!in_array(true, $errors))
                    {
                        esc_html_e('done', 'text_domain');
                    }
                }
            }
            elseif (get_option('clear_error') != '')
            {
                update_option('clear_error', '');
            }

            if(wp_doing_ajax())
            {
                wp_die();
            }
        }
    }
}

new CLUSTER_CS_CLEAR_CACHE();
