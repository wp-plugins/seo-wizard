<?php

if ( ! class_exists( 'WSW_Show' ) ) {

    /**
     * generate SEO info;
     */
    class WSW_Show extends WP_Module {
        /**
         * Constructor
         */
        protected function __construct() {

            $this->register_hook_callbacks();
        }

        /**
         * Register callbacks for actions and filters
         */
        public function register_hook_callbacks() {

            add_action('wp_head', __CLASS__. '::fake_wp_head' );
            add_action('wp_footer', __CLASS__. '::fake_wp_footer' );
            add_action('login_head', __CLASS__. '::fake_login_head' );
            add_action('admin_head', __CLASS__. '::fake_admin_head' );

            // Add filter for POST content
            add_filter('the_content', __CLASS__. '::filter_post_content', 1, 2);
            // Add filter for POST title
            add_filter('the_title', __CLASS__. '::filter_post_title', 1, 2);
            // Filter the Posts Slugs
            add_filter('name_save_pre', __CLASS__. '::filter_post_slug',10);

        }

        function fake_login_head() {
            if (WSW_Main::$settings['chk_block_login_page'] == '1') {
                echo "\n<!-- WordPress SEO Wizard -->\n";
                echo "<link rel=\"canonical\" href=\"".get_option('home')."\" />\n";
                echo "<!-- WordPress SEO Wizard -->\n";
            }
        }
        function fake_admin_head() {
            if (WSW_Main::$settings['chk_block_admin_page'] == '1') {
                echo "\n<!-- WordPress SEO Wizard -->\n";
                echo "<link rel=\"canonical\" href=\"".get_option('home')."\" />\n";
                echo "<!-- WordPress SEO Wizard -->\n";
            }
        }
        function filter_post_slug($slug) {
            // If settings is enable, filter the Slug
            $settings = WSW_Main::$settings;

            if ($settings['chk_convertion_post_slug']=='1') {
                // We don't want to change an existing slug
                if ($slug) return $slug;

                $seo_slug = strtolower(stripslashes($_POST['post_title']));

                $seo_slug = preg_replace('/&.+?;/', '', $seo_slug); // kill HTML entities
                // kill anything that is not a letter, digit, space or apostrophe
                $seo_slug = preg_replace ("/[^a-zA-Z0-9 \']/", "", $seo_slug);

                // Turn it to an array and strip common words by comparing against c.w. array
                $seo_slug_array = array_diff (explode(" ", $seo_slug), WSW_Settings::get_slugs_stop_words());

                // Turn the sanitized array into a string
                $seo_slug = implode("-", $seo_slug_array);

                return $seo_slug;
            }

            return $slug;
        }

        /**
         * hook wp_head
         */
        public function fake_wp_head() {
            // Only to add the head in Single page where Post is shown
            if (is_single() || is_page()) {
                $post_id = get_the_ID();

                $settings = get_post_meta( $post_id , 'wsw-settings');

                if($settings[0]['is_meta_keyword']){

                    $meta_keyword_type = $settings[0]['meta_keyword_type'];
                    if (trim($meta_keyword_type)!='') {

                        if($meta_keyword_type == 'keywords'){
                            $meta_value = $settings[0]['keyword_value'];
                        }
                        elseif ($meta_keyword_type=='categories') {
                            $categories_arr = wp_get_post_categories($post_id,array('fields'=>'names'));

                            if (count($categories_arr)>0) {
                                $meta_value = implode(',', $categories_arr);
                            }
                        }
                        elseif ($meta_keyword_type=='tags') {
                            $tags_arr = wp_get_post_tags($post_id,array('fields'=>'names'));

                            if (count($tags_arr)>0) {
                                $meta_value = implode(',', $tags_arr);
                            }
                        }
                        if($meta_value!='')   echo '<meta name="keywords" content="' . $meta_value . '" />'. "\r\n";;
                    }
                }

                if($settings[0]['is_meta_title']){
                    $meta_title_metadata = $settings[0]['meta_title'];
                    if (trim($meta_title_metadata)!='') {
                        echo '<meta name="title" content="' . $meta_title_metadata . '" />'. "\r\n";;
                    }
                }

                if($settings[0]['is_meta_description']){
                    $meta_description_metadata = $settings[0]['meta_description'];
                    if (trim($meta_description_metadata)!='') {
                        echo '<meta name="description" content="' . $meta_description_metadata . '" />'. "\r\n";;
                    }
                }

                /*
                 * Add Facebook meta data
                 */
                if (WSW_Main::$settings['chk_use_facebook'] == '1' && $settings[0]['is_social_facebook']=='1') {
                    echo '<meta property="og:type"   content="article" />' . "\r\n";
                    echo '<meta property="og:title"  content="' . esc_attr( $settings[0]['social_facebook_publisher'] ) . '" />' . "\r\n";
                    echo '<meta property="article:author"  content="' . esc_attr( $settings[0]['social_facebook_author'] ) . '" />' . "\r\n";
                    echo '<meta property="article:publisher"  content="' . esc_attr( $settings[0]['social_facebook_publisher'] ) . '" />' . "\r\n";
                    echo '<meta property="og:description"  content="' . esc_attr( $settings[0]['social_facebook_description'] ) . '" /> ' . "\r\n";
                 }
                /*
                 * Add Twitter meta data
                 */
                if (WSW_Main::$settings['chk_use_twitter'] == '1' && $settings[0]['is_social_twitter']=='1') {
                    echo '<meta name="twitter:card" content="summary">' . "\n";
                    echo '<meta name="twitter:title" content="' . esc_attr( $settings[0]['social_twitter_title'] ) . '">' . "\n";
                    echo '<meta name="twitter:description" content="' . esc_attr( $settings[0]['social_twitter_description'] ) . '">' . "\n";
                }

            }
        }

        /**
         * hook wp_footer
         */
        public function fake_wp_footer() {
            // Only to add the head in Single page where Post is shown
            if (is_single() || is_page()) {
                $post_id = get_the_ID();

                //$settings = get_post_meta( $post_id , 'wsw-settings');

                if (WSW_Main::$settings['chk_author_linking'] == '1') {
                    echo '<div style="text-align: center;"><a href="http://seo.uk.net/seo-wizard/">Seo Wizard</a> powered by <a href="http://seo.uk.net"><img src="http://seo.uk.net/wp-content/uploads/2013/11/seo.uk_.net_.gif" width="70" height="20" alt="The official Seo Company of London" /></a></div>' . "\n";
                }
            }
        }

        /**
         *
         * Filter the POST content
         *
         */
        function filter_post_content($content,$post_id='') {
            // Only filter if is Single Page
            if (!((is_single()  || is_page() ) && !is_feed())) {
                return $content;
            }

            if ($post_id=='') {
                global $post;
                $post_id = $post->ID;
            }

            if (!isset($post)) {
                $post = get_post($post_id);
            }

            $filtered_content = $content;

            $settings = get_post_meta( $post_id , 'wsw-settings');

            if(WSW_Main::$settings['chk_use_richsnippets'] == '1' && $settings[0]['is_rich_snippets'] == '1'){
                if($settings[0]['rating_value']!=0){
                    $variables = array();
                    $variables['seo_post_title'] = $post->post_title;
                    $variables['seo_rating_value'] = $settings[0]['rating_value'];
                    $variables['seo_review_author'] = $settings[0]['review_author'];
                    $variables['seo_review_summary'] = $settings[0]['review_summary'];
                    $variables['seo_review_description'] = $settings[0]['review_description'];
                    $variables['seo_show_rich_snippets'] = $settings[0]['show_rich_snippets'];

                    $attach_content = self::render_template( 'templates/page-rating.php', $variables);
                    $filtered_content = $filtered_content . $attach_content ;
                }

                if($settings[0]['event_name']!=''){

                    $variables = array();

                    $variables['seo_event_name'] = $settings[0]['event_name'];
                    $variables['seo_event_url'] = $settings[0]['event_url'];
                    $variables['seo_event_date'] = $settings[0]['event_date'];
                    $variables['seo_event_location'] = $settings[0]['event_location_name'];
                    $variables['seo_event_street'] = $settings[0]['event_location_street'];
                    $variables['seo_event_locality'] = $settings[0]['event_location_locality'];
                    $variables['seo_event_region'] = $settings[0]['event_location_region'];
                    $variables['seo_show_rich_snippets'] = $settings[0]['show_rich_snippets'];

                    $attach_content = self::render_template( 'templates/page-event.php', $variables );
                    $filtered_content = $filtered_content . $attach_content ;
                }

                if($settings[0]['people_fname']!=''){
                    $variables = array();

                    $variables['seo_people_name'] = $settings[0]['people_fname'] . ' ' . $settings[0]['people_lname'];
                    $variables['seo_people_locality'] = $settings[0]['people_locality'];
                    $variables['seo_people_region'] = $settings[0]['people_region'];
                    $variables['seo_show_rich_snippets'] = $settings[0]['show_rich_snippets'];
                    $attach_content = self::render_template( 'templates/page-people.php' , $variables);
                    $filtered_content = $filtered_content . $attach_content ;
                }

                if($settings[0]['product_name']!=''){

                    $variables = array();

                    $variables['seo_product_name'] = $settings[0]['product_name'] ;
                    $variables['seo_product_description'] = $settings[0]['product_description'];
                    $variables['seo_product_price'] = $settings[0]['product_offers'];
                    $variables['seo_show_rich_snippets'] = $settings[0]['show_rich_snippets'];
                    $attach_content = self::render_template( 'templates/page-product.php' , $variables);
                    $filtered_content = $filtered_content . $attach_content ;
                }
            }

            // Apply settings related to keyword, if keyword is specified
            if ($settings[0]['keyword_value'] != '') {
                $filtered_content = self::apply_biu_to_content($filtered_content, $settings[0]['keyword_value']);
            }

            // Decorates all Images Alt and Title attributes
            $filtered_content = WSW_HtmlStyles::decorates_images($filtered_content, WSW_Main::$settings);

            // Add of rel="nofollow" to external links
            $filtered_content = WSW_HtmlStyles::add_rel_nofollow_external_links($filtered_content, WSW_Main::$settings);

            // Add of rel="nofollow" to Image links
            $filtered_content = WSW_HtmlStyles::add_rel_nofollow_image_links($filtered_content, WSW_Main::$settings);

            if (WSW_Main::$settings['chk_tagging_using_google'] == '1') {

                // Check for Google Searchs and Tags
                if(trim(WSW_Main::$settings['txt_generic_tags']) != '')
                {
                    $tags_to_keep = array();
                    wp_set_post_tags( $post_id, $tags_to_keep, false );
                    wp_set_post_tags( $post_id, trim(WSW_Main::$settings['txt_generic_tags']), true );
                }
            }

            return $filtered_content;

        }


        static function apply_biu_to_content($content, $keyword) {
            $settings = WSW_Main::$settings;

            $new_content = $content;

                if ($settings['chk_keyword_decorate_bold']===1 || $settings['chk_keyword_decorate_bold']==='1')
                    $already_apply_bold = FALSE;
                else
                    $already_apply_bold = TRUE;

                if ($settings['chk_keyword_decorate_italic']===1 || $settings['chk_keyword_decorate_italic']==='1')
                    $already_apply_italic = FALSE;
                else
                    $already_apply_italic = TRUE;

                if ($settings['chk_keyword_decorate_underline']===1 || $settings['chk_keyword_decorate_underline']==='1')
                    $already_apply_underline = FALSE;
                else
                    $already_apply_underline = TRUE;

                // Pass through all keyword until ends or until are applied all designs
                $how_many_keys = WSW_Keywords::how_many_keywords(array($keyword), $new_content);



                // To avoid make the request for each keyword: Get pieces by keyword for determine if some has the design applied
                $pieces_by_keyword = WSW_Keywords::get_pieces_by_keyword(array($keyword), $new_content,TRUE);
                $pieces_by_keyword_matches = $pieces_by_keyword[1];
                $pieces_by_keyword = $pieces_by_keyword[0];

                // First, only check for designs already applied
                for ($i=1;$i<=$how_many_keys;$i++) {

                    // Stop if are already all the design applied
                    if ($already_apply_bold && $already_apply_italic && $already_apply_underline)
                        break;


                    // Getting the position
                    $key_pos = WSW_Keywords::strpos_offset($keyword,$new_content,$i,$pieces_by_keyword,$pieces_by_keyword_matches);

                    if ($key_pos!==FALSE) {
                        $already_style = WSW_HtmlStyles::if_some_style_or_in_tag_attribute($new_content,array($keyword),$i);


                        if ($already_style) {
                            if ($already_style[1] == 'bold')
                                $already_apply_bold = TRUE;
                            elseif ($already_style[1] == 'italic')
                                $already_apply_italic = TRUE;
                            elseif ($already_style[1] == 'underline')
                                $already_apply_underline = TRUE;
                        }
                    }
                }

                // Apply designs pendings to apply
                for ($i=1;$i<=$how_many_keys;$i++) {

                    // Stop if are already all the design applied
                    if ($already_apply_bold && $already_apply_italic && $already_apply_underline)
                        break;

                    // Getting the position. Here can't be calculate one time ($pieces_by_keyword) and rehuse it because the content changes
                    $key_pos = WSW_Keywords::strpos_offset($keyword,$new_content,$i);
                    $pieces_by_keyword_matches_item = $pieces_by_keyword_matches[$i-1];


                    if ($key_pos!==FALSE) {
                        $already_style = WSW_HtmlStyles::if_some_style_or_in_tag_attribute($new_content,array($keyword),$i);

                        if ($already_style) {
                            if ($already_style[1] == 'bold')
                                $already_apply_bold = TRUE;
                            elseif ($already_style[1] == 'italic')
                                $already_apply_italic = TRUE;
                            elseif ($already_style[1] == 'underline')
                                $already_apply_underline = TRUE;
                        }
                        else {

                            if (!$already_apply_bold) {
                                $keyword_with_style = WSW_HtmlStyles::apply_bold_styles($pieces_by_keyword_matches_item);
                                $already_apply_bold = TRUE;
                            }
                            elseif (!$already_apply_italic) {
                                $keyword_with_style = WSW_HtmlStyles::apply_italic_styles($pieces_by_keyword_matches_item);
                                $already_apply_italic = TRUE;
                            }
                            elseif (!$already_apply_underline) {
                                $keyword_with_style = WSW_HtmlStyles::apply_underline_styles($pieces_by_keyword_matches_item);
                                $already_apply_underline = TRUE;
                            }

                           {
                                $new_content = substr_replace($new_content,$keyword_with_style
                                    ,$key_pos,strlen($pieces_by_keyword_matches_item));
                            }

                            // Calculate how many keyword, because in case the keyword is, for example "b" this value will change
                            $how_many_keys = WSW_Keywords::how_many_keywords(array($keyword), $new_content);
                        }
                    }
                }


            return $new_content;
        }

        function filter_post_title($title,$post_id='') {

            if ($post_id=='') {
                global $post;
                $post_id = $post->ID;
            }

            if (!isset($post)) {
                $post = get_post($post_id);
            }

            // Check if the filter must be applied
            if (WSW_Main::$settings['chk_keyword_to_titles'] != '1')
                return $title;

            if ($title=='')
                return 'no title';

            if ($post->post_status=='auto-draft' || $post->post_status=='trash'
                || $post->post_status=='inherit'
            ) {
                return $title;
            }

            $settings = get_post_meta( $post_id , 'wsw-settings');


            if(trim($settings[0]['keyword_value']) == '') return $title;

            $filtered_title = $title . ' | ' . $settings[0]['keyword_value'];


            // Changed for Headway Theme
            if (! isset ( $filtered_title ) || trim ( $filtered_title ) == '') {
                return $title;
            } else {
                return $filtered_title;
            }
        }


    } // end WSW_Show
}
