<?php

/**
* Data Transaction / LDAP Plugin
* A LDAP plugin to the (data_trans) class
* @package		phpOpenFW
* @subpackage 	Database_Tools
* @author 		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @access		private
* @version 		Started: 2-1-2007 updated: 2-21-2012
*/

//***************************************************************
/**
 * dt_ldap Class
 * @package		phpOpenFW
 * @subpackage	Database_Tools
 * @access		private
 */
//***************************************************************
class dt_ldap extends dt_structure
{
    
    /**
	* Opens a connection to the specified data source based on the data source type
	**/
	//*************************************************************************
	// Make a connection to the given source and store the handle
	//*************************************************************************
	public function open()
	{
		if (!$this->port) { $this->port = 389; }
        $this->handle = @ldap_connect($this->server, $this->port);
        if (!$this->handle){
            trigger_error("$this->disp_dt Connection Error: [ $this->server:$this->port ].");
            return false;
        }
        else {
            if (!ldap_set_option($this->handle, LDAP_OPT_PROTOCOL_VERSION, 3)){
                trigger_error("$this->disp_dt Failed to set LDAP protocol version to 3.");
                return false
            }
	  	}

		// Keep track of the number of connections we create
        if (!isset($GLOBALS['ldap_conns'])) { $GLOBALS['ldap_conns'] = 0; }
		$GLOBALS['ldap_conns']++;

		return true;
	}
	
	/**
	* Closes a connection to the specified data source based on the data source type
	**/
	//*************************************************************************
	// Close the connection to the data source
	//*************************************************************************
    public function close()
	{
	   if ($this->handle) { ldap_close($this->handle); }
	   $this->conn_open = false;
	}
	
	/**
	* Executes a query based on the data source type
	* @param mixed LDAP: properly formatted and filled array
	* @param string "anon" - Anonymous Bind, "user" - User Bind, "admin" - Admin Bind
	**/
	//*************************************************************************
	// Execute a query and store the record set
	//*************************************************************************
	public function query($query)
	{
        if (!is_array($query)) { $this->rsrc_id = false; }
        else {
			$this->curr_query = $query;

            switch ($this->trans_type) {
                case 'qry':
                case 'qry1':
                    $search_dn = $query[0] . $this->source;
                    $ldapFilter = (isset($query[1])) ? ($query[1]) : ('*');
                    $selectAttrs = (!isset($query[2]) || !is_array($query[2])) ? (array('*')) : ($query[2]);
                    if ($this->trans_type == 'qry1') {
                        $this->rsrc_id = @ldap_list($this->handle, $search_dn, $ldapFilter, $selectAttrs);
                    }
                    else {
                        $this->rsrc_id = @ldap_search($this->handle, $search_dn, $ldapFilter, $selectAttrs);
                    }
							
                    if (isset($query['ldapSortAttributes'])) {
                        foreach ($query['ldapSortAttributes'] as $eachSortAttribute) {
                            ldap_sort($this->handle, $this->rsrc_id, $eachSortAttribute);
                        }
                    }
							
                    if ($this->rsrc_id) {
                        $this->num_rows = ldap_count_entries($this->handle, $this->rsrc_id);
                        $this->result = ldap_get_entries($this->handle, $this->rsrc_id);
                    }
                    else { $this->result = false; }
                    break;
							
                case 'add':
                    $this->rsrc_id = @ldap_add($this->handle, $query['dn'], $query['values']);
                    break;

                case 'mod':
                    $this->rsrc_id = @ldap_modify($this->handle, $query['dn'], $query['values']);
                    break;

                case 'del':							
                    $this->rsrc_id = @ldap_delete($this->handle, $query['dn']);
                    break;
            }
					
            // LDAP Error Reporting
            // ??
        }
	}

	return $this->rsrc_id;
}

?>
