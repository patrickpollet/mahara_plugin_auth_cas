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
$string['description'] = 'Authentification à l\'aide d\'un serveur CAS SSO';
$string['notusable'] = 'Vous ne pouvez utiliser qu\'une seule instance du plugin CAS dans tout le site';

$string['login'] = 'Connexion CAS';

$string['plugincasnotinstalled'] = 'Votre administrateur n\'a pas installé le plugin d\'authentification CAS';
$string['noinstanceofplugincasinstalled'] = 'Aucune instance du plugin d\'authentification CAS n\'a été asscociée à aucune institution';
$string['morethanoneinstanceofplugincasinstalled'] = 'Vous ne pouvez pas avoir plus d\'une instance du plugin d\'authentification CAS dans tout le site Mahara';


$string['cassettings'] = 'Paramètres du serveur CAS';
$string['ldapsettings'] = 'Paramètres LDAP';


$string['cas_hostname'] = 'Nom d\'hôte du serveur CAS';
$string['cas_port'] = 'Port';
$string['cas_version'] = 'Version';
$string['cas_baseuri'] = 'Base de l\'URI';
$string['cas_language'] = 'Langue';
$string['cas_proxy'] = 'Mode du Proxy';
$string['cas_logout'] = 'Déconnexion CAS';
$string['cas_certificatecheck'] = 'Valider le serveur';
$string['cas_certificatepath'] = 'Chemin vers le certificat';
