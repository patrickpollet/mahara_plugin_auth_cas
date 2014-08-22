<?php
/**
 *
 * @package    mahara
 * @subpackage auth-cas
 * @author     Jean-Philippe Gaudreau <jp.gaudreau@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

function xmldb_auth_cas_upgrade($oldversion=0) {
    if ($oldversion < 2014082400) {
        // Convert the first letter of the language string to uppercase.
        $instance = get_record('auth_instance', 'authname', 'cas');
        if ($instance) {
            $instanceconfig = get_record('auth_instance_config', 'instance', $instance->id, 'field', 'cas_language');
            $instanceconfig->value = ucfirst($instanceconfig->value);
            update_record('auth_instance_config', $instanceconfig,
                    array('instance' => $instance->id, 'field' => $instanceconfig->field));
        }

        return true;
    }

    return true;
}
