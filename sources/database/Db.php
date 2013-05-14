<?php

/**
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * @version 1.0 Alpha
 *
 * This class is the base class for database drivers implementations.
 *
 */

if (!defined('ELKARTE'))
	die('No access...');

interface Database
{
	/**
	 * Fix up the prefix so it doesn't require the database to be selected.
	 *
	 * @param string &db_prefix
	 * @param string $db_name
	 */
	function fix_prefix(&$db_prefix, $db_name);

	/**
	 * Callback for preg_replace_callback on the query.
	 * It allows to replace on the fly a few pre-defined strings, for convenience ('query_see_board', 'query_wanna_see_board'), with
	 * their current values from $user_info.
	 * In addition, it performs checks and sanitization on the values sent to the database.
	 *
	 * @param $matches
	 */
	function replacement__callback($matches);

	/**
	 * Just like the db_query, escape and quote a string, but not executing the query.
	 *
	 * @param string $db_string
	 * @param array $db_values
	 * @param resource $connection = null
	 */
	function quote($db_string, $db_values, $connection = null);

	/**
	 * Do a query.  Takes care of errors too.
	 *
	 * @param string $identifier
	 * @param string $db_string
	 * @param array $db_values = array()
	 * @param resource $connection = null
	 */
	function query($identifier, $db_string, $db_values = array(), $connection = null);

	/**
	 * Fetch next result as association.
	 *
	 * @param resource $request
	 * @param mixed counter = false
	 */
	function fetch_assoc($request, $counter = false);

	/**
	 * Fetch a row from the resultset given as parameter.
	 *
	 * @param resource $result
	 */
	function fetch_row($result);

	/**
	 * affected_rows
	 * @param resource $connection
	 */
	function affected_rows($connection = null);

	/**
	 * insert_id
	 *
	 * @param string $table
	 * @param string $field = null
	 * @param resource $connection = null
	 */
	function insert_id($table, $field = null, $connection = null);

	/**
	 * Do a transaction.
	 *
	 * @param string $type - the step to perform (i.e. 'begin', 'commit', 'rollback')
	 * @param resource $connection = null
	 */
	function db_transaction($type = 'commit', $connection = null);

	/**
	 * Database error!
	 * Backtrace, log, try to fix.
	 *
	 * @param string $db_string
	 * @param resource $connection = null
	 */
	function error($db_string, $connection = null);

	/**
	 * insert
	 *
	 * @param string $method - options 'replace', 'ignore', 'insert'
	 * @param $table
	 * @param $columns
	 * @param $data
	 * @param $keys
	 * @param bool $disable_trans = false
	 * @param resource $connection = null
	 */
	function insert($method = 'replace', $table, $columns, $data, $keys, $disable_trans = false, $connection = null);

	/**
	 * This function tries to work out additional error information from a back trace.
	 *
	 * @param $error_message
	 * @param $log_message
	 * @param $error_type
	 * @param $file
	 * @param $line
	 */
	function error_backtrace($error_message, $log_message = '', $error_type = false, $file = null, $line = null);

	/**
	 * Escape the LIKE wildcards so that they match the character and not the wildcard.
	 *
	 * @param $string
	 * @param bool $translate_human_wildcards = false, if true, turns human readable wildcards into SQL wildcards.
	 */
	function escape_wildcard_string($string, $translate_human_wildcards=false);

	/**
	 * Returns whether the database system supports ignore.
	 *
	 * @return bool
	 */
	function support_ignore();

	/**
	 * Get the name (title) of the database system.
	 */
	function db_title();
}