<?php

class LdapServer {

public $hostname;
    public $port;
    public $version;
    public $link;
    public $basedn;
    public $bind_dn;
    private $bind_password;
    public $search_attr;
    public $filter_attr;
    public $group_dn;
    public $group_attr;


	/**
	 * Set values from the provided settings
	 *
	 * @param object $settings
	 */
	public function __construct($settings) {
        $fields = array(
            'hostname',
            'port',
            'version',
            'basedn',
            'bind_dn',
            'bind_password',
            'search_attr',
            'filter_attr',
            'group_dn',
            'group_attr',
        );
        
        foreach ($fields as $field) {
            if (empty($settings->$field)) {
                if ($field!='group_dn' && $field!='group_attr') {
                    $message = "LDAP: missing settings value for $field.";
                    elgg_log($message, 'ERROR');
                    throw new Exception($message);
                }
            }
            $this->$field = $settings->$field;
        }
        
        return $this;
    }


	/**
	 * Bind to an LDAP directory
	 *
	 * @param string bind_rdn
	 * @param string bind_password
	 * @return bool True on success or false on failure
	 */
	public function getLink() {
		if (!$this->link) {
			$this->link = ldap_connect($this->hostname, $this->port);

			if (!$this->link) {
				elgg_log("Unable to connect to LDAP server at {$this->hostname}", 'ERROR');
			}

			ldap_set_option($this->link, LDAP_OPT_PROTOCOL_VERSION, $this->version);
		}

		return $this->link;
	}

	/**
	 * Bind to an LDAP directory
	 *
	 * @param string $bind_dn
	 * @param string $password
	 * @return boolean $ldab_bind
	 */
	public function bind($bind_dn = null, $password = null) {
		if (!$bind_dn && !$password) {
			$bind_dn = $this->bind_dn;
			$password = $this->bind_password;
		}

		$ldab_bind = ldap_bind($this->getLink(), $bind_dn, $password);

		if ($ldab_bind) {
			elgg_log("Successfully bind to the LDAP directory with $bind_dn", 'NOTICE');
		} else {
			elgg_log("Failed to bind to the LDAP directory with $bind_dn", 'NOTICE');
		}

		return $ldab_bind;
	}

	/**
	 * Search LDAP tree for a specified filter
	 *
	 * @param string $filter The search filter
	 * @return array $data   Associative array of the user data
	 */
	public function search($filter) {
		elgg_log("Performing an LDAP query with filter $filter", 'NOTICE');

		$search_attributes = $this->getSearchAttr();

		$query = ldap_search($this->getLink(), $this->basedn, $filter, array_values($search_attributes));

		if (!$query) {
			return false;
		}

		$result = ldap_get_entries($this->getLink(), $query);

		if (empty($result['count'])) {
			elgg_log("LDAP search for filter \"$filter\" returned no results.", 'NOTICE');

			return false;
		}

		// Map the values to the keys provided in plugin settings
		foreach (array_keys($search_attributes) as $attr) {
			$data[$attr] = $result[0][$search_attributes[$attr]][0];
		}

		$data['dn'] = $result[0]['dn'];

		return $data;
	}

	/**
	 * Unbinds from the LDAP directory
	 */
	public function __destruct() {
		if ($this->link) {
			ldap_close($this->link);
		}
	}

	/**
	 * Get array of search attributes defined in plugin settings
	 *
	 * @return array Associative array ('name' => 'ldap_attribute')
	 */
	private function getSearchAttr() {
		$search_attr = array(
			'dn' => 'dn',
			$this->filter_attr => $this->filter_attr,
		);

		if (!empty($this->search_attr)) {
			$pairs = array_map('trim', explode(',', $this->search_attr));

			foreach ($pairs as $pair) {
				$parts = array_map('trim', explode(':', $pair));

				$search_attr[$parts[0]] = strtolower($parts[1]);
			}
		}

		return $search_attr;
	}
	
	/**
    * Check if a user is a member of a group
    *
    * @param string $userdn The user dn to check membership
    * @return boolean
    */
    public function isMember($userdn) {
        if (empty($this->group_dn)) {
            //No group in settings so exit this function
            //elgg_log("No LDAP group in settings", 'NOTICE');
            return true;
        }

        if (empty($this->group_attr)) {
            // There is a group in settings but no parameter. Assuming member
            $this->group_attr="member";
        }

        $filter="($this->group_attr=$userdn)";

        $search_attributes = $this->getSearchAttr();

        $query = ldap_search($this->getLink(), $this->group_dn, $filter, array_values($search_attributes));

        if (!$query) {
            //elgg_log("Query in isMember function is null");
            return false;
        }

        $result = ldap_get_entries($this->getLink(), $query);

        if (empty($result['count'])) {
            //elgg_log("LDAP search for filter \"$filter\" on \"$this->group_dn\" returned no results.", 'NOTICE');
            return false;
        } else {
            $resultdn=$result[0]["dn"];
			//a member entry was found on the group for this userdn
            //elgg_log("LDAP search for filter \"$filter\" on \"$this->group_dn\" returned $resultdn", 'NOTICE');
        }

        return true;
    }
}
