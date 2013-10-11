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

defined('INTERNAL') || die();
require_once(get_config('docroot') . 'auth/lib.php');
require_once(get_config('docroot') . 'auth/ldap/lib.php');
require_once(get_config('docroot') . 'auth/cas/CAS/CAS.php');
require_once(get_config('docroot') . 'auth/cas/PluginAuthCas.class.php');

/**
 * Authenticates users with CAS and an associated Lightweight Directory Access Protocol
 */
class AuthCas extends AuthLdap {

	public function __construct($id = null) {
		parent::__construct($id); //takes care of initing the config values if $id <>null
		$this->type = 'cas';
		$this->has_instance_config = true;
		//$this->config['studentidfield2'] = 'supannEmpId'; INSA specific setting
		// pp_error_log('constr',$this->config);
		return true;
	}

	public function init($id = null) {
		$this->ready = parent::init($id);
		// Check that required fields are set
		if (empty($this->config['cas_hostname']) ||
		empty($this->config['cas_port']) ||
		empty($this->config['cas_language'])
		) {
			$this->ready = false;
		}
		return $this->ready;
	}


	/**
	 * Attempt to authenticate user
	 *
	 * @param string $user     The user record to authenticate with
	 * @param string $password The password being used for authentication
	 * @return bool            True/False based on whether the user
	 *                         authenticated successfully
	 * @throws AuthUnknownUserException is no LDAP support
	 */
	public function authenticate_user_account($user, $password) {

		// first make sure we are called from auth/cas/index.php
		// this may happen if CAS user typed its credentials in some Mahara login box ...
		global $PHPCAS_CLIENT, $CFG;
		if (!is_object($PHPCAS_CLIENT)) {
			return false;
		}

		$this->must_be_ready();
		$username = $user->username;

		// check ldap functionality exists
		if (!function_exists('ldap_bind')) {
			throw new AuthUnknownUserException('LDAP is not available in your PHP environment. Check that it is properly installed');
		}

		// empty username is not allowed.
		if (empty($username)) {
			return false;
		}
		// For update user info on login
		$update = false;

		if ('1' == $this->config['updateuserinfoonlogin']) {
			$update = true;
		}
		/*******************
		 NO NO
		 if current user is a new user, Mahara has cleared the session
		 so phpCAS::isAuthenticated fails ...
		 $this->connectCAS();
		 if (!(phpCAS::isAuthenticated() || (strtolower(phpCAS::getUser()) != $username) )) {
		 pp_error_log("raté ","isAuthenticated");
		 return false;
		 }
		 *********************/
		/*
		 * note that if phpCAS::isAuthenticated() has not been called within the same session
		 * (only in auth/cas/index.php) before this phpCAS:getUser()
		 * this call will die with phpCAS fatal error , so no way to break in ;-)
		 * and we do not call connectCAS() either ! this should have been done already in auth/cas/index.php
		 */
		if (strtolower(phpCAS::getUser()) != strtolower($username)) {
			//pp_error_log("raté ","test getuser");
			return false;
		}


		if ($user->id && $update) {

			// Retrieve information of user from LDAP via its public method
			$ldapdetails = $this->get_user_info($username);
			// this method returns an object and we want an array below
			$ldapdetails = (array)$ldapdetails;
			// Match database and ldap entries and update in database if required
			$fieldstoimport = array('firstname', 'lastname', 'email', 'studentid', 'preferredname');
			foreach ($fieldstoimport as $field) {
				if (!isset($ldapdetails[$field])) {
					continue;
				}
				$sanitizer = "sanitize_$field";
				$ldapdetails[$field] = $sanitizer($ldapdetails[$field]);
				if (!empty($ldapdetails[$field]) && ($user->$field != $ldapdetails[$field])) {
					$user->$field = $ldapdetails[$field];
					set_profile_field($user->id, $field, $ldapdetails[$field]);
					if (('studentid' == $field) && ('mahara' != $this->institution)) {
						// studentid is specific for the institution, so store it there too.
						$dataobject = array(
                                    'usr' => $user->id,
                                    'institution' => $this->institution,
                                    'ctime' => db_format_timestamp(time()),
                                    'studentid' => $user->studentid,
						);
						$whereobject = $dataobject;
						unset($whereobject['ctime']);
						unset($whereobject['studentid']);
						ensure_record_exists('usr_institution', $whereobject, $dataobject);
						unset($dataobject);
						unset($whereobject);
					}
				}
			}

		}
		return true;
	}

	/**
	 * Connect to the CAS (clientcas connection or proxycas connection)
	 * borrowed from Moodle code
	 */
	public function connectCAS() {
		global $PHPCAS_CLIENT, $CFG;


		//  pp_error_log("cas config",$this->config);
		// pp_error_log("cas client",$PHPCAS_CLIENT);
		if (!is_object($PHPCAS_CLIENT)) {
			// Make sure phpCAS doesn't try to start a new PHP session when connecting to the CAS server (false)
			if ($this->config['cas_proxy']) {
				phpCAS::proxy((string)$this->config['cas_version'], $this->config['cas_hostname'],
				(int)$this->config['cas_port'], $this->config['cas_baseuri'], false);
			} else {
				phpCAS::client((string)$this->config['cas_version'], $this->config['cas_hostname'],
				(int)$this->config['cas_port'], $this->config['cas_baseuri'], false);
			}

			if ($this->config['cas_certificatecheck'] && $this->config['cas_certificatepath']) {
				phpCAS::setCasServerCACert($this->config['cas_certificatepath']);
			} else {
				// Don't try to validate the server SSL credentials
				phpCAS::setNoCasServerValidation();
			}
			phpCAS::setLang($this->config['cas_language']);
		}
	}

	/**
	 * @override
	 * also logout from CAS is specified in the configuration
	 */
	public function logout() {
		global $CFG;
		if ($this->config['cas_logout']) {
			$backurl = $CFG->wwwroot;
			$this->connectCAS();
			// phpCAS::logoutWithURL ($backurl);
			//should be with CAS server >=3.3.5 see  http://tracker.moodle.org/browse/MDL-27610   and https://wiki.jasig.org/display/CASC/phpCAS+logout
			//phpCAS::logoutWithRedirectService($backurl);

			if (method_exists('phpCAS', 'logoutWithRedirectService'))    {
				//pp_error_log ('logout via','phpCAS::logoutWithRedirectService');
				phpCAS::logoutWithRedirectService($backurl);
			}
			else {
				//pp_error_log('logout via ','phpCAS::logoutWithURL');
				phpCAS::logoutWithURL($backurl);
			}
		}
	}


}



