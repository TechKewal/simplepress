<?php
/*
  Simple:Press
  ADS plugin routines
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * class SP_Ads_Showing_Ad
 */
class SP_Ads_Showing_Ad {

    /**
     * Array of ads ids
     * 
     * @since 1.0
     * 
     * @access private
     * @var array
     */
    private static $_adsIds = array();

    /**
     * Array of location hooks
     * 
     * @since 1.0
     * 
     * @access private
     * @var array
     */
    private static $_locationHooks = array();

    /**
     * Ordinal number of forum
     * 
     * @since 1.0
     * 
     * @access private
     * @var int
     */
    private static $_ordinalNumOfForum = 0;

    /**
     * Ordinal number of topic
     * 
     * @since 1.0
     * 
     * @access private
     * @var int
     */
    private static $_ordinalNumOfTopic = 0;

    /**
     * Ordinal number of post
     * 
     * @since 1.0
     * 
     * @access private
     * @var int
     */
    private static $_ordinalNumOfPost = 0;

    /**
     * Ads logic hooks
     * 
     * @since 1.0
     * 
     * @access public
     */
    public static function init() {
        $pageData = SP()->rewrites->pageData;
        if (empty($pageData['forumid'])) {
            add_action('sph_BeforeSectionStart_eachForum', function ($a) {
                ++self::$_ordinalNumOfForum;
            });
        } elseif (empty($pageData['topicid'])) {
            add_action('sph_BeforeSectionStart_eachTopic', function ($a) {
                ++self::$_ordinalNumOfTopic;
            });
        } elseif (empty($pageData['postid'])) {
            add_action('sph_BeforeSectionStart_eachPost', function ($a) {
                ++self::$_ordinalNumOfPost;
            });
        }
        // 
        foreach (self::getLocationsHooks() as $hook => $_) {
            add_action($hook, function() use($hook) {
                $args = func_get_args();
                array_unshift($args, $hook);
                call_user_func_array(array('self', 'exec'), $args);
            });
        }
    }

    /**
     * Show ads on hook
     * 
     * @since 1.0
     * 
     * @access public
     * @param string $hook
     */
    public static function exec($hook) {
        $hook = self::_fixHookNameOrdinal($hook);
        if (!$ads = SP_Ads_Database::getAdsOnHook($hook, self::$_adsIds)) {
            return;
        }
        foreach ($ads as $ad) {
            SP_Ads_Database::addAdHit($ad->ad_id);
            array_push(self::$_adsIds, $ad->ad_id);
            ?>
            <div class="sp-forum-ad-wrap">
                <div class="sp-forum-ad"<?php if ($ad->size): ?> style="<?php echo $ad->size ?>"<?php endif ?>>
                    <?php echo $ad->content ?>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Get hooks locations of ads
     * 
     * @since 1.0
     * 
     * @access public
     * @return array
     */
    public static function getLocationsHooks() {
        if (!self::$_locationHooks) {
            self::$_locationHooks = (array) include SPPLUGINDIR . '/ads/sp-ads-location-hooks.php';
        }
        return self::$_locationHooks;
    }

    /**
     * Check, is the hook is dynamic
     * 
     * @since 1.0
     * 
     * @access public
     * @param string $hook
     * @return boolean
     */
    public static function isDynamicHook($hook) {
        return preg_match('/_each[A-Z][a-z]+$/', $hook);
    }

    /**
     * Build dynamic hook name
     * 
     * @since 1.0
     * 
     * @access public
     * @param string $hook
     * @param int $num
     * @return string
     */
    public static function buildDynamicHookName($hook, $num) {
        if (self::isDynamicHook($hook) && $num) {
            $hook .= '_' . (int) $num;
        }
        return $hook;
    }

    /**
     * Get hook name with ordinal number
     * 
     * Adds sequence numbers to hook names if needed
     * 
     * @since 1.0
     * 
     * @access private
     * @param string $hook hook name
     * @return string hook name
     */
    private static function _fixHookNameOrdinal($hook) {
        $pageData = SP()->rewrites->pageData;
        if (empty($pageData['forumid'])) {
            if (preg_match('/_eachForum$/', $hook)) {
                $hook .= '_' . self::$_ordinalNumOfForum;
            }
        } elseif (empty($pageData['topicid'])) {
            if (preg_match('/_eachTopic$/', $hook)) {
                $hook .= '_' . self::$_ordinalNumOfTopic;
            }
        } elseif (empty($pageData['postid'])) {
            if (preg_match('/_eachPost$/', $hook)) {
                $hook .= '_' . self::$_ordinalNumOfPost;
            }
        }
        return $hook;
    }

}
