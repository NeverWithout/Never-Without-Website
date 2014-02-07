<?php

/**
* Data Transaction / MySQL Plugin
* A MySQL plugin to the (data_trans) class
* @package		phpOpenFW
* @subpackage 	Database_Tools
* @author 		Christian J. Clark
* @copyright	Copyright (c) Christian J. Clark
* @license		http://www.gnu.org/licenses/gpl-2.0.txt
* @access		private
* @version 		Started: 2-1-2007 updated: 3-21-2012
*/

//***************************************************************
/**
 * dt_mysql Class
 * @package		phpOpenFW
 * @subpackage	Database_Tools
 * @access		private
 */
//***************************************************************
class dt_mysql extends dt_structure
{

    /**
	* Opens a connection to the specified data source based on the data source type
	**/
	//*************************************************************************
	// Make a connection to the given source and store the handle
	//*************************************************************************
	public function open()
	{
		if (!$this->handle) {
			$host = $this->server;
			if (!empty($this->port)) { $host .= ':' . $this->port; }
			if ($this->persistent) { $this->handle = @mysql_pconnect($host, $this->user, $this->pass); }
	        else { $this->handle = @mysql_connect($host, $this->user, $this->pass); }
		}

        if (!$this->handle || mysql_errno()) {
            $this->connection_error(mysql_error());
            $this->handle = false;
            return false;
        }
        else {
			// Select Database
        	@mysql_select_db($this->source, $this->handle);

			if (mysql_errno()) {
				$this->gen_error(mysql_error());
				mysql_close($this->handle);
				$this->handle = false;
				return false;
			}

			// Keep track of the number of connections we create
			$this->increment_counters();

		}

		// Flag Connection as Open       
        $this->conn_open = true;

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
		$this->conn_open = false;
		if (!$this->reuse_connection) {
			if ($this->handle) { return mysql_close($this->handle); }
		}
		return true;
	}

	/**
	* Executes a query based on the data source type
	* @param mixed MySQL: SQL Statement
	**/
	//*************************************************************************
	// Execute a query and store the record set
	//*************************************************************************
	public function query($query)
	{
		// Check for Open Connection
		if (!$this->is_open()) { return false; }
		$this->curr_query = $query;

        $this->rsrc_id = mysql_query($query);
        if ($this->rsrc_id && gettype($this->rsrc_id) != 'boolean') {
            $this->num_rows = mysql_num_rows($this->rsrc_id);
            while($row=mysql_fetch_assoc($this->rsrc_id)){
                array_push($this->result, $row);
            }
        }

        // Last Insert ID
        $this->last_id = mysql_insert_id();

        // MySQL Error Reporting
        if ($mysql_error=mysql_error()) {
        	$this->gen_error($mysql_error);
        	return false;
        }
        return true;
	}

}

?>
