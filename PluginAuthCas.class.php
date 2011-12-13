<?php
/**
 * Plugin configuration class
 */


class PluginAuthCas extends PluginAuthLdap
{

    const NAME = 'cas';
    const CAS_HOSTNAME = 'cas_hostname';
    const CAS_PORT = 'cas_port';
    const CAS_VERSION = 'cas_version';
    const CAS_BASEURI = 'cas_baseuri';
    const CAS_LANGUAGE = 'cas_language';
    const CAS_PROXY = 'cas_proxy';
    const CAS_LOGOUT = 'cas_logout';
    const CAS_CERTIFICATECHECK = 'cas_certificatecheck';
    const CAS_CERTIFICATEPATH = 'cas_certificatepath';

    private static $default_config = array(
        self::CAS_HOSTNAME => '',
        self::CAS_PORT => '443',
        self::CAS_VERSION => CAS_VERSION_2_0,
        self::CAS_BASEURI => '',
        self::CAS_LANGUAGE => '',
        self::CAS_PROXY => '0',
        self::CAS_LOGOUT => '0',
        self::CAS_CERTIFICATECHECK => '',
        self::CAS_CERTIFICATEPATH => '',
    );

    public static function has_config()
    {
        return false;
    }

    public static function get_config_options()
    {
        return array();
    }

    public static function has_instance_config()
    {
        return true;
    }

    public static function can_be_disabled()
    {
        return true;
    }


    public static function get_instance_config_options($institution, $instance = 0)
    {
        $caslangprefix = 'PHPCAS_LANG_';
        $CASLANGUAGES = array();

        $consts = get_defined_constants(true);
        foreach ($consts['user'] as $key => $value) {
            if (substr($key, 0, strlen($caslangprefix)) == $caslangprefix) {
                $CASLANGUAGES[$value] = $value;
            }
        }
        if (empty($CASLANGUAGES)) {
            $CASLANGUAGES = array('english' => 'english',
                'french' => 'french');
        }

        $CASVERSIONS = array();
        $CASVERSIONS[CAS_VERSION_1_0] = 'CAS 1.0';
        $CASVERSIONS[CAS_VERSION_2_0] = 'CAS 2.0';

        $yesnoopt = array('yes' => 'Yes', 'no' => 'No');


        //get LDAP instance values
        $parent = parent::get_instance_config_options($institution, $instance);

        // change  what must be changed
        unset($parent['elements']['authname']); // we shall change it to cas
        unset($parent['elements']['institution']); //pieforms does not want hidden in fieldsets
        unset($parent['elements']['instance']);

        // we want it at the 1er item as usual
        $first = array_shift($parent['elements']); // ['instancename'];

        // add CAS specific informations
        // first read possible values from DB
        if ($instance > 0) {
            $default = get_record('auth_instance', 'id', $instance);
            if ($default == false) {
                throw new SystemException('Could not find data for auth instance ' . $instance);
            }
            $current_config = get_records_menu('auth_instance_config', 'instance', $instance, '', 'field, value');

            if ($current_config == false) {
                $current_config = array();
            }

            foreach (self::$default_config as $key => $value) {
                if (array_key_exists($key, $current_config)) {
                    self::$default_config[$key] = $current_config[$key];
                }
            }
        } else {
            $default = new stdClass();
            $default->instancename = '';
        }

        $elements = array(
            // the name of the instance
            'instancename' => $first,
            //the 3 hidden fields that cannot be in fieldsets
            'instance' => array(
                'type' => 'hidden',
                'value' => $instance,
            ),
            'institution' => array(
                'type' => 'hidden',
                'value' => $institution,
            ),
            'authname' => array(
                'type' => 'hidden',
                'value' => self::NAME,
            ),
            // then two filedsets

            'fsCAS' => array(
                'type' => 'fieldset',
                'legend' => get_string('cassettings', 'auth.cas'),
                'collapsible' => true,
                'collapsed' => true,
                'elements' => array(
                    self::CAS_HOSTNAME => array(
                        'type' => 'text',
                        'title' => get_string(self::CAS_HOSTNAME, 'auth.cas'),
                        'rules' => array(
                            'required' => true,
                        ),
                        'defaultvalue' => self::$default_config[self::CAS_HOSTNAME],
                        'help' => true,
                    ),
                    self::CAS_BASEURI => array(
                        'type' => 'text',
                        'title' => get_string(self::CAS_BASEURI, 'auth.cas'),
                        'rules' => array(
                            'required' => true,
                        ),
                        'defaultvalue' => self::$default_config[self::CAS_BASEURI],
                        'help' => true,
                    ),
                    self::CAS_PORT => array(
                        'type' => 'text',
                        'title' => get_string(self::CAS_PORT, 'auth.cas'),
                        'rules' => array(
                            'required' => true,
                        ),
                        'defaultvalue' => self::$default_config[self::CAS_PORT],
                        'help' => true,
                    ),
                    self::CAS_VERSION => array(
                        'type' => 'select',
                        'title' => get_string(self::CAS_VERSION, 'auth.cas'),
                        'options' => $CASVERSIONS,
                        'rules' => array(
                            'required' => true,
                        ),
                        'defaultvalue' => self::$default_config[self::CAS_VERSION],
                        'help' => true,
                    ),
                    self::CAS_VERSION => array(
                        'type' => 'select',
                        'title' => get_string(self::CAS_VERSION, 'auth.cas'),
                        'options' => $CASVERSIONS,
                        'rules' => array(
                            'required' => true,
                        ),
                        'defaultvalue' => self::$default_config[self::CAS_VERSION],
                        'help' => true,
                    ),

                    self::CAS_LANGUAGE => array(
                        'type' => 'select',
                        'title' => get_string(self::CAS_LANGUAGE, 'auth.cas'),
                        'options' => $CASLANGUAGES,
                        'rules' => array(
                            'required' => true,
                        ),
                        'defaultvalue' => self::$default_config[self::CAS_LANGUAGE],
                        'help' => true,
                    ),

                    self::CAS_PROXY => array(
                        'type' => 'checkbox',
                        'title' => get_string(self::CAS_PROXY, 'auth.cas'),
                        'defaultvalue' => self::$default_config[self::CAS_PROXY],
                        'help' => true,
                    ),
                    self::CAS_LOGOUT => array(
                        'type' => 'checkbox',
                        'title' => get_string(self::CAS_LOGOUT, 'auth.cas'),
                        'defaultvalue' => self::$default_config[self::CAS_LOGOUT],
                        'help' => true,
                    ),
                    self::CAS_CERTIFICATECHECK => array(
                        'type' => 'checkbox',
                        'title' => get_string(self::CAS_CERTIFICATECHECK, 'auth.cas'),
                        'defaultvalue' => self::$default_config[self::CAS_CERTIFICATECHECK],
                        'help' => true,
                    ),

                    self::CAS_CERTIFICATEPATH => array(
                        'type' => 'text',
                        'title' => get_string(self::CAS_CERTIFICATEPATH, 'auth.cas'),
                        'defaultvalue' => self::$default_config[self::CAS_CERTIFICATEPATH],
                        'help' => true,
                    ),
                ),
            ),
            'fsLDAP' => array(
                'type' => 'fieldset',
                'legend' => get_string('ldapsettings', 'auth.cas'),
                'collapsible' => true,
                'collapsed' => true,
                'elements' => $parent['elements'],
            ),
        );
        //put the CAS values at the top 
        //$parent['elements']=array_merge($elements,$parent['elements']);
        // pp_error_log("retour",$elements);
        //return $parent;
        return array(
            'elements' => $elements,
            'renderer' => 'table'
        );

    }


    public static function save_config_options($values, $form)
    {


        // let parent take care of the LDAP settings and of creating the authinstance if needed
        $values = parent::save_config_options($values, $form);

        //at this stage the instance does exist        
        $current = get_records_assoc('auth_instance_config', 'instance', $values['instance'], '', 'field, value');

        if (empty($current)) {
            $current = array();
        }

        self::$default_config = array(
            self::CAS_HOSTNAME => $values[self::CAS_HOSTNAME],
            self::CAS_PORT => $values[self::CAS_PORT],
            self::CAS_VERSION => $values[self::CAS_VERSION],
            self::CAS_BASEURI => $values[self::CAS_BASEURI],
            self::CAS_LANGUAGE => $values[self::CAS_LANGUAGE],
            self::CAS_PROXY => $values[self::CAS_PROXY],
            self::CAS_LOGOUT => $values[self::CAS_LOGOUT],
            self::CAS_CERTIFICATECHECK => $values[self::CAS_CERTIFICATECHECK],
            self::CAS_CERTIFICATEPATH => $values[self::CAS_CERTIFICATEPATH],
        );

        foreach (self::$default_config as $field => $value) {
            $record = new stdClass();
            $record->instance = $values['instance'];
            $record->field = $field;
            $record->value = $value;

            if ($values['create'] || !array_key_exists($field, $current)) {
                insert_record('auth_instance_config', $record);
            }
            else {
                update_record('auth_instance_config', $record, array('instance' => $values['instance'], 'field' => $field));
            }
        }

        return $values;
    }


}


