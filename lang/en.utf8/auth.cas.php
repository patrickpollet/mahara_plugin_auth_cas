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
 */

defined ('INTERNAL') || die();

$string['title'] = 'CAS';
$string['description'] = 'Authenticate against an CAS SSO server';


$string['login'] = 'CAS Login';
$string['notusable'] = 'Please install the PHP LDAP extension';

$string['plugincasnotinstalled'] = 'Your administrator did not install the CAS authentication plugin';
$string['noinstanceofplugincasinstalled'] = 'No instance of the CAS authentication plugin has been associated to an institution';
$string['morethanoneinstanceofplugincasinstalled'] = 'You cannot have more than one instance of the CAS authentication plugin in a Mahara site';


$string['cassettings'] = 'CAS server settings';
$string['ldapsettings'] = 'LDAP settings';


$string['cas_hostname'] = 'CAS server host name';
$string['cas_port'] = 'Port';
$string['cas_version'] = 'Version';
$string['cas_baseuri'] = 'Base URI';
$string['cas_language'] = 'Language';
$string['cas_proxy'] = 'Proxy mode';
$string['cas_logout'] = 'Logout CAS';
$string['cas_certificatecheck'] = 'Server validation';
$string['cas_certificatepath'] = 'Certificate path';
