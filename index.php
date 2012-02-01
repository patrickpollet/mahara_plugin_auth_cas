<?php
/**
 * Mahara: Electronic portfolio, weblog, resume builder and social networking
 * Copyright (C) 2006-2009 Catalyst IT Ltd (http://www.catalyst.net.nz)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    mahara
 * @subpackage auth-cas
 * @author     Patrick Pollet <pp@patrickpollet.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2006-2011 Catalyst IT Ltd http://catalyst.net.nz
 * @copyright  (C) 2011 INSA de Lyon France
 *
 * This file incorporates work covered by the following copyright and
 * permission notice:
 *
 *    Moodle - Modular Object-Oriented Dynamic Learning Environment
 *             http://moodle.com
 *
 *    Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com
 *
 *    This program is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 2 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details:
 *
 *             http://www.gnu.org/copyleft/gpl.html
 */

define('INTERNAL', 1);
define('PUBLIC', 1); // IMPORTANT !!!!

global $CFG, $USER, $SESSION;
require(dirname (dirname (dirname (__FILE__))) . '/init.php');
require_once('pieforms/pieform.php');
require_once(get_config ('docroot') . 'auth/lib.php');
require_once(get_config ('docroot') . 'auth/cas/lib.php');

//we may have to manually start the session when this page is called back by the CAS server
// since in that case the session cookie will not be sent back
@session_start ();

// check that the plugin is installed and activated
if (get_field ('auth_installed', 'active', 'name', 'cas') != 1) {
    $SESSION->add_error_msg (get_string ('plugincasnotinstalled', 'auth.cas'));
    redirect ();
}


//pp_error_log("start",'');

$wantsurl = $CFG->wwwroot;

if ($SESSION->get ('wantsurl')) {
    $wantsurl = $SESSION->get ('wantsurl');
    $SESSION->set ('wantsurl', null);
}
// sanity check the redirect - we don't want to loop
if (preg_match ('/\/auth\/cas\//', $wantsurl)) {
    $wantsurl = $CFG->wwwroot;
}
// must be within this domain
if (!preg_match ('/' . $_SERVER['HTTP_HOST'] . '/', $wantsurl)) {
    $wantsurl = $CFG->wwwroot;
}

//pp_error_log("********************wurl",$wantsurl);

// they are logged in, so they dont need to be here
if ($USER->is_logged_in ()) {
    redirect ($wanturl);
}
//pp_error_log("ligne 77",$wantsurl);


// make sure there is ONE instance of CAS auth plugin and get it's instance id
//TODO may be add an institutionname as an optional parameter to have more than
//one instance allowed
$instances = auth_instance_get_cas_records ();
//pp_error_log('',$instances);
if (count ($instances) == 0) {
    $SESSION->add_error_msg (get_string ('noinstanceofplugincasinstalled', 'auth.cas'));
    redirect ();

}
if (count ($instances) > 1) {
    $SESSION->add_error_msg (get_string ('morethanoneinstanceofplugincasinstalled', 'auth.cas'));
    redirect ();

}

phpCAS::setDebug ('/tmp/phpcas_mahara.log');
$auth = new AuthCas($instances[0]->id);
// Connection to CAS server
$auth->connectCAS ();

//pp_error_log("ligne 85",$wantsurl);

if (phpCAS::CheckAuthentication ()) {
    // OK let's proceed
    
	//remove a php warning about argument 1 of login_submit must be an instance of Pieform  
	login_submit (new Pieform(array('name'=>'dummy')), array('login_username' => phpCAS::getUser (), 'login_password' => 'not cached'));
	
    //login_submit (NULL, array('login_username' => phpCAS::getUser (), 'login_password' => 'not cached'));
    //pp_error_log("retour L_s",$wantsurl);
    redirect ($wantsurl);
}

//pp_error_log("ligne 88",$wantsurl);

phpCAS::forceAuthentication ();
//pp_error_log("this line will never be reached","");


/**
 * Returns all authentication instances using the CAS method
 *
 */
function auth_instance_get_cas_records () {
    $result = get_records_select_array ('auth_instance', "authname = 'cas'");
    $result = empty($result) ? array() : $result;
    return $result;
}

 
