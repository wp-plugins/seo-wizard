<?php

if ( ! class_exists( 'WSW_Settings' ) ) {

    /**
     *
     */
    class WSW_Settings{

        /**
         * The name for plugin options
         *
         * @var string
         */
        static $global_option = 'SEOWizard_Options';

        /**
         * Updates the General Settings of Plugin
         *
         * @return void
         * @access public
         */
        static function update_options($options) {
            // Save Class variable
            WSW_Main::$settings = $options;

            return update_option(self::$global_option, $options);
        }

        /**
         * Return the Stop Words for Slugs
         *
         * This values isn't in Settings general plugin value
         * instead is saved in a different options value
         *
         * @return array	with all the Stop Words
         * @access public
         */
        static function get_slugs_stop_words() {
            // default values
            $option_default = self::get_default_filter_post_slugs_stop_words();
            return $option_default;
        }

        /**
         * Default list for Stop Words for the filter of Post Slug
         *
         * @return 	string
         */
        static function get_default_filter_post_slugs_stop_words() {
            $arr = array ("a", "able", "about", "above", "abroad", "according", "accordingly", "across"
            , "actually", "adj", "after", "afterwards", "again", "against", "ago", "ahead", "ain't", "all"
            , "allow", "allows", "almost", "alone", "along", "alongside", "already", "also", "although", "always"
            , "am", "amid", "amidst", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow"
            , "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate"
            , "appropriate", "are", "aren't", "around", "as", "a's", "aside", "ask", "asking", "associated"
            , "at", "available", "away", "awfully", "b", "back", "backward", "backwards", "be", "became"
            , "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "behind"
            , "being", "believe", "below", "beside", "besides", "best", "better", "between", "beyond", "both"
            , "brief", "but", "by", "c", "came", "can", "cannot", "cant", "can't", "caption", "cause", "causes"
            , "certain", "certainly", "changes", "clearly", "c'mon", "co", "co.", "com", "come", "comes"
            , "concerning", "consequently", "consider", "considering", "contain", "containing", "contains"
            , "corresponding", "could", "couldn't", "course", "c's", "currently", "d", "dare", "daren't"
            , "definitely", "described", "despite", "did", "didn't", "different", "directly", "do", "does"
            , "doesn't", "doing", "done", "don't", "down", "downwards", "during", "e", "each", "edu", "eg"
            , "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "entirely"
            , "especially", "et", "etc", "even", "ever", "evermore", "every", "everybody", "everyone"
            , "everything", "everywhere", "ex", "exactly", "example", "except", "f", "fairly", "far", "farther"
            , "few", "fewer", "fifth", "first", "five", "followed", "following", "follows", "for", "forever"
            , "former", "formerly", "forth", "forward", "found", "four", "from", "further", "furthermore", "g"
            , "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten"
            , "greetings", "h", "had", "hadn't", "half", "happens", "hardly", "has", "hasn't", "have", "haven't"
            , "having", "he", "he'd", "he'll", "hello", "help", "hence", "her", "here", "hereafter", "hereby"
            , "herein", "here's", "hereupon", "hers", "herself", "he's", "hi", "him", "himself", "his", "hither"
            , "hopefully", "how", "howbeit", "however", "hundred", "i", "i'd", "ie", "if", "ignored", "i'll"
            , "i'm", "immediate", "in", "inasmuch", "inc", "inc.", "indeed", "indicate", "indicated", "indicates"
            , "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn't", "it", "it'd"
            , "it'll", "its", "it's", "itself", "i've", "j", "just", "k", "keep", "keeps", "kept", "know"
            , "known", "knows", "l", "last", "lately", "later", "latter", "latterly", "least", "less"
            , "lest", "let", "let's", "like", "liked", "likely", "likewise", "little", "look", "looking"
            , "looks", "low", "lower", "ltd", "m", "made", "mainly", "make", "makes", "many", "may"
            , "maybe", "mayn't", "me", "mean", "meantime", "meanwhile", "merely", "might", "mightn't"
            , "mine", "minus", "miss", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "must"
            , "mustn't", "my", "myself", "n", "name", "namely", "nd", "near", "nearly", "necessary"
            , "need", "needn't", "needs", "neither", "never", "neverf", "neverless", "nevertheless"
            , "new", "next", "nine", "ninety", "no", "nobody", "non", "none", "nonetheless", "noone"
            , "no-one", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now", "nowhere"
            , "o", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one"
            , "ones", "one's", "only", "onto", "opposite", "or", "other", "others", "otherwise", "ought"
            , "oughtn't", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "p"
            , "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus"
            , "possible", "presumably", "probably", "provided", "provides", "q", "que", "quite", "qv"
            , "r", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding"
            , "regardless", "regards", "relatively", "respectively", "right", "round", "s", "said"
            , "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem"
            , "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious"
            , "seriously", "seven", "several", "shall", "shan't", "she", "she'd", "she'll", "she's"
            , "should", "shouldn't", "since", "six", "so", "some", "somebody", "someday", "somehow"
            , "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry"
            , "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "t", "take"
            , "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that"
            , "that'll", "thats", "that's", "that've", "the", "their", "theirs", "them", "themselves",
                "then", "thence", "there", "thereafter", "thereby", "there'd", "therefore", "therein"
            , "there'll", "there're", "theres", "there's", "thereupon", "there've", "these", "they"
            , "they'd", "they'll", "they're", "they've", "thing", "things", "think", "third", "thirty"
            , "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout"
            , "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried"
            , "tries", "truly", "try", "trying", "t's", "twice", "two", "u", "un", "under", "underneath"
            , "undoing", "unfortunately", "unless", "unlike", "unlikely", "until", "unto", "up", "upon"
            , "upwards", "us", "use", "used", "useful", "username"
            , "uses", "using", "usually", "v", "value"
            , "various", "versus", "very", "via", "viz", "vs", "w", "want", "wants", "was", "wasn't"
            , "way", "we", "we'd", "welcome", "well", "we'll", "went", "were", "we're", "weren't"
            , "we've", "what", "whatever", "what'll", "what's", "what've", "when", "whence", "whenever"
            , "where", "whereafter", "whereas", "whereby", "wherein", "where's", "whereupon", "wherever"
            , "whether", "which", "whichever", "while", "whilst", "whither", "who", "who'd", "whoever"
            , "whole", "who'll", "whom", "whomever", "who's", "whose", "why", "will", "willing", "wish"
            , "with", "within", "without", "wonder", "won't", "would", "wouldn't", "x", "y", "yes"
            , "yet", "you", "you'd", "you'll", "your", "you're", "yours", "yourself", "yourselves"
            , "you've", "z", "zero");

            return $arr;
        }

        /**
         * Return the General Settings of Plugin, and set them to default values if they are empty
         *
         * @return array general options of plugin
         * @access public
         */
        static function get_options() {

            // If isn't empty, return class variable
            if (WSW_Main::$settings) {
                return WSW_Main::$settings;
            }

            // default values
            $options = array
            (
                'chk_keyword_to_titles' => 0,
                'chk_nofollow_in_external' => 0,
                'chk_nofollow_in_image' => 0,
                'chk_use_facebook' => 0,
                'chk_use_twitter' => 0,
                'chk_use_dublin' => 0,
                'chk_use_richsnippets' => 0,

                'chk_keyword_decorate_bold' => 0,
                'chk_keyword_decorate_italic' => 0,
                'chk_keyword_decorate_underline' => 0,

                'opt_keyword_decorate_bold_type' => '0',
                'opt_keyword_decorate_italic_type' => '0',
                'opt_keyword_decorate_underline_type' => '0',
                'opt_image_alternate_type' => 'empty',
                'opt_image_title_type' => 'empty',

                'txt_image_alternate' => '',
                'txt_image_title' => '',
                'lsi_bing_api_key' => '',

                'chk_use_headings_h1' => 0,
                'chk_use_headings_h2' => 0,
                'chk_use_headings_h3' => 0,
                'chk_convertion_post_slug' => 0,
                'chk_tagging_using_google' => 0,
                'txt_generic_tags' => '',
                'chk_author_linking' => 0,
                'wsw_initial_dt' => '',
                'chk_block_login_page' => 0,
                'chk_block_admin_page' => 0
            );

            // get saved options
            $saved = get_option(self::$global_option);

            // assign them
            if (!empty($saved)) {
                foreach ($saved as $key => $option) {
                    $options[$key] = $option;
                }
            }

            // update the options if necessary
            if ($saved != $options) {
                update_option(self::$global_option, $options);
            }

            // Save class variable
            WSW_Main::$settings = $options;

            //return the options
            return $options;
        }

    } // end WSW_Settings
}
