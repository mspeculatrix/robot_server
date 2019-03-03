<?php
/**
	db_library.php

	*** NB: PRETTY MUCH ALL OF THIS PROBABLY NEED REWRITING TO
	WORK WITH THE NEW WAYS OF USING MYSQL, INCL MYSQLI
	AND OBJECT-BASED METHODS.

**/


if ( !defined ( '__INCLUDED_DB_LIBRARY__' ) ) :

define ( '__INCLUDED_DB_LIBRARY__', TRUE );

function dbQuery($table, $fields='*', $condition=FALSE,
                        $order=FALSE, $limit=FALSE, $offset=FALSE, $link=FALSE) {
        $query_result = array ( 'result'=> FALSE,
                                'rows'  => FALSE);
        if(!$link) { $link = DB_LINK; }
        $clause = '';
        if ( $condition ) $clause .= 'WHERE '.$condition.' ';
        if ( $order ) $clause .= 'ORDER BY '.$order.' ';
        $limit_clause = '';
        if ( $offset ) { $limit_clause = $offset.','; }
        if ( $limit ) { $limit_clause .= $limit; }
        if ( $limit_clause ) $clause .= 'LIMIT '.$limit_clause.' ';
        $dbquery = 'SELECT '.$fields.' FROM '.$table.' '.$clause;
        // for debugging only //echo "<p>*** dbquery: $dbquery ***</p>".NL;
        $query_result['result'] = mysql_query($dbquery,$link) or db_error();
        $query_result['rows'] = mysql_num_rows($query_result['result']);
        return $query_result;
}

function db_lookup($table, $search_field, $search_value, $data_field, $link=FALSE) {
        /*      Lookup a value from the named table.
                $search_field should be a field containing unique values -
                usually some kind of ID field. We'll search this for the value
                $search_value and return the value found in the corresponding
                $data_field. If not found, we'll return FALSE. */
        if(!$link) { $link = DB_LINK; }
        $condition = "$search_field='$search_value'";
        $dbquery = db_query($table, $data_field, $condition, FALSE, FALSE, FALSE, $link);
        if($dbquery['rows'] == 1) {
                $dbinfo = mysql_fetch_assoc($dbquery['result']);
                $dbdata = $dbinfo[$data_field];
        } else { $dbdata = FALSE; }
        return $dbdata;
}

function update_record($table, $data, $ref, $id_field, $link=FALSE ) {
        /* Updates a record using a record ID. The $data param should contain
                data in a comma list in the form: field='data' - eg;*/
        if(!$link) { $link = DB_LINK; }
        $dbquery = "UPDATE $table SET $data WHERE $id_field='$ref'";
        $dbresult = mysql_query($dbquery, $link) or db_error ( $dbquery );
        $result = mysql_affected_rows ($link);
        return $result;
}

function delete_record($table, $ref, $id_field='id', $link=FALSE ) {
        if(!$link) { $link = DB_LINK; }
        $rec = array ( $id_field => $ref );
        $result = write_data ( $table, $link, $rec, $id_field, 'delete' );
        if ( $result == 1 ) {
                return 'Record deleted. ';
        } else {
                return 'Problem deleting record. '.$result.' rows affected. ';
        }
}

function write_data ( $db, $data=FALSE, $id_field='id', $action='new', $link=FALSE ) {
        /*      Saves or updates records using the following:
                $db =   name of database table
                $link = db link
                $data = hash containing data - the key names must match
                                the column names for the database and not
                                contain any extra values
                                For delete - $data contains id of record to be
                                deleted.
                $unique_id = the field that contains a unique value to
                                use to identify the relevant record for updates
                                or deletes
                $action =       "new", "update" or "delete"
                RETURNS number of affected rows for updates & deletions
                or new id for inserts */
        if(!$link) { $link = DB_LINK; }
        $result = FALSE;
        SWITCH ( $action ) {
                CASE 'new':
                        /*      see if the unique_id field is also in the $data array and
                                if so, remove it */
                        if ( array_key_exists ( $id_field, $data ) ) {
                                unset ( $data[$id_field] );
                        }
                        $dbquery = "INSERT INTO $db ($id_field,";
                        $dbquery .= implode(",",array_keys($data));
                        $dbquery .= ") VALUES (NULL,'";
                        // $dbquery .= implode("','",array_values($data));
                        $dbquery .= implode("','",array_values($data));
                        $dbquery .= "')";
                        break;
                CASE 'update':
                        $id_value = $data[$id_field];
                        unset($data[$id_field]);
                        $dbquery = "UPDATE $db SET ";
                        $update = array();
                        foreach ($data as $key => $value) {
                                array_push($update,"$key='".$value."'");
                        }
                        $dbquery .= implode(",",$update);
                        $dbquery .= " WHERE $id_field=".$id_value;
                        break;
                CASE 'delete':
                        $dbquery = "DELETE FROM $db WHERE $id_field=".$data[$id_field];
                        break;
        }       // end of switch
        // for debugging only
        //              echo "$dbquery<br>";
        $dbresult = mysql_query($dbquery,$link) or db_error($dbquery);
        if($action == 'new') {
                $result = mysql_insert_id($link);
        } else {
                $result = mysql_affected_rows($link);
        }
        return $result;
}

class DBtable
{
/** 	This class needs to be passed a DB link and a table name -- see constructor
**/

	/// *** MEMBERS ***
	public $tableName;
	public $idField = '';
	public $lastQuery = array ( 'rows' => 0,
								'result' 	=> FALSE );
	protected $dbLink;

	/// *** CONSTRUCTOR ***
	//	Giving the __construct() method paraemters is how you pass parameters
	//	when creating an instance of  an object
	function __construct ( $table, $link = FALSE ) {
		//parent::__construct(); // only needed for sub-classes

		// set database link
		if ( !$link ) {
			if ( defined ( 'DB_LINK' ) ) {
				$this->dbLink = DB_LINK;
			} else {
				throw new Exception ( 'No database link supplied.' );
			}
		} else {
			$this->dbLink = $link;
		}
		$this->tableName = $table;
	}

	/// *** DESTRUCTOR ***
	function __destruct() {
		// nothing here yet
	}


	/**	\name		dbQuery
		\returns	associative array with keys: result, rows
	**/
	public function dbQuery (
					$condition		= FALSE,
					$returnFields	= '*',
					$order			= FALSE,
					$limit			= FALSE,
					$offset			= FALSE ) {

		$query_result = array (	'result'	=> FALSE,
								'rows' 		=> FALSE );
		$clause = '';
		if ( $condition ) $clause .= 'WHERE '.$condition.' ';
		if ( $order ) $clause .= 'ORDER BY '.$order.' ';
		$limit_clause = '';
		if ( $offset ) { $limit_clause = $offset.','; }
		if ( $limit ) { $limit_clause .= $limit; }
		if ( $limit_clause ) $clause .= 'LIMIT '.$limit_clause.' ';
		$dbquery = 'SELECT '.$returnFields.' FROM '.$this->tableName.' '.$clause;
		//println ( 'dbquery: '.$dbquery ); // for debugging only
		$query_result = $this->performDbQuery ( $dbquery, 'select' );
		return $query_result;
	}


	public function performDbQuery ( $dbQueryString, $queryType = 'select' ) {
		// performs a db query and returns an associative array with keys: result, rows
		$this->lastQuery['rows'] = 0;
		$this->lastQuery['result'] = FALSE;
		$this->lastQuery['insertID'] = FALSE;
		$queryResult = array ( 'errors' => FALSE );
		if ( $dbQueryString ):
			$queryResult['result'] = mysql_query ( $dbQueryString, $this->dbLink )
				or $this->dbError ( 'Unable to make database query: '.mysql_error() );
			SWITCH ( $queryType ) :
				CASE 'new':
					$queryResult['insertID'] = mysql_insert_id ( $this->dbLink );
					$this->lastQuery['insertID'] = $queryResult['insertID'];
					break;
				CASE 'update':
					$queryResult['rows'] = mysql_affected_rows ( $this->dbLink );
					break;
				CASE 'select':
				DEFAULT:
					$queryResult['rows'] = mysql_num_rows ( $queryResult['result'] );
					break;
			endswitch;
			$this->lastQuery['rows'] = $queryResult['rows'];
			$this->lastQuery['result'] = $queryResult['result'];
		endif;
		return $queryResult;
	}


	private function dbError ( $message = FALSE ) {
		if ( !$message ) $message = 'unspecified';
		echo '<p class="warning">Database error: '.$message.'</p>'.NL;
	}


	public function fieldArray () {
	/*	Returns an array containing all the keys - ie, field names -
		in the chosen database table */
		$key_array = array();
		$dbResult = $this->dbQuery ( FALSE, '*', FALSE, 1 )
			or $this->dbError( 'Unable to query for field list' );
		if ( $dbResult['rows'] > 0 ) {
			$key_array = array_keys ( mysql_fetch_assoc ( $dbResult['result'] ) );
		}
		return $key_array;
	}


	public function uniqueRecord ( $idField, $searchValue, $returnFields = '*' ) {
		$recordArray = array (	'record' => array(),
								'error' => FALSE );
		$query_result = $this->dbQuery ( $idField.'="'.$searchValue.'"', $returnFields );
		if ( $query_result['rows'] === 1 ) {
			$recordArray['record'] = mysql_fetch_assoc ( $query_result['result'] );
		} elseif ( $query_result['rows'] == 0 ) {
			$recordArray['error'] = 'No record found';
		}
		return $recordArray;
	}


	/**	Wrapper to uniqueRecord, returns the value from the $returnField or
	 *	FALSE if not found
	**/
	public function uniqueValue ( $idField, $searchValue, $returnField ) {
		$fieldValue = FALSE;
		$recordArray = $this->uniqueRecord ( $idField, $searchValue, $returnField );
		if ( !$recordArray['error'] && $recordArray['record'][$returnField]) {
			$fieldValue = $recordArray['record'][$returnField];
		}
		return $fieldValue;
	}

	public function writeData ( $data, $action = 'new', $idField = 'id' ) {
		// for inserting, updating & deleting records
		// For 'new' and 'edit' modes, $data must be a hash with keys
		// matching the DB table fieldnames.
		// For deletion, $data must be the idField value.
		$result = array( 'id' => FALSE, 'rows' => FALSE,
						'info' => FALSE, 'result' => FALSE );
		SWITCH ( $action ) {
			CASE 'new':
			/*	see if the unique_id field is also in the $data array and
				if so, remove it */
			if ( array_key_exists ( $idField, $data ) ) {
				unset ( $data[$idField] );
			}
			$dbquery = 'INSERT INTO '.$this->tableName.' ('.$idField.',';
			$dbquery .= implode ( ',', array_keys ( $data ) );
			$dbquery .= ') VALUES (NULL, \'';
			// $dbquery .= implode("','",array_values($data));
			$dbquery .= implode ( "', '", array_values ( $data ) );
			$dbquery .= '\')';
			break;
		CASE 'edit':
			$id_value = $data[$idField];
			unset ( $data[$idField] );
			$dbquery = 'UPDATE '.$this->tableName.' SET ';
			$update = array();
			foreach ( $data as $key => $value ) {
				array_push ( $update, $key."='".$value."'" );
			}
			$dbquery .= implode ( ', ', $update );
			$dbquery .= ' WHERE '.$idField.'=\''.$id_value.'\'';
			break;
		CASE 'delete':
			$dbquery = 'DELETE FROM '.$this->tableName
				.' WHERE '.$idField.'='.$data;
			break;
		}	// switch
		// for debugging only
				//echo NL.'<p>'.$dbquery.'</p>'.NL;
				//echo '[link: '.$this->dbLink.']';
		$dbresult = mysql_query ( $dbquery, $this->dbLink );
		if ( $action == 'new' ) {
			$result['id'] = mysql_insert_id ( $this->dbLink );
			$result['result'] = $result['id'];
		} else {
			$result['rows'] = mysql_affected_rows ( $this->dbLink );
			$result['result'] = $result['rows'];
		}
		$result['info'] = mysql_info($this->dbLink);
		return $result;
	}

}

/**	\name	DBRecord
 *	\param	an array output by MySQLtable's methods such as uniqueRecord, which
 *			consists of an array containing the record's data (with key 'record')
 *			and a possible scalar with an error message (key 'error')
**/
class DBRecord
{
	protected $fields = array();
	protected $dbLink;

	/// *** CONSTRUCTOR ***
	function __construct ( $fieldArray = FALSE, $link = FALSE ) {
		// when creating a new instance, one should always pass
		// a DB link. It's made sort-of optional here for backward
		// compatibility with what's been written so far. But that may change
		if ( $fieldArray ) {
			$this->fields = $fieldArray['record'];
		} else {
			$this->fields = array();
		}
		if ( $link ) {
			$this->dbLink = $link;
		} elseif ( defined ( 'DB_LINK' ) ) {
			$this->dbLink = DB_LINK;
		}
	}

	/// *** DESTRUCTOR ***
	function __destruct() {
	}


	public function setField ( $fieldName, $fieldValue ) {
		$this->fields[$fieldName] = $fieldValue;
	}

	public function metaLabelArray ( $fieldName, $metaTable,
									$idValSeparator = ';',
									$metaIdField = 'id',
									$metaDataField = 'name' ) {
		// designed to work with fields in the record that store lists of
		// IDs that relate to separate tables of metadata - eg, the field will
		// contain something like 3;7;9 - which are the id values. These meta
		// tables typical have a field called 'id' with the unique ID value
		// and another called 'name' with the data. But we've provided params
		// to this function so that it will work with differently constructed
		// tables. It's anticipated that localRecord classes will include
		// wrappers to this function, to simplify it.
		// This function returns an array of the values matching the IDs.
		$labelArray = array();
		if ( $this->field($fieldName) ) :
			$idArray = explode ( $idValSeparator, $this->field($fieldName) );
			foreach ( $idArray as $id ) {
				$dbq = 'SELECT '.$metaIdField.','.$metaDataField
					.' FROM '.$metaTable.' WHERE '.$metaIdField.'=\''.$id.'\'';
				$dbr = mysql_query ( $dbq, $this->dbLink );
				if ( mysql_numrows ( $dbr) == 1 ) {
					$dbdata = mysql_fetch_assoc ( $dbr );
					array_push ( $labelArray, $dbdata[$metaDataField] );
				}
			}
		endif;
		return $labelArray;
	}

	public function metaLabelList ( $fieldName, $metaTable,
									$listSeparator = ' : ',
									$idValSeparator = ';',
									$metaIdField = 'id',
									$metaDataField = 'name' ) {
		// wrapper to metaLabelArray()
		// returns a string rather than array
		$labelList = '';
		$labelArray = $this->metaLabelArray ( $fieldName, $metaTable,
			$idValSeparator, $metaIdField, $metaDataField );
		if ( $labelArray ) {
			$labelList = implode ( $listSeparator, $labelArray );
		}
		return $labelList;
	}

	/** *** ACCESSOR METHODS *** **/

	public function field ( $fieldName ) {
		$return = FALSE;
		if ( $fieldName == 'all' || $fieldName == 'array' ) {
			$return = $this->fields;
		} elseif ( array_key_exists ( $fieldName, $this->fields ) ) {
			$return = $this->fields[$fieldName];
		}
		return $return;
	}

	public function debugDisplay () {
		echo '<table class="debugTable">'.NL;
		foreach ( $this->fields as $key => $val ):
			echo '<tr><th>'.$key.'</th><td>'.$val.'</td></tr>'.NL;
		endforeach;
		echo '</table>'.NL;
	}

	public function metaTypeIDs ( $fieldName, $listDelimiter = ';' ) {
		// returns an array with the ID numbers from meta fields
		// (typically ones containing lists of ID numbers separated
		// by semi-colons where the IDs relate to entries in a
		// separate table)
		$metaTypeIDs = array();
		if ( $this->field ( $fieldName ) ) {
			$metaTypeIDs = explode ( $listDelimiter, $this->field ( $fieldName ) );
		}
		return $metaTypeIDs;
	}

}

endif;

?>
