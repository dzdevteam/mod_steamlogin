<?php
/**
 * @version     1.0.0
 * @package     mod_steamlogin
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      DZ Team <dev@dezign.vn> - dezign.vn
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


try {
    // Include the syndicate functions only once
    require_once __DIR__ . '/helper.php';

    $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

    if (JRequest::getVar("try_auth")) {
        $form   = ModSteamLoginHelper::getForm($params);
        require JModuleHelper::getLayoutPath('mod_steamlogin', 'default_form');
        return;
    }
} catch(Exception $e) {
    require JModuleHelper::getLayoutPath('mod_steamlogin', 'default_error');
    return;
}
$user   = JFactory::getUser();
$layout = $params->get('layout', 'default');
$type   = ModSteamLoginHelper::getType();
$return = ModSteamLoginHelper::getReturnURL($params, $type);

// Logged users must load the logout sublayout
if (!$user->guest) {
    $layout .= '_logout';
} else {
    if (JRequest::getVar('janrain_nonce')) {
        $credentials = $_GET;

        $result = JFactory::getApplication()->login($_GET, array('autoregister' => true));
        usleep(300); // Make sure the login session is complete before redirect

        $session = &JFactory::getSession();
        if ($result && $session->get('user.first_connect', false)) {
            $session->clear('user.first_connect');
            JFactory::getApplication()->enqueueMessage(JText::_('MOD_STEAMLOGIN_FIRST_LOGIN_MESSAGE'), 'notice');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit'));
        } else {
            JFactory::getApplication()->redirect(JRoute::_($return));
        }

    }
}

// Display template
require JModuleHelper::getLayoutPath('mod_steamlogin',$layout);
