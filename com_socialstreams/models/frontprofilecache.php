<?php

// No direct access
defined('_JEXEC') or die('Restricted access');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
jimport('joomla.application.component.model');

/**
 * Description of Socialcount
 *
 * @author stuart
 */
class SocialstreamsModelFrontProfilecache extends JModel {

    public $_cache = array();

    function __construct() {
        parent::__construct();
    }

    function getFacebookCache() {
        if (empty($this->_cache['facebook']))
            $this->_cache['facebook'] = $this->getCache('facebook');
        return count($this->_cache['facebook']);
    }

    function getTwitterCache() {
        if (empty($this->_cache['twitter']))
            $this->_cache['twitter'] = $this->getCache('twitter');
        return count($this->_cache['twitter']);
    }

    function getLinkedinCache() {
        if (empty($this->_cache['linkedin']))
            $this->_cache['linkedin'] = $this->getCache('linkedin');
        return count($this->_cache['linkedin']);
    }

    function getGoogleCache() {
        if (empty($this->_cache['google']))
            $this->_cache['google'] = $this->getCache('google');
        return count($this->_cache['google']);
    }

    function getCache($network) {
        $db = & JFactory::getDBO();
        $now = time();
        $profile_cache = array();
        $query = 'SELECT * FROM ' . $db->nameQuote('#__socialstreamprofiles');
        $query.= ' WHERE ' . $db->nameQuote('network') . ' = ' . $db->Quote($network);
        $db->setQuery($query);
        $profiles = $db->loadObjectlist();
//        dump($profiles, 'Profiles');
        $profile_class = 'appsol' . ucfirst($network) . 'Profile';
        foreach ($profiles as $stored_profile) {
            $profile_cache[$stored_profile->user] = new $profile_class();
            if ($profile = json_decode($stored_profile->profile))
                $profile_cache[$stored_profile->user]->setProfile($profile);
        }
        return $profile_cache;
    }
    
    function getProfile($network, $user) {
        // If the profile is already cached locally, return that
        if (isset($this->_profiles[$network][$user]))
            return $this->_profiles[$network][$user];
        $db = & JFactory::getDBO();
        $query = 'SELECT * FROM ' . $db->nameQuote('#__socialstreamprofiles');
        $query.= ' WHERE ' . $db->nameQuote('network') . ' = ' . $db->Quote($network);
        $query.= ' AND ' . $db->nameQuote('user') . ' = ' . $db->Quote($user);
        $db->setQuery($query);

        if (!$stored_profile = $db->loadObject())
            return false;

        $profile_class = 'appsol' . ucfirst($network) . 'Profile';
        /**
         * @todo implement install check to see if >= PHP5.2 &json_encode available, otherwise use serialize
         */
        $stream_profile = new $profile_class();
        if ($profile = json_decode($stored_profile->profile))
            $stream_profile->setProfile($profile);
        return $stream_profile;
    }

}

?>
