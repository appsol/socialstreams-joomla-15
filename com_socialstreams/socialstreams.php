<?php

// No direct access
defined('_JEXEC') or die('Restricted access');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// Require the base controller
$registry = & JFactory::getConfig();
// import JFile
jimport('joomla.filesystem.file');
// Load the registry file
if (JFile::exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'socialstreams.ini', 'INI', 'socialstreams'))
    $registry->loadFile(JPATH_COMPONENT_ADMINISTRATOR . DS . 'socialstreams.ini', 'INI', 'socialstreams');
require_once( JPATH_COMPONENT . DS . 'controller.php' );
$controller = JRequest::getCmd('controller', '');
// Require specific controller if requested
if (!empty($controller)) {
    jimport('joomla.filesystem.file');
    $path = JPATH_COMPONENT . DS . 'controllers' . DS . $controller . '.php';
    if (JFile::exists($path)) {
        require_once $path;
    } else {
        // controller does not exist
        JError::raiseError('500', JText::_('Unknown controller'));
    }
}

// Create the controller
$classname = 'SocialstreamsController' . $controller;
$controller = new $classname( );

// Perform the Request task
$controller->execute(JRequest::getVar('task', 'display'));
$ini = $registry->toString('INI', 'socialstreams');
// save INI file
jimport('joomla.filesystem.file');
JFile::write(JPATH_COMPONENT_ADMINISTRATOR . DS . 'socialstreams.ini', $ini);
// Redirect if set by the controller
$controller->redirect();
