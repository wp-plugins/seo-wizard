<?php

if ( ! class_exists( 'WSW_Dashboard' ) ) {

    /**
     * Handles plugin settings and user profile meta fields
     */
    class WSW_Dashboard extends WSW_Module {

        const page_id = 'wsw_dashboard_page';
        const PREFIX     = 'wsw-';

        protected $modules;
        protected  $page_hook;
        static $post_types_to_ignore;

        /**
         * Constructor
         */
        protected function __construct() {
            self::$post_types_to_ignore = array('thirstylink');
            $this->register_hook_callbacks();

        }

        /**
         * Register callbacks for actions and filters
         */
        public function register_hook_callbacks() {

            add_action('admin_menu',                        __CLASS__ . '::register_settings_pages' );

            add_filter('manage_posts_columns',              __CLASS__ . '::handle_add_columns', 10, 2);
            add_filter('manage_pages_columns',              __CLASS__ . '::handle_add_columns', 10, 2);

            add_action('manage_posts_custom_column',        __CLASS__ . '::handle_show_columns_values', 10, 2);
            add_action('manage_pages_custom_column',        __CLASS__ . '::handle_show_columns_values', 10, 2);

            add_action('seowizard_sitemap_event', __CLASS__ . '::generate_sitemap_cron');

            add_action( 'template_redirect', __CLASS__ . '::log_404s'  );
        }

        function log_404s () {
            if( !is_404() )  return;

            $data = array(
                'date' => current_time('mysql'),
                'url' => $_SERVER['REQUEST_URI']
            );
            $data['ip'] = $_SERVER['REMOTE_ADDR'];

            $data['ref'] = $_SERVER['HTTP_REFERER'];

            $data['ua'] = $_SERVER['HTTP_USER_AGENT'];

            WSW_Model_Log::add_record($data);
        }

        /**
         * Called via the cron job for actually generating the sitemap.
         */
        function generate_sitemap_cron() {
            self::build_sitemap_by_cron();
        } // end generate_sitemap_cron

        /**
         * Returns the root directory of a WordPress installation. This is used
         * to write the sitemap into the site's root.
         *
         * http://stackoverflow.com/questions/2354633/wordpress-root-directory
         */

        function get_sitemap_location() {

            $base = dirname(__FILE__);

            $path = false;
            if (@file_exists(dirname(dirname(dirname($base)))."/wp-config.php"))
            {
                $path = dirname(dirname(dirname($base)))."/sitemap.xml";
            }
            else{
                if (@file_exists(dirname(dirname(dirname(dirname($base))))."/wp-config.php"))
                {
                    $path = dirname(dirname(dirname(dirname($base))))."/sitemap.xml";
                }
                else
                    $path = false;
            }

            if ($path != false)
            {
                $path = str_replace("\\", "/", $path);
            }


            return $path;

        } // end get_sitemap_location
        /**
         * Writes the header of the XML file to the sitemap.
         *
         * @handle	The resource used to write data to disk.
         */
        function write_sitemap_header($handle) {

            $sitemap = '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?>';
            $sitemap .= "\n";
            $sitemap .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
            $sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
            $sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $sitemap .= "\n";
            $sitemap .= '';

            fwrite($handle, $sitemap);

        } // end write_sitemap_header

        /**
         * Creates a gzip archive of the sitemap and writes it to disk.
         */
        function gzip_sitemap() {

            $handle = fopen(self::get_sitemap_location() . '.gz', 'w');
            $sitemap = implode("", file(self::get_sitemap_location()));
            if($handle != null) {
                fwrite($handle, gzencode($sitemap, 9));
                fclose($handle);
            } // end if

        } // end gzip_sitemap

        function build_sitemap_by_cron() {
            if(WSW_Main::$settings['chk_make_sitemap'] == '1'){
                self::build_sitemap();
            }
        }


        static public function ajax_build_sitemap() {
            $settings = WSW_Main::$settings;
            $settings['chk_make_sitemap'] = '1';
            WSW_Settings::update_options($settings);
            self::build_sitemap();
        }


        /**
         * Actually generates the sitemap. The function is accessible via the administrator's
         * dashboard and by the WordPress scheduler.
         *
         * @is_scheduled	Whether or not this function is being called via the scheduler.
         */
        static public function build_sitemap() {

            global $wpdb;

            $all_posts = array();		// track all of the posts that we've written
            $limit = 25;						// process 25 rows at a time (for big blogs)
            $processed_posts = 0; 	// used to track how many rows we've processed

            $total_posts = $wpdb->get_var("
			select count(ID)
			from $wpdb->posts
			where post_status = 'publish' and post_password = '' and post_type in ('post', 'page')
		");

            // open the file to begin writing the sitemap
            $handle = fopen(self::get_sitemap_location(), 'w');
            if($handle == null) { // we don't have permissions to do this. throw up an error message.

                exit;
            } // end if

            self::write_sitemap_header($handle);

            // first, write the homepage...
            $homepage = "\t<url>\n";
            $homepage .= "\t\t<loc>" . get_bloginfo('url') . "/</loc>\n";
            $homepage .= "\t\t<changefreq>daily</changefreq>\n";
            $homepage .= "\t\t<priority>1</priority>\n";
            $homepage .= "\t</url>\n";
            fwrite($handle, $homepage);

            // and now we write the posts...
            while($total_posts > $processed_posts) {

                $posts = $wpdb->get_results("
				select id, post_title, post_name, post_modified, post_type, guid
				from $wpdb->posts where post_status = 'publish' and post_password = '' and post_type in ('post', 'page')
				order by post_modified DESC
				limit $limit offset $processed_posts
			");

                // write a sitemap entry for each URL in the array
                foreach($posts as $post) {

                    // create the entry for this post
                    $current_post = '';
                    $current_post .= "\t<url>\n";
                    $current_post .= "\t\t<loc>" . get_permalink($post->id) . "</loc>\n";
                    $current_post .= "\t\t<lastmod>" . date(DATE_W3C, strtotime($post->post_modified)) . "</lastmod>\n";

                    if($post->post_type == 'post') {
                        $current_post .= "\t\t<changefreq>weekly</changefreq>\n";
                        $current_post .= "\t\t<priority>" . 0.8 . "</priority>\n";
                    } else {
                        $current_post .= "\t\t<changefreq>monthly</changefreq>\n";
                        $current_post .= "\t\t<priority>" . 0.6 . "</priority>\n";
                    } // end if/else

                    $current_post .= "\t</url>\n";

                    // only write this entry if we've not already done so
                    if(!in_array($post->guid, $all_posts)) {
                        fwrite($handle, $current_post);
                        $all_posts[] = $post->guid;
                    } // end if

                } // end foreach

                $processed_posts += $limit;

            } // end while

            // write out the footer
            $footer = '</urlset>';
            fwrite($handle, $footer);

            $date = date('F j, Y');


            // gzip the sitemap
            self::gzip_sitemap();


            // submit to google and bing (stamp the time for reference)
            $time = date('g:i a');
            $sitemap_url = urlencode(get_bloginfo('url') . '/sitemap.xml.gz');

            $response = wp_remote_get('http://www.google.com/webmasters/tools/ping?sitemap=' . $sitemap_url);
            if(!is_wp_error($response) && $response['response']['code'] == '200') {
                fwrite($handle, "<!-- Successfully submitted sitemap to Google on " . $date . " (" . $time . ") -->\n");
            } // end if

            $response = wp_remote_get('http://www.bing.com/webmaster/ping.aspx?sitemap=' . $sitemap_url);
            if(!is_wp_error($response) && $response['response']['code'] == '200') {
                fwrite($handle, "<!-- Successfully submitted sitemap to Bing on " . $date . " (" . $time . ") -->\n");
            } // end if

            fclose($handle);
            $wpdb->flush();


        } // end generate_sitemap


        /**
         * Add columns to Posts/Pages
         *
         * @param $posts_columns
         * @version	v5.0
         *
         */
        function handle_add_columns($posts_columns) {

            $posts_columns['wsw_keyword'] = 'Keyword';
            $posts_columns['wsw_score'] = 'Score';
            return $posts_columns;
        }

        /**
         * Show values of new added columns of Posts/Pages
         *
         * @param $column_name
         * @param $post_id
         * @version v5.0
         */
        function handle_show_columns_values($column_name, $post_id) {
            try {
                $settings = get_post_meta( $post_id , 'wsw-settings');

                if(isset($settings)){
                    $keywords = $settings[0]['keyword_value'];

                    $post    = get_post( $post_id );
                    $post_content = $post->post_content;

                    // Show only if some keyword defined
                    if ($keywords!='') {
                        $score = WSW_Calc::calc_post_score($post_content);
                    }
                    else {
                        $score = 0.00;
                    }

                    if ('wsw_keyword' == $column_name) {
                        echo '<span class="wsw-keyword-wrapper">' . $keywords . '</span>';
                    }
                    elseif ('wsw_score' == $column_name) {
                        echo '<span class="wsw-score-wrapper">' . $score . '</span>';
                    }
                    else {
                        echo '';
                    }
                }
            } catch (Exception $e) {
                echo '';
            }
        }

        /**
         * Adds pages to the Admin Panel menu
         */
        public function register_settings_pages() {

            $hook = add_menu_page(WSW_NAME . ' Settings', WSW_NAME , 'manage_options', self::page_id, __CLASS__ . '::markup_dashboard_page');
            //  $hook = add_submenu_page(self::page_id, 'Dashboard', 'Dashboard', 'manage_options',self::page_id, __CLASS__ . '::markup_dashboard_page');

            add_action( 'admin_print_scripts-' . $hook, __CLASS__ . '::enqueue_scripts');
            add_action( 'admin_print_styles-' . $hook, __CLASS__ . '::enqueue_styles');

            $types_to_have_boxes = array_merge(array('post','page'),get_post_types(array('_builtin'=>false,'public'=>true),'names'));

            foreach ($types_to_have_boxes as $types_to_have_boxes_name) {
                if (!in_array($types_to_have_boxes_name, self::$post_types_to_ignore)) {
                    $metabox_below	= add_meta_box( 'WSW_metabox_below', 'SEOWizard Settings', __CLASS__ . '::show_metabox_below', $types_to_have_boxes_name,'normal','core');

                }
            }
        }

        /**
         * enqueue scripts of plugin
         */
        function enqueue_scripts()
        {

        }

        /**
         * enqueue style sheets of plugin
         */
        function enqueue_styles()
        {

            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-datepicker' );

            wp_enqueue_style( 'datepickercss',
                plugins_url( 'css/ui-lightness/ jquery-ui-1.8.17.custom.css',
                    dirname(__FILE__) ), array(), '1.8.17' );

            wp_enqueue_script( self::PREFIX . 'bootstrap-js' );
            wp_localize_script(self::PREFIX . 'admin-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
            wp_enqueue_script( self::PREFIX . 'admin-js' );
            wp_enqueue_script(self::PREFIX . 'colorpicker-js');
            wp_enqueue_script( self::PREFIX . 'zozo-js' );

            wp_enqueue_style( self::PREFIX . 'admin-css' );
            wp_enqueue_style( self::PREFIX . 'bootstrap-css' );

            wp_enqueue_style( self::PREFIX . 'zozo-tab-css' );
            wp_enqueue_style( self::PREFIX . 'zozo-tab-flat-css' );
            wp_enqueue_style(self::PREFIX . 'colorpicker-css');
        }

        /**
         * Creates the markup for the Dashboard page
         */
        public function markup_dashboard_page() {

            WSW_Main::markup_settings_header();
            if ( current_user_can( WSW_Main::REQUIRED_CAPABILITY ) ) {

                $variables = array();

                $variables['chk_keyword_to_titles'] = WSW_Main::$settings['chk_keyword_to_titles'];
                $variables['chk_tweak_permalink'] = WSW_Main::$settings['chk_tweak_permalink'];

                $variables['chk_nofollow_in_external'] = WSW_Main::$settings['chk_nofollow_in_external'];
                $variables['chk_nofollow_in_image'] = WSW_Main::$settings['chk_nofollow_in_image'];
                $variables['chk_use_facebook'] = WSW_Main::$settings['chk_use_facebook'];

                $variables['chk_use_twitter'] = WSW_Main::$settings['chk_use_twitter'];
                $variables['chk_use_meta_robot'] = WSW_Main::$settings['chk_use_meta_robot'];
                $variables['chk_use_richsnippets'] = WSW_Main::$settings['chk_use_richsnippets'];

                $variables['chk_keyword_decorate_bold'] = WSW_Main::$settings['chk_keyword_decorate_bold'];
                $variables['chk_keyword_decorate_italic'] = WSW_Main::$settings['chk_keyword_decorate_italic'];
                $variables['chk_keyword_decorate_underline'] = WSW_Main::$settings['chk_keyword_decorate_underline'];
                $variables['chk_make_sitemap'] = WSW_Main::$settings['chk_make_sitemap'];

                $variables['opt_keyword_decorate_bold_type'] = WSW_Main::$settings['opt_keyword_decorate_bold_type'];
                $variables['opt_keyword_decorate_italic_type'] = WSW_Main::$settings['opt_keyword_decorate_italic_type'];
                $variables['opt_keyword_decorate_underline_type'] = WSW_Main::$settings['opt_keyword_decorate_underline_type'];

                $variables['opt_image_alternate_type'] = WSW_Main::$settings['opt_image_alternate_type'];
                $variables['opt_image_title_type'] = WSW_Main::$settings['opt_image_title_type'];
                $variables['txt_image_alternate'] = WSW_Main::$settings['txt_image_alternate'];
                $variables['txt_image_title'] = WSW_Main::$settings['txt_image_title'];

                $variables['chk_use_headings_h1'] = WSW_Main::$settings['chk_use_headings_h1'];
                $variables['chk_use_headings_h2'] = WSW_Main::$settings['chk_use_headings_h2'];
                $variables['chk_use_headings_h3'] = WSW_Main::$settings['chk_use_headings_h3'];
                $variables['chk_tagging_using_google'] = WSW_Main::$settings['chk_tagging_using_google'];
                $variables['txt_generic_tags'] = WSW_Main::$settings['txt_generic_tags'];

                $variables['chk_author_linking'] = WSW_Main::$settings['chk_author_linking'];
                $variables['chk_block_login_page'] = WSW_Main::$settings['chk_block_login_page'];
                $variables['chk_block_admin_page'] = WSW_Main::$settings['chk_block_admin_page'];
                $variables['lsi_bing_api_key'] = WSW_Main::$settings['lsi_bing_api_key'];

                $variables['first_tab'] = 'tab1';

                if($_GET['action'] == 'Trash'){
                    WSW_Model_Log::remove_record($_GET['book']);
                    $variables['first_tab'] = 'tab4';
                }else if($_GET['paged']){

                    $variables['first_tab'] = 'tab4';
                }

                if($_POST['page'] == 'wsw_log_404'){

                    if($_POST['log404']){
                        foreach( $_POST['log404'] as $log404 ){
                            WSW_Model_Log::remove_record($log404);
                        }
                    }
                    $variables['first_tab'] = 'tab4';
                }

                echo self::render_template( 'global-settings/page-dashboard.php', $variables );
            }
            else {
                wp_die( 'Access denied.' );
            }
        }



        /** Ajax module for save Global Settings */
        static public function ajax_save_global_settings(){

            $options = WSW_Main::$settings;


            if(!is_null($_POST['chk_keyword_to_titles'])) $options['chk_keyword_to_titles'] = $_POST['chk_keyword_to_titles'];

            if(!is_null($_POST['chk_tweak_permalink'])) $options['chk_tweak_permalink'] = $_POST['chk_tweak_permalink'];
            if(!is_null($_POST['chk_nofollow_in_external'])) $options['chk_nofollow_in_external'] = $_POST['chk_nofollow_in_external'];
            if(!is_null($_POST['chk_nofollow_in_image'])) $options['chk_nofollow_in_image'] = $_POST['chk_nofollow_in_image'];
            if(!is_null($_POST['chk_use_facebook'])) $options['chk_use_facebook'] = $_POST['chk_use_facebook'];
            if(!is_null($_POST['chk_use_twitter'])) $options['chk_use_twitter'] = $_POST['chk_use_twitter'];
            if(!is_null($_POST['chk_use_meta_robot'])) $options['chk_use_meta_robot'] = $_POST['chk_use_meta_robot'];
            if(!is_null($_POST['chk_use_richsnippets'])) $options['chk_use_richsnippets'] = $_POST['chk_use_richsnippets'];
            if(!is_null($_POST['chk_author_linking'])) $options['chk_author_linking'] = $_POST['chk_author_linking'];

            if(!is_null($_POST['chk_keyword_decorate_bold'])) $options['chk_keyword_decorate_bold'] = $_POST['chk_keyword_decorate_bold'];
            if(!is_null($_POST['chk_keyword_decorate_italic'])) $options['chk_keyword_decorate_italic'] = $_POST['chk_keyword_decorate_italic'];
            if(!is_null($_POST['chk_keyword_decorate_underline'])) $options['chk_keyword_decorate_underline'] = $_POST['chk_keyword_decorate_underline'];
            if(!is_null($_POST['chk_make_sitemap'])) $options['chk_make_sitemap'] = $_POST['chk_make_sitemap'];

            if(!is_null($_POST['opt_keyword_decorate_bold_type'])) $options['opt_keyword_decorate_bold_type'] = $_POST['opt_keyword_decorate_bold_type'];
            if(!is_null($_POST['opt_keyword_decorate_italic_type'])) $options['opt_keyword_decorate_italic_type'] = $_POST['opt_keyword_decorate_italic_type'];
            if(!is_null($_POST['opt_keyword_decorate_underline_type'])) $options['opt_keyword_decorate_underline_type'] = $_POST['opt_keyword_decorate_underline_type'];

            if(!is_null($_POST['opt_image_alternate_type'])) $options['opt_image_alternate_type'] = $_POST['opt_image_alternate_type'];
            if(!is_null($_POST['opt_image_title_type'])) $options['opt_image_title_type'] = $_POST['opt_image_title_type'];
            if(!is_null($_POST['txt_image_alternate'])) $options['txt_image_alternate'] = $_POST['txt_image_alternate'];
            if(!is_null($_POST['txt_image_title'])) $options['txt_image_title'] = $_POST['txt_image_title'];

            if(!is_null($_POST['txt_generic_tags'])) $options['txt_generic_tags'] = $_POST['txt_generic_tags'];
            if(!is_null($_POST['chk_tagging_using_google'])) $options['chk_tagging_using_google'] = $_POST['chk_tagging_using_google'];


            if(!is_null($_POST['chk_block_login_page'])) $options['chk_block_login_page'] = $_POST['chk_block_login_page'];
            if(!is_null($_POST['chk_block_admin_page'])) $options['chk_block_admin_page'] = $_POST['chk_block_admin_page'];

            if(!is_null($_POST['lsi_bing_api_key'])) $options['lsi_bing_api_key'] = $_POST['lsi_bing_api_key'];

            WSW_Settings::update_options($options);

            die();
        }


        /**
         * Save Post Settings
         * */
        function save_post_settings()
        {
            wp_redirect(add_query_arg('page', WSW_Dashboard::page_id , admin_url('admin.php')));
            add_notice( ' Save Settings' , 'update' );
            exit;
        }

        /** Ajax module for save post */
        static public function ajax_save_post_settings(){
            $settings = array();

            $post_id = $_POST['post_id'];
            $settings['keyword_value'] = $_POST['keyword_value'];
            $settings['is_meta_keyword'] = $_POST['is_meta_keyword'];
            $settings['meta_keyword_type'] = $_POST['meta_keyword_type'];
            $settings['is_meta_title'] = $_POST['is_meta_title'];
            $settings['meta_title'] = $_POST['meta_title'];
            $settings['is_meta_description'] = $_POST['is_meta_description'];
            $settings['is_meta_robot_noindex'] = $_POST['is_meta_robot_noindex'];
            $settings['is_meta_robot_nofollow'] = $_POST['is_meta_robot_nofollow'];
            $settings['meta_description'] = $_POST['meta_description'];
            $settings['is_over_sentences'] = $_POST['is_over_sentences'];
            $settings['first_over_sentences'] = $_POST['first_over_sentences'];
            $settings['last_over_sentences'] = $_POST['last_over_sentences'];

            $settings['is_rich_snippets'] = $_POST['is_rich_snippets'];
            $settings['show_rich_snippets'] = $_POST['show_rich_snippets'];
            $settings['rating_value'] = $_POST['rating_value'];
            $settings['review_author'] = $_POST['review_author'];
            $settings['review_summary'] = $_POST['review_summary'];
            $settings['review_description'] = $_POST['review_description'];

            $settings['event_name'] = $_POST['event_name'];
            $settings['event_date'] = $_POST['event_date'];
            $settings['event_url'] = $_POST['event_url'];
            $settings['event_location_name'] = $_POST['event_location_name'];
            $settings['event_location_street'] = $_POST['event_location_street'];
            $settings['event_location_locality'] = $_POST['event_location_locality'];
            $settings['event_location_region'] = $_POST['event_location_region'];

            $settings['people_fname'] = $_POST['people_fname'];
            $settings['people_lname'] = $_POST['people_lname'];
            $settings['people_locality'] = $_POST['people_locality'];
            $settings['people_region'] = $_POST['people_region'];
            $settings['people_title'] = $_POST['people_title'];
            $settings['people_homeurl'] = $_POST['people_homeurl'];
            $settings['people_photourl'] = $_POST['people_photourl'];

            $settings['product_name'] = $_POST['product_name'];
            $settings['product_imageurl'] = $_POST['product_imageurl'];
            $settings['product_description'] = $_POST['product_description'];
            $settings['product_offers'] = $_POST['product_offers'];


            $settings['is_social_facebook'] = $_POST['is_social_facebook'];
            $settings['social_facebook_publisher'] = $_POST['social_facebook_publisher'];
            $settings['social_facebook_author'] = $_POST['social_facebook_author'];
            $settings['social_facebook_title'] = $_POST['social_facebook_title'];
            $settings['social_facebook_description'] = $_POST['social_facebook_description'];

            $settings['is_social_twitter'] = $_POST['is_social_twitter'];
            $settings['social_twitter_title'] = $_POST['social_twitter_title'];
            $settings['social_twitter_description'] = $_POST['social_twitter_description'];

            update_post_meta($post_id, 'wsw-settings', $settings);

            die();
        }

        /**
         * Display box in add/edit post/page page to Show the Score
         * @global	$post	POST Object
         * @return 	void
         * @access 	public
         */
        function show_metabox_below() {
            global $post;

            $variables = array();
            $variables['wsw_post_id'] = $post->ID;

            $settings = get_post_meta( $post->ID , 'wsw-settings');

            if(isset($settings)){

                /** General Settings */
                $variables['wsw_keyword_value'] = $settings[0]['keyword_value'];
                $variables['wsw_is_meta_keyword'] = $settings[0]['is_meta_keyword'];
                $variables['wsw_meta_keyword_type'] = $settings[0]['meta_keyword_type'];
                $variables['wsw_is_meta_title'] = $settings[0]['is_meta_title'];
                $variables['wsw_meta_title'] = $settings[0]['meta_title'];
                $variables['wsw_is_meta_description'] = $settings[0]['is_meta_description'];
                $variables['wsw_is_meta_robot_noindex'] = $settings[0]['is_meta_robot_noindex'];
                $variables['wsw_is_meta_robot_nofollow'] = $settings[0]['is_meta_robot_nofollow'];
                $variables['wsw_meta_description'] = $settings[0]['meta_description'];
                $variables['wsw_is_over_sentences'] = $settings[0]['is_over_sentences'];
                $variables['wsw_first_over_sentences'] = $settings[0]['first_over_sentences'];
                $variables['wsw_last_over_sentences'] = $settings[0]['last_over_sentences'];

                /** Rich Snippets Settings */
                $variables['wsw_is_rich_snippets'] = $settings[0]['is_rich_snippets'];
                $variables['wsw_show_rich_snippets'] = $settings[0]['show_rich_snippets'];

                $variables['wsw_rating_value'] = $settings[0]['rating_value'];
                $variables['wsw_review_author'] = $settings[0]['review_author'];
                $variables['wsw_review_summary'] = $settings[0]['review_summary'];
                $variables['wsw_review_description'] = $settings[0]['review_description'];

                $variables['wsw_event_name'] = $settings[0]['event_name'];
                $variables['wsw_event_date'] = $settings[0]['event_date'];
                $variables['wsw_event_url'] = $settings[0]['event_url'];
                $variables['wsw_event_location_name'] = $settings[0]['event_location_name'];
                $variables['wsw_event_location_street'] = $settings[0]['event_location_street'];
                $variables['wsw_event_location_locality'] = $settings[0]['event_location_locality'];
                $variables['wsw_event_location_region'] = $settings[0]['event_location_region'];

                $variables['wsw_people_fname'] = $settings[0]['people_fname'];
                $variables['wsw_people_lname'] = $settings[0]['people_lname'];
                $variables['wsw_people_locality'] = $settings[0]['people_locality'];
                $variables['wsw_people_region'] = $settings[0]['people_region'];
                $variables['wsw_people_title'] = $settings[0]['people_title'];
                $variables['wsw_people_homeurl'] = $settings[0]['people_homeurl'];
                $variables['wsw_people_photourl'] = $settings[0]['people_photourl'];

                $variables['wsw_product_name'] = $settings[0]['product_name'];
                $variables['wsw_product_imageurl'] = $settings[0]['product_imageurl'];
                $variables['wsw_product_description'] = $settings[0]['product_description'];
                $variables['wsw_product_offers'] = $settings[0]['product_offers'];

                $variables['wsw_is_social_facebook'] = $settings[0]['is_social_facebook'];
                $variables['wsw_social_facebook_publisher'] = $settings[0]['social_facebook_publisher'];
                $variables['wsw_social_facebook_author'] = $settings[0]['social_facebook_author'];
                $variables['wsw_social_facebook_title'] = $settings[0]['social_facebook_title'];
                $variables['wsw_social_facebook_description'] = $settings[0]['social_facebook_description'];

                $variables['wsw_is_social_twitter'] = $settings[0]['is_social_twitter'];
                $variables['wsw_social_twitter_title'] = $settings[0]['social_twitter_title'];
                $variables['wsw_social_twitter_description'] = $settings[0]['social_twitter_description'];

                self::enqueue_styles();

            }

            echo self::render_template( 'global-settings/page-metabox-below.php', $variables );
        }
        function show_metabox() {
            global $post;
            $post_id = $post->ID;

            $variables = array();
            $variables['wsw_post_id'] = $post->ID;
            echo self::render_template( 'global-settings/page-metabox.php', $variables );
        }

        /** Ajax module for analysis */
        static public function ajax_calc_post_score(){
            $variables = array();
            $post_id = $_POST['post_id'];
            $post    = get_post( $post_id );
            $post_content = $post->post_content;


            echo WSW_Calc::calc_post_score($post_content);

            die();
        }
        static public function ajax_calc_post_density(){
            $variables = array();
            $post_id = $_POST['post_id'];
            $post    = get_post( $post_id );
            $post_content = $post->post_content;

            $settings = get_post_meta( $post_id , 'wsw-settings');

            $keyword = $settings[0]['keyword_value'];
            if($keyword!='' && $post_content!='' )     echo WSW_Calc::calc_post_density($post_content, $keyword);
            else echo '0.00';
            die();
        }
        static public function ajax_get_keyword_suggestion(){
            $variables = array();
            $post_id = $_POST['post_id'];
            $post    = get_post( $post_id );
            $post_content = $post->post_content;

            $settings = get_post_meta( $post->ID , 'wsw-settings');


            $suggestions = array();

            $global_settings = WSW_Main::$settings;

            $isHeading = WSW_Calc::get_keyword_decoration_bold($post_content, $settings[0]['keyword_value']);


            if($isHeading == '1'){
                $suggestions[] =array (
                    'msg' => 'You have keyword in bold.',
                    'state' => 'yes'
                );
            }
            else{
                $suggestions[] =array (
                    'msg' => 'You do not have keyword(s) in bold.',
                    'state' => 'no'
                );
            }
            $isHeading = WSW_Calc::get_keyword_decoration_italic($post_content, $settings[0]['keyword_value']);
            if($isHeading == '1'){
                $suggestions[] =array (
                    'msg' => 'You have keyword in italic.',
                    'state' => 'yes'
                );
            }
            else{
                $suggestions[] =array (
                    'msg' => 'You do not have keyword(s) in italic.',
                    'state' => 'no'
                );
            }

            $isHeading = WSW_Calc::get_keyword_decoration_underline($post_content, $settings[0]['keyword_value']);
            if($isHeading == '1'){
                $suggestions[] =array (
                    'msg' => 'You have keyword in underline.',
                    'state' => 'yes'
                );
            }
            else{
                $suggestions[] =array (
                    'msg' => 'You do not have keyword(s) in underline.',
                    'state' => 'no'
                );
            }

            $isHeading = WSW_Calc::get_headings_h1($post_content, $settings[0]['keyword_value']);

            if($isHeading == '1'){
                $suggestions[] =array (
                    'msg' => 'You have keyword in H1 tag.',
                    'state' => 'yes'
                );
            }
            else{
                $suggestions[] =array (
                    'msg' => 'You do not have keyword in H1 tag.',
                    'state' => 'no'
                );
            }

            $isHeading = WSW_Calc::get_headings_h2($post_content, $settings[0]['keyword_value']);
            if($isHeading == '1'){
                $suggestions[] =array (
                    'msg' => 'You have keyword in H2 tag.',
                    'state' => 'yes'
                );
            }
            else{
                $suggestions[] =array (
                    'msg' => 'You do not have keyword in H2 tag.',
                    'state' => 'no'
                );
            }

            $isHeading = WSW_Calc::get_headings_h3($post_content, $settings[0]['keyword_value']);
            if($isHeading == '1'){
                $suggestions[] =array (
                    'msg' => 'You have keyword in H3 tag.',
                    'state' => 'yes'
                );
            }
            else{
                $suggestions[] =array (
                    'msg' => 'You do not have keyword in H3 tag.',
                    'state' => 'no'
                );
            }



            $variables['wsw_suggestion_keyword'] = $suggestions;

            $variables['wsw_icon_success'] = dirname(WSW_Main::$plugin_url) . '/img/icons/sprites.png';

            echo self::render_template( 'global-settings/page-suggestion-keyword.php', $variables );
            die();
        }
        static public function ajax_get_url_suggestion(){
            $variables = array();
            $post_id = $_POST['post_id'];
            $post    = get_post( $post_id );
            $post_content = $post->post_content;


            $suggestions = array();
            $suggestions[] =array (
                'msg' => 'Keyword in anchor text of an internal link.',
                'state' => 'yes'
            );

            $suggestions[] =array (
                'msg' => 'keyword not found in anchor text of an external link.',
                'state' => 'no'
            );

            $variables['wsw_suggestion_url'] = $suggestions;
            $variables['wsw_icon_success'] = dirname(WSW_Main::$plugin_url) . '/img/icons/sprites.png';
            echo self::render_template( 'global-settings/page-suggestion-url.php', $variables );
            die();
        }
        static public function ajax_get_content_suggestion(){
            $variables = array();
            $post_id = $_POST['post_id'];
            $post    = get_post( $post_id );
            $post_content = $post->post_content;



            $suggestions = array();
            $suggestions[] =array (
                'msg' => 'Increase the length of the content.',
                'state' => 'no'
            );

            $suggestions[] =array (
                'msg' => 'Keyword is found in META Keyword.',
                'state' => 'yes'
            );

            $variables['wsw_suggestion_content'] = $suggestions;

            $variables['wsw_icon_success'] = dirname(WSW_Main::$plugin_url) . '/img/icons/sprites.png';
            echo self::render_template( 'global-settings/page-suggestion-content.php', $variables );
            die();
        }
        static public function ajax_get_youtube(){
            $variables = array();
            $post_id = $_POST['post_id'];

            $all_videos_per_key = array();
            $settings = get_post_meta( $post_id , 'wsw-settings');
            $keyword = $settings[0]['keyword_value'];

            $youtubeKey1 = new Youtube_Interface ( $keyword );

            $all_videos_per_key[$keyword] = $youtubeKey1->getVideos ( 8 );
            /*@var $youtubeVideo WPPostsRateKeys_YoutubeVideo */
            foreach ($all_videos_per_key as $post_key => $youtubeVideos) {

                foreach ($youtubeVideos as $youtubeVideo) {
                    $data_item = array();
                    /*@var $videoEntry Zend_Gdata_YouTube_VideoEntry */
                    $videoEntry = $youtubeVideo->videoEntry;

                    $tmp_video_entry = array();
                    $tmp_video_entry['url'] = $videoEntry->getVideoWatchPageUrl();
                    $tmp_video_entry['thumbnail'] = $youtubeVideo->getThumbnail();
                    $seconds = $videoEntry->getVideoDuration();
                    $tmp_video_entry['duration'] = gmdate("H:i:s", $seconds);
                    // Remove the hours as 00:
                    if (strpos($tmp_video_entry['duration'], '00:')===0) {
                        $tmp_video_entry['duration'] = substr_replace($tmp_video_entry['duration'], '', 0,3);
                    }
                    /*@var $author Zend_Gdata_App_Extension_Author */
                    $author = $videoEntry->getAuthor();
                    /*@var $author Zend_Gdata_App_Extension_Name */
                    $author = $author[0]->getName();
                    $tmp_video_entry['author'] = $author->getText();
                    $tmp_video_entry['title'] = $videoEntry->getVideoTitle();
                    $tmp_video_entry['id'] = $youtubeVideo->id;
                    $tmp_video_entry['views'] = number_format($videoEntry->getVideoViewCount(),0);
                    //$youtubeVideo->getCodeToDisplayVideo ( 425, 350 );

                    $data_to_return['videos']['list'][$post_key]['list'][] = $tmp_video_entry;
                }
            }

            $variables['wsw_youtube_keyword'] = $keyword;
            $variables['wsw_youtube_list'] = $data_to_return;
            //  echo self::render_template( 'templates/page-youtube.php', $variables );

            $json = json_encode($data_to_return); echo $json;

            die();
        }
        static public function ajax_get_lsi(){
            $variables = array();
            $post_id = $_POST['post_id'];
            $post    = get_post( $post_id );

            $settings = get_post_meta( $post_id , 'wsw-settings');
            $keyword = $settings[0]['keyword_value'];

            $tmp_arr = WSW_LSI::get_lsi_by_keyword($keyword);
            if(count($tmp_arr)){
                $variables['wsw_lsi_list'] = $tmp_arr;
                echo self::render_template( 'templates/page-lsi.php', $variables );
            }
            else {
                echo '';
            }

            die();
        }
        static public function ajax_set_support_link(){
            $options = WSW_Main::$settings;
            $options['chk_author_linking'] = '1';

            WSW_Settings::update_options($options);


            die();
        }
        static public function ajax_set_support_time(){
            $options = WSW_Main::$settings;
            $options['wsw_initial_dt'] = time();
            WSW_Settings::update_options($options);

            die();
        }

    } // end WSW_Dashboard
}
