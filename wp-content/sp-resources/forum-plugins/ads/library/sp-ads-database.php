<?php

/*
  Simple:Press
  ADS plugin database routines
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

/**
 * class SP_Ads_Database
 */
class SP_Ads_Database {

    /**
     * Tables names
     * 
     * @since 1.0
     * 
     * @const string Class constants
     */
    const
            AD_TABLE = SP_PREFIX . 'sf_ad',
            AD_HITS_TABLE = SP_PREFIX . 'sf_ad_hits',
            AD_SETS_TABLE = SP_PREFIX . 'sf_ad_sets',
            # table for belongs usergroups, forums, topics, posts
            AD_BELONGS_TABLE = SP_PREFIX . 'sf_ad_belongs',
            AD_KEYWORDS_TABLE = SP_PREFIX . 'sf_ad_keywords',
            AD_LOCATION_TABLE = SP_PREFIX . 'sf_ad_location',
            AD_DATE_RANGE_TABLE = SP_PREFIX . 'sf_ad_date_range';

    /**
     * Min length of word
     * 
     * @since 1.0
     * 
     * @const int Class constant
     */
    const MIN_LEN_WORD = 1;

    /**
     * Create tables when ads plugin activate
     * 
     * @since 1.0
     * 
     * @access public
     */
    public static function doActivate() {
        # need new tables for ads
        $charset = SP()->DB->charset();
        $tables[self::AD_SETS_TABLE] = '
                    CREATE TABLE IF NOT EXISTS ' . self::AD_SETS_TABLE . ' (
                        ad_set_id bigint(20) unsigned NOT NULL auto_increment,
                        name varchar(64) unique NOT NULL,
                        is_active tinyint(1) NOT NULL DEFAULT 1,
                        combine tinyint(1) NOT NULL DEFAULT 0,
                        PRIMARY KEY (ad_set_id)
                    ) ' . $charset . ';';
        $tables[self::AD_TABLE] = '
                    CREATE TABLE IF NOT EXISTS ' . self::AD_TABLE . ' (
                        ad_id bigint(20) unsigned NOT NULL auto_increment,
                        ad_set_id bigint(20) unsigned NOT NULL,
                        name varchar(64) NOT NULL,
                        content text NOT NULL,
                        size varchar(64) default NULL,
                        max_views bigint(20) unsigned default NULL,
                        is_active tinyint(1) NOT NULL DEFAULT 1,
                        script_allowed tinyint(1) NOT NULL DEFAULT 0,
                        PRIMARY KEY (ad_id),
                        unique ad_set_id__name (ad_set_id, name)
                    ) ' . $charset . ';';
        $tables[self::AD_HITS_TABLE] = '
                    CREATE TABLE IF NOT EXISTS ' . self::AD_HITS_TABLE . ' (
                        hit_id bigint(20) unsigned NOT NULL auto_increment,
                        ad_id bigint(20) unsigned NOT NULL,
                        dt datetime NOT NULL,
                        PRIMARY KEY (hit_id),
                        KEY ad_id_idx (ad_id)
                    ) ' . $charset . ';';
        $tables[self::AD_BELONGS_TABLE] = '
                    CREATE TABLE IF NOT EXISTS ' . self::AD_BELONGS_TABLE . ' (
                        belongs_id bigint(20) unsigned NOT NULL auto_increment,
                        ad_set_id bigint(20) unsigned NOT NULL,
                        type_id bigint(20) unsigned NOT NULL,
                        type_name enum("usergroup", "forum", "topic", "post") NOT NULL,
                        PRIMARY KEY (belongs_id),
                        KEY ad_set_id_idx (ad_set_id),
                        KEY type_id_idx (type_id),
                        unique ad_set_id__type_id__type_name (ad_set_id, type_id, type_name)
                    ) ' . $charset . ';';
        $tables[self::AD_KEYWORDS_TABLE] = '
                    CREATE TABLE IF NOT EXISTS ' . self::AD_KEYWORDS_TABLE . ' (
                        keyword_id bigint(20) unsigned NOT NULL auto_increment,
                        ad_set_id bigint(20) unsigned NOT NULL,
                        keyword varchar(64) NOT NULL,
                        PRIMARY KEY (keyword_id),
                        KEY ad_set_id_idx (ad_set_id),
                        unique ad_set_id__keyword (ad_set_id, keyword)
                    ) ' . $charset . ';';
        $tables[self::AD_LOCATION_TABLE] = '
                    CREATE TABLE IF NOT EXISTS ' . self::AD_LOCATION_TABLE . ' (
                        location_id bigint(20) unsigned NOT NULL auto_increment,
                        ad_set_id bigint(20) unsigned NOT NULL,
                        hook varchar(128) NOT NULL,
                        PRIMARY KEY (location_id),
                        KEY ad_set_id_idx (ad_set_id),
                        unique ad_set_id__hook (ad_set_id, hook)
                    ) ' . $charset . ';';
        $tables[self::AD_DATE_RANGE_TABLE] = '
                    CREATE TABLE IF NOT EXISTS ' . self::AD_DATE_RANGE_TABLE . ' (
                        date_range_id bigint(20) unsigned NOT NULL auto_increment,
                        ad_set_id bigint(20) unsigned NOT NULL,
                        dt_from datetime NOT NULL,
                        dt_to datetime NOT NULL,
                        PRIMARY KEY (date_range_id),
                        KEY ad_set_id_idx (ad_set_id)
                    ) ' . $charset . ';';

        foreach ($tables as $sql) {
            SP()->DB->execute($sql);
        }
        // Drop function strip_tags
        SP()->DB->execute("DROP FUNCTION IF EXISTS strip_tags");
        // Create function strip_tags
        SP()->DB->execute("
            CREATE FUNCTION strip_tags( x longtext) RETURNS longtext
            LANGUAGE SQL NOT DETERMINISTIC READS SQL DATA
            BEGIN
            DECLARE sstart INT UNSIGNED;
            DECLARE ends INT UNSIGNED;
            SET sstart=LOCATE('<', x, 0);
            IF(sstart>0) THEN
            REPEAT
            SET ends=LOCATE('>', x, sstart);
            SET x=CONCAT(SUBSTRING(x, 1, sstart-1), SUBSTRING(x, ends+1)) ;
            SET sstart=LOCATE('<', x, 1);
            UNTIL sstart<1 END REPEAT;
            END IF;
            return x;
            END");

        // add our tables to installed list
        $spTables = SP()->options->get('installed_tables');
        if ($spTables) {
            foreach (array_keys($tables) as $table) {
                if (!in_array($table, $spTables)) {
                    $spTables[] = $table;
                }
            }
            SP()->options->update('installed_tables', $spTables);
        }
    }

    /**
     * Deactivate ads plugin
     * 
     * @since 1.0
     * 
     * @access public
     */
    public static function doDeactivate() {
        // remove glossary entries
        sp_remove_glossary_plugin('sp-ads');
    }

    /**
     * Delete tables when ads plugin uninstall
     * 
     * @since 1.0
     * 
     * @access public
     */
    public static function doUninstall() {
        $tables = array(
            self::AD_BELONGS_TABLE,
            self::AD_KEYWORDS_TABLE,
            self::AD_LOCATION_TABLE,
            self::AD_DATE_RANGE_TABLE,
            self::AD_TABLE,
            self::AD_HITS_TABLE,
            self::AD_SETS_TABLE,
        );
        // remove tables from db
        foreach ($tables as $table) {
            SP()->DB->execute('DROP TABLE IF EXISTS ' . $table);
        }
        // and from installed list
        $spTables = SP()->options->get('installed_tables');
        if ($spTables) {
            foreach ($tables as $table) {
                unset($spTables[$table]);
            }
            SP()->options->update('installed_tables', $spTables);
        }
        self::doDeactivate();
    }

    /**
     * Get ad set item
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $id
     * @return stdClass|null
     */
    public static function getAdSetById($id) {
        return SP()->DB->select('SELECT * FROM ' . self::AD_SETS_TABLE . ' WHERE ad_set_id=' . (int) $id, 'row');
    }

    /**
     * Get ad sets
     * 
     * @since 1.0
     * 
     * @access public
     * @param \DateTime $dtFrom
     * @param \DateTime $dtTo
     * @return array
     */
    public static function getAdSets(\DateTime $dtFrom = null, \DateTime $dtTo = null) {
        $sql = 'SELECT sets.*,';
        $sql .= ' (SELECT COUNT(h.ad_id) FROM ' . self::AD_HITS_TABLE . ' AS h';
        $sql .= ' LEFT JOIN ' . self::AD_TABLE . ' USING(ad_id)';
        $sql .= ' LEFT JOIN ' . self::AD_SETS_TABLE . ' AS _sets USING(ad_set_id)';
        $sql .= ' WHERE sets.ad_set_id=_sets.ad_set_id';
        if ($dtFrom) {
            $sql .= ' AND DATE(h.dt)>="' . $dtFrom->format('Y-m-d') . '"';
            if ($dtTo) {
                $sql .= ' AND DATE(h.dt)<="' . $dtTo->format('Y-m-d') . '"';
            }
        }
        $sql .= ' ) AS hits,';
        $sql .= ' (SELECT COUNT(ad.ad_id) FROM ' . self::AD_TABLE . ' AS ad WHERE ad.ad_set_id=sets.ad_set_id) AS count_ads';
        $sql .= ' FROM ' . self::AD_SETS_TABLE . ' AS sets';
        $sql .= ' GROUP BY sets.ad_set_id';
        return SP()->DB->select($sql);
    }

    /**
     * Add Ad Set
     * 
     * @since 1.0
     * 
     * @access public
     * @param string $name
     * @param boolean $isActive
     * @param boolean $combine
     * @return null|int
     */
    public static function addAdSet($name, $isActive, $combine) {
        $query = new stdClass();
        $query->table = self::AD_SETS_TABLE;
        $query->fields = array('name', 'is_active', 'combine');
        $query->data = array($name, (int) $isActive, (int) $combine);
        if (SP()->DB->insert($query)) {
            return self::_getLastInsertId();
        }
    }

    /**
     * Update ad set
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $adSetId
     * @param string $name
     * @param boolean $isActive
     * @param boolean $combine
     * @return boolean
     */
    public static function updateAdSet($adSetId, $name, $isActive, $combine) {
        $query = new stdClass();
        $query->table = self::AD_SETS_TABLE;
        $query->fields = array('name', 'is_active', 'combine');
        $query->data = array($name, (int) $isActive, (int) $combine);
        $query->where = 'ad_set_id=' . (int) $adSetId;
        return SP()->DB->update($query);
    }

    /**
     * Delete ad set
     * 
     * @since 1.0
     * 
     * @access public
     * @param mixed int|array $adIds
     * @return boolean
     */
    public static function deleteAdSet($adSetIds) {
        foreach ((array) $adSetIds as $id) {
            if ($id = SP()->filters->integer($id)) {
                SP()->DB->execute('DELETE FROM ' . self::AD_BELONGS_TABLE . ' WHERE ad_set_id=' . $id);
                SP()->DB->execute('DELETE FROM ' . self::AD_KEYWORDS_TABLE . ' WHERE ad_set_id=' . $id);
                SP()->DB->execute('DELETE FROM ' . self::AD_LOCATION_TABLE . ' WHERE ad_set_id=' . $id);
                SP()->DB->execute('DELETE FROM ' . self::AD_DATE_RANGE_TABLE . ' WHERE ad_set_id=' . $id);
                SP()->DB->execute('DELETE FROM ' . self::AD_HITS_TABLE . ' WHERE ad_id IN(SELECT ad_id FROM '
                        . self::AD_TABLE . ' WHERE ad_set_id=' . $id . ')');
                SP()->DB->execute('DELETE FROM ' . self::AD_TABLE . ' WHERE ad_set_id=' . $id);
                SP()->DB->execute('DELETE FROM ' . self::AD_SETS_TABLE . ' WHERE ad_set_id=' . $id);
            }
        }
        return true;
    }

    /**
     * Checks is ad set name unique
     * 
     * @since 1.0
     * 
     * @access public
     * @param string $name
     * @param int $adSetId [optional]
     * @return boolean
     */
    public static function isUniqueAdSetName($name, $adSetId = null) {
        $sql = 'SELECT 1 FROM ' . self::AD_SETS_TABLE . " WHERE name='$name'";
        if ($adSetId) {
            $sql .= ' AND ad_set_id<>' . (int) $adSetId;
        }
        return !SP()->DB->select($sql);
    }

    /**
     * Checks is ad name unique
     * 
     * @since 1.0
     * 
     * @access public
     * @param string $name
     * @param int $adSetId
     * @param int $adId [optional]
     * @return boolean
     */
    public static function isUniqueAdName($name, $adSetId, $adId = null) {
        $sql = 'SELECT 1 FROM ' . self::AD_TABLE;
        $sql .= " WHERE name='$name' AND ad_set_id=" . (int) $adSetId;
        if ($adId) {
            $sql .= ' AND ad_id<>' . (int) $adId;
        }
        return !SP()->DB->select($sql);
    }

    /**
     * Get ad item
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $id
     * @return stdClass|null
     */
    public static function getAdById($id) {
        return SP()->DB->select('SELECT ad.*, sets.name AS set_name FROM ' . self::AD_TABLE . ' AS ad'
                        . ' LEFT JOIN ' . self::AD_SETS_TABLE . ' AS sets USING(ad_set_id)'
                        . ' WHERE sets.ad_set_id IS NOT NULL AND ad_id=' . (int) $id, 'row');
    }

    /**
     * Get all ads items
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $adSetId
     * @param \DateTime $dtFrom [optioan]
     * @param \DateTime $dtTo [optioan]
     * @return array
     */
    public static function getAds($adSetId, \DateTime $dtFrom = null, \DateTime $dtTo = null) {
        $sql = 'SELECT ad.*,';
        $sql .= ' (SELECT COUNT(h.ad_id) FROM ' . self::AD_HITS_TABLE . ' AS h';
        $sql .= ' WHERE h.ad_id=ad.ad_id';
        if ($dtFrom) {
            $sql .= ' AND DATE(h.dt)>="' . $dtFrom->format('Y-m-d') . '"';
            if ($dtTo) {
                $sql .= ' AND DATE(h.dt)<="' . $dtTo->format('Y-m-d') . '"';
            }
        }
        $sql .= ' ) AS hits FROM ' . self::AD_TABLE . ' AS ad';
        $sql .= ' LEFT JOIN ' . self::AD_SETS_TABLE . ' AS sets USING(ad_set_id)';
        $sql .= ' WHERE sets.ad_set_id=' . (int) $adSetId;
        $sql .= ' GROUP BY ad.ad_id ORDER BY ad.name';
        return SP()->DB->select($sql);
    }

    /**
     * Get ads on hook
     * 
     * @since 1.0
     * 
     * @access public
     * @param string $hook
     * @param mixed array|int $exceptIds [optional]
     * @return array|null
     */
    public static function getAdsOnHook($hook, $exceptIds = null) {
        if (!$hook) {
            return;
        }
        $userGroupsIds = array();
        foreach (SP()->user->thisUser->memberships as $membership) {
            array_push($userGroupsIds, $membership['usergroup_id']);
        }
        $pageData = SP()->rewrites->pageData;
        $forumId = empty($pageData['forumid']) ? 0 : (int) $pageData['forumid'];
        $topicId = empty($pageData['topicid']) ? 0 : (int) $pageData['topicid'];
        $date = date('Y-m-d');
        #
        $sql = 'SELECT res.combine, res.hits, res.ad_id, res.size, res.content FROM (';
        $sql .= 'SELECT sets.combine, ad.*,';
        $sql .= ' (SELECT COUNT(h.ad_id) FROM ' . self::AD_HITS_TABLE . ' AS h WHERE h.ad_id=ad.ad_id) AS hits';
        $sql .= ' FROM ' . self::AD_TABLE . ' ad';
        $sql .= ' LEFT JOIN ' . self::AD_SETS_TABLE . ' sets ON sets.ad_set_id=ad.ad_set_id';
        $sql .= ' LEFT JOIN ' . self::AD_BELONGS_TABLE . ' usergroup ON sets.ad_set_id=usergroup.ad_set_id AND usergroup.type_name="usergroup"';
        $sql .= ' LEFT JOIN ' . self::AD_BELONGS_TABLE . ' forum ON sets.ad_set_id=forum.ad_set_id AND forum.type_name="forum"';
        $sql .= ' LEFT JOIN ' . self::AD_BELONGS_TABLE . ' topic ON sets.ad_set_id=topic.ad_set_id AND topic.type_name="topic"';
        $sql .= ' LEFT JOIN ' . self::AD_LOCATION_TABLE . ' l ON sets.ad_set_id=l.ad_set_id';
        $sql .= ' LEFT JOIN ' . self::AD_DATE_RANGE_TABLE . ' dr ON sets.ad_set_id=dr.ad_set_id';
        $sql .= ' LEFT JOIN ' . self::AD_KEYWORDS_TABLE . ' k ON sets.ad_set_id=k.ad_set_id';
        # only is active
        $sql .= ' WHERE sets.is_active>0 AND ad.is_active>0';
        $sql .= " AND hook='$hook'";
        // if 3 locations are checked then ads should appear in all three location
        # prevent duplicate ad on page
        # if (!empty($exceptIds) && ($exceptIds = self::_arraySqlInInt($exceptIds))) {
        #     $sql .= ' AND ad.ad_id NOT IN(' . implode(',', $exceptIds) . ')';
        # }
        # check date
        if (!empty($date)) {
            $sql .= " AND (date_range_id IS NULL OR (DATE(dt_from)<='$date' AND DATE(dt_to)>='$date'))";
        }
        # check belongs forums, topics
        switch ($pageData['pageview']) {
            case 'group':
                $sql .= ' AND ((forum.type_id IS NULL AND topic.type_id IS NULL) OR forum.type_id=0)';
                break;
            case 'forum':
                $sql .= ' AND ((forum.type_id IS NULL AND topic.type_id IS NULL) OR forum.type_id=' . (int) $forumId . ')';
                break;
            case 'topic':
                $sql .= ' AND ((forum.type_id IS NULL AND topic.type_id IS NULL) OR topic.type_id=' . (int) $topicId . ')';
                break;
            default:
                $sql .= ' AND forum.type_id IS NULL AND topic.type_id IS NULL';
        }
        # check belongs usergroups
        $sql .= ' AND (usergroup.type_id IS NULL';
        if (!empty($userGroupsIds)) {
            $sql .= ' OR usergroup.type_id IN (' . implode(',', $userGroupsIds) . ')';
        }
        $sql .= ')';
        # find keyword >
        if (!$forumId) {
            $sql .= ' AND keyword_id IS NULL';
        } else {
            $sql .= ' AND (keyword_id IS NULL OR (';
            # find keyword in topic_name
            $sql .= '(SELECT 1 FROM ' . SPTOPICS . ' WHERE forum_id=' . (int) $forumId;
            if ($topicId) {
                $sql .= ' AND topic_id=' . (int) $topicId;
            }
            $sql .= ' AND strip_tags(topic_name) RLIKE CONCAT("[[:<:]]", keyword, "[[:>:]]") LIMIT 1)';
            # find keyword in post_content
            $sql .= ' OR (SELECT 1 FROM ' . SPPOSTS . ' WHERE forum_id=' . (int) $forumId;
            if ($topicId) {
                $sql .= ' AND topic_id=' . (int) $topicId;
            }
            $sql .= ' AND strip_tags(post_content) RLIKE CONCAT("[[:<:]]", keyword, "[[:>:]]") LIMIT 1)';
            $sql .= '))';
        }
        # < find keyword
        #
        $sql .= ' GROUP BY ad.ad_id';
        # check max views
        $sql .= ' HAVING (ad.max_views IS NULL OR ad.max_views<1 OR ad.max_views>hits)';
        $sql .= ' ORDER BY hits ASC LIMIT 100';
        $sql .= ') AS res';
        $sql .= ' GROUP BY res.ad_set_id';
        $sql .= ' ORDER BY hits ASC';
        $res = SP()->DB->select($sql);
        $combinePlace = false;
        foreach ($res as $k => $row) {
            if ($row->combine) {
                if (!$combinePlace) {
                    $combinePlace = true;
                } else {
                    unset($res[$k]);
                }
            }
        }
        return array_values($res);
    }

    /**
     * Add ad
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $adSetId
     * @param string $name
     * @param string $content
     * @param int $maxViews
     * @param string $size
     * @param boolean $isActive
     * @param boolean $scriptAllowed
     * @return int id of last added ad
     */
    public static function addAd($adSetId, $name, $content, $maxViews, $size, $isActive, $scriptAllowed) {
        $query = new stdClass();
        $query->table = self::AD_TABLE;
        $query->fields = array('ad_set_id', 'name', 'content', 'max_views', 'size', 'is_active', 'script_allowed');
        $query->data = array((int) $adSetId, $name, $content, (int) $maxViews, $size, (int) $isActive, (int) $scriptAllowed);
        if (SP()->DB->insert($query)) {
            return self::_getLastInsertId();
        }
    }

    /**
     * Update ad
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $adId
     * @param string $name
     * @param string $content
     * @param int $maxViews
     * @param string $size
     * @param boolean $isActive
     * @param boolean $scriptAllowed
     * @return boolean
     */
    public static function updateAd($adId, $name, $content, $maxViews, $size, $isActive, $scriptAllowed) {
        $query = new stdClass();
        $query->table = self::AD_TABLE;
        $query->fields = array('name', 'content', 'max_views', 'size', 'is_active', 'script_allowed');
        $query->data = array($name, $content, (int) $maxViews, $size, (int) $isActive, (int) $scriptAllowed);
        $query->where = 'ad_id=' . (int) $adId;
        return SP()->DB->update($query);
    }

    /**
     * Delete ad
     * 
     * @since 1.0
     * 
     * @access public
     * @param mixed int|array $adIds
     * @return boolean
     */
    public static function deleteAd($adIds) {
        foreach ((array) $adIds as $id) {
            if ($id = SP()->filters->integer($id)) {
                SP()->DB->execute('DELETE FROM ' . self::AD_TABLE . ' WHERE ad_id=' . $id);
                SP()->DB->execute('DELETE FROM ' . self::AD_HITS_TABLE . ' WHERE ad_id=' . $id);
            }
        }
        return true;
    }

    /**
     * Add ads hit
     * 
     * @since 1.0
     * 
     * @access public
     * @param mixed int|array $adIds
     */
    public static function addAdHit($adIds) {
        if ($adIds && ($adIds = self::_arraySqlInInt($adIds))) {
            foreach ($adIds as $adId) {
                $query = new stdClass();
                $query->table = self::AD_HITS_TABLE;
                $query->fields = array('ad_id', 'dt');
                $query->data = array($adId, date('Y-m-d H:i:s'));
                SP()->DB->insert($query);
            }
        }
    }

    /**
     * Get hooks of ad set
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $adSetId
     * @return array
     */
    public static function getAdSetHooks($adSetId) {
        return SP()->DB->select('SELECT location_id, hook FROM ' . self::AD_LOCATION_TABLE . ' WHERE ad_set_id=' . (int) $adSetId);
    }

    /**
     * Save ad set hooks
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $adSetId
     * @param mixed string|array $hooks [optional]
     */
    public static function saveAdSetLocationHooks($adSetId, $hooks = null) {
        if (!$adSetId = (int) $adSetId) {
            return;
        }
        if (!$hooks) {
            SP()->DB->execute('DELETE FROM ' . self::AD_LOCATION_TABLE
                    . ' WHERE ad_set_id=' . $adSetId);
        } else {
            $hooks = self::_arraySqlInStr($hooks);
            $values = array();
            foreach ($hooks as $hook) {
                array_push($values, "({$adSetId}, {$hook})");
            }
            $sql = 'INSERT IGNORE INTO ' . self::AD_LOCATION_TABLE;
            $sql .= ' (ad_set_id, hook) VALUES ' . implode(',', $values);
            SP()->DB->execute($sql);
            SP()->DB->execute('DELETE FROM ' . self::AD_LOCATION_TABLE
                    . ' WHERE ad_set_id=' . $adSetId
                    . ' AND hook NOT IN(' . implode(',', $hooks) . ')');
        }
    }

    /**
     * Get all forums
     * 
     * @since 1.0
     * 
     * @access public
     * @return array
     */
    public static function getForums() {
        return SP()->DB->select('SELECT forum_id, forum_name FROM ' . SPFORUMS);
    }

    /**
     * Get count forum topics
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $forumId
     * @return boolean
     */
    public static function getCountForumTopics($forumId) {
        $sql = 'SELECT COUNT(topic_id) AS count FROM ' . SPTOPICS . ' WHERE forum_id=' . (int) $forumId;
        return SP()->DB->select($sql, 'row')->count;
    }

    /**
     * Get topics
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $forumId
     * @param int $adSetId [optional]
     * @return array
     */
    public static function getTopics($forumId, $adSetId = null) {
        $sql = 'SELECT topic_id, topic_name FROM ' . SPTOPICS . ' WHERE  forum_id=' . (int) $forumId;
        if ($adSetId) {
            $sql .= ' AND topic_id IN (SELECT type_id FROM ' . self::AD_BELONGS_TABLE
                    . ' WHERE type_name="topic" AND ad_set_id=' . (int) $adSetId . ')';
        }
        return SP()->DB->select($sql);
    }

    /**
     * Search topics
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $forumId
     * @param string $query
     * @return array
     */
    public static function searchTopics($forumId, $query) {
        $query = trim($query);
        $sql = 'SELECT topic_id AS id, CONCAT("#", topic_id , " " , topic_name) AS text FROM ' . SPTOPICS
                . ' WHERE  forum_id=' . (int) $forumId;
        $sql .= " AND (topic_id LIKE '%{$query}%' OR topic_name LIKE '%{$query}%') LIMIT 100";
        return SP()->DB->select($sql);
    }

    /**
     * Get all user groups
     * 
     * @since 1.0
     * 
     * @access public
     * @return array
     */
    public static function getUserGroups() {
        return SP()->DB->select('SELECT usergroup_id, usergroup_name FROM ' . SPUSERGROUPS);
    }

    /**
     * Get ids belong type for ad set
     * 
     * @since 1.0
     * 
     * @access public
     * @param string $type
     * @param int $adSetId
     * @return array
     */
    public static function getAdSetBelongs($type, $adSetId) {
        $res = array();
        $rows = SP()->DB->select('SELECT type_id FROM ' . self::AD_BELONGS_TABLE
                . ' WHERE ad_set_id=' . (int) $adSetId
                . " AND type_name='{$type}'");
        foreach ($rows as $item) {
            array_push($res, $item->type_id);
        }
        return $res;
    }

    /**
     * Save ad set belongs (usergroups, forums, topics, posts)
     * 
     * @since 1.0
     * 
     * @access public
     * @param string $belongsType [usergroup, forum, topic, post]
     * @param int $adId
     * @param mixed int|array $userGroups [optional]
     */
    public static function saveAdSetBelongs($belongsType, $adId, $ids = null) {
        $adId = (int) $adId;
        if ($adId && strlen($belongsType)) {
            if (!$ids) {
                SP()->DB->execute('DELETE FROM ' . self::AD_BELONGS_TABLE
                        . ' WHERE ad_set_id=' . $adId
                        . " AND type_name='{$belongsType}'");
            } else {
                $ids = self::_arraySqlInInt($ids);
                $values = array();
                foreach ($ids as $id) {
                    array_push($values, "({$adId}, {$id}, '{$belongsType}')");
                }
                $sql = 'INSERT IGNORE INTO ' . self::AD_BELONGS_TABLE
                        . ' (ad_set_id, type_id, type_name) VALUES ' . implode(',', $values);
                SP()->DB->execute($sql);
                SP()->DB->execute('DELETE FROM ' . self::AD_BELONGS_TABLE
                        . ' WHERE ad_set_id=' . $adId
                        . " AND type_name='{$belongsType}' AND type_id NOT IN(" . implode(',', $ids) . ')');
            }
        }
    }

    /**
     * Get keywords of ad set
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $adSetId
     * @return array
     */
    public static function getAdSetKeywords($adSetId) {
        $res = array();
        $rows = SP()->DB->select('SELECT keyword FROM ' . self::AD_KEYWORDS_TABLE . ' WHERE ad_set_id=' . (int) $adSetId);
        foreach ($rows as $row) {
            array_push($res, $row->keyword);
        }
        return $res;
    }

    /**
     * Save keywords of ad set
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $adSetId
     * @param mixed string|array $keywords [optional]
     */
    public static function saveAdsetKeywords($adSetId, $keywords = null) {
        if (!$adSetId = (int) $adSetId) {
            return;
        }
        $keywords && $keywords = self::_arraySqlInStr($keywords, self::MIN_LEN_WORD);
        if (!$keywords) {
            SP()->DB->execute('DELETE FROM ' . self::AD_KEYWORDS_TABLE
                    . ' WHERE ad_set_id=' . $adSetId);
        } else {
            $values = array();
            foreach ($keywords as $keyword) {
                array_push($values, "({$adSetId}, {$keyword})");
            }
            $sql = 'INSERT IGNORE INTO ' . self::AD_KEYWORDS_TABLE;
            $sql .= ' (ad_set_id, keyword) VALUES ' . implode(',', $values);
            SP()->DB->execute($sql);
            SP()->DB->execute('DELETE FROM ' . self::AD_KEYWORDS_TABLE
                    . ' WHERE ad_set_id=' . $adSetId
                    . ' AND keyword NOT IN(' . implode(',', $keywords) . ')');
        }
    }

    /**
     * Get ad set date ranges
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $adSetId
     * @return array
     */
    public static function getAdSetDateRanges($adSetId) {
        return SP()->DB->select("SELECT date_range_id, dt_from, dt_to FROM " .
                        self::AD_DATE_RANGE_TABLE . " WHERE ad_set_id=" . (int) $adSetId . " ORDER BY dt_from");
    }

    /**
     * Save ad set date ranges
     * 
     * @since 1.0
     * 
     * @access public
     * @param int $adSetId
     * @param array $dtRanges
     */
    public static function saveAdSetDateRanges($adSetId, $dtRanges) {
        if (!$adSetId = (int) $adSetId) {
            return;
        }
        if (!$dtRanges) {
            SP()->DB->execute('DELETE FROM ' . self::AD_DATE_RANGE_TABLE . ' WHERE ad_set_id=' . $adSetId);
            return;
        }
        $dtRanges = (array) $dtRanges;
        if ($ids = self::_arraySqlInInt(array_keys($dtRanges))) {
            SP()->DB->execute('DELETE FROM ' . self::AD_DATE_RANGE_TABLE . ' WHERE ad_set_id=' . $adSetId
                    . ' AND date_range_id NOT IN(' . implode(',', $ids) . ')');
        }
        foreach ((array) $dtRanges as $dateRangeId => $dtRange) {
            if (!self::_isValidDateRange($adSetId, $dtRange[0], $dtRange[1], $dateRangeId)) {
                SP()->DB->execute('DELETE FROM ' . self::AD_DATE_RANGE_TABLE . ' WHERE ad_set_id=' . $adSetId
                        . ' AND date_range_id=' . (int) $dateRangeId);
            } else {
                $query = new stdClass;
                $query->table = self::AD_DATE_RANGE_TABLE;
                $query->fields = array('ad_set_id', 'dt_from', 'dt_to');
                $query->data = array($adSetId, $dtRange[0]->format('Y-m-d'), $dtRange[1]->format('Y-m-d'));
                if ($dateRangeId && SP()->DB->select('SELECT 1 FROM ' . self::AD_DATE_RANGE_TABLE
                                . ' WHERE date_range_id=' . (int) $dateRangeId)) {
                    $query->where = 'date_range_id=' . (int) $dateRangeId;
                    SP()->DB->update($query);
                } else {
                    SP()->DB->insert($query);
                }
            }
        }
    }

    /**
     * Validate date range
     * 
     * @since 1.0
     * 
     * @access private
     * @param int $adSetId
     * @param \DateTime $dtFrom [optional]
     * @param \DateTime $dtTo [optional]
     * @param int $dateRangeId [optional]
     * @return boolean
     */
    private static function _isValidDateRange($adSetId, \DateTime $dtFrom = null, \DateTime $dtTo = null, $dateRangeId = null) {
        if (!$adSetId || !$dtFrom || !$dtTo || $dtFrom > $dtTo) {
            return false;
        }
        $sql = 'SELECT * FROM ' . self::AD_DATE_RANGE_TABLE . ' WHERE ad_set_id=' . (int) $adSetId;
        if ($dateRangeId) {
            $sql .= ' AND date_range_id<>' . (int) $dateRangeId;
        }
        $sql .= ' AND ((DATE(dt_from) BETWEEN "' . $dtFrom->format('Y-m-d') . '" AND "' . $dtTo->format('Y-m-d') . '")';
        $sql .= ' OR (DATE(dt_to) BETWEEN "' . $dtFrom->format('Y-m-d') . '" AND "' . $dtTo->format('Y-m-d') . '"))';
        return !SP()->DB->select($sql);
    }

    /**
     * Get last insert id
     * 
     * @since 1.0
     * 
     * @access private
     * @return null|int
     */
    private static function _getLastInsertId() {
        $res = SP()->DB->select('SELECT LAST_INSERT_ID() as id', 'row');
        return $res ? $res->id : null;
    }

    /**
     * Auxiliary method
     * 
     * Prepare array of intager for SQL-query 
     * 
     * @since 1.0
     * 
     * @access private
     * @param array|int $value
     * @return array
     */
    private static function _arraySqlInInt($value) {
        return array_unique(array_map(function($id) {
                    return (int) $id;
                }, (array) $value));
    }

    /**
     * Auxiliary method
     * 
     * Prepare array of strings for SQL-query
     * 
     * @since 1.0
     * 
     * @access private
     * @param array|string $value
     * @param int $minStrlen [optional]
     * @return array
     */
    private static function _arraySqlInStr($value, $minStrlen = 1) {
        return array_unique(array_filter(array_map(function($word) use($minStrlen) {
                            $word = esc_sql(trim($word, "\"\r\n\t "));
                            $strlen = mb_strlen($word);
                            if ($strlen >= $minStrlen) {
                                return sprintf('"%s"', $word);
                            }
                        }, (array) $value)));
    }

}
