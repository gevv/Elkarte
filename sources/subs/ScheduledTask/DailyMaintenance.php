<?php

/**
 * This class does daily cleaning up.
 *
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.1 dev Release Candidate 2
 *
 */

namespace ElkArte\sources\subs\ScheduledTask;

if (!defined('ELK'))
	die('No access...');

/**
 * This function does daily cleaning up:
 *
 * - decrements warning levels if it's enabled
 * - consolidate spider statistics
 * - fix MySQL version
 * - regenerate Diffie-Hellman keys for OpenID
 * - remove obsolete login history logs
 *
 * @package ScheduledTasks
 */
class Daily_Maintenance implements Scheduled_Task_Interface
{
	public function run()
	{
		global $modSettings;

		$db = database();

		// First clean out the cache.
		clean_cache('data');

		// If warning decrement is enabled and we have people who have not had a new warning in 24 hours, lower their warning level.
		list (, , $modSettings['warning_decrement']) = explode(',', $modSettings['warning_settings']);
		if ($modSettings['warning_decrement'])
		{
			// Find every member who has a warning level...
			$request = $db->query('', '
				SELECT id_member, warning
				FROM {db_prefix}members
				WHERE warning > {int:no_warning}',
				array(
					'no_warning' => 0,
				)
			);
			$members = array();
			while ($row = $db->fetch_assoc($request))
				$members[$row['id_member']] = $row['warning'];
			$db->free_result($request);

			// Have some members to check?
			if (!empty($members))
			{
				// Find out when they were last warned.
				$request = $db->query('', '
					SELECT id_recipient, MAX(log_time) AS last_warning
					FROM {db_prefix}log_comments
					WHERE id_recipient IN ({array_int:member_list})
						AND comment_type = {string:warning}
					GROUP BY id_recipient',
					array(
						'member_list' => array_keys($members),
						'warning' => 'warning',
					)
				);
				$member_changes = array();
				while ($row = $db->fetch_assoc($request))
				{
					// More than 24 hours ago?
					if ($row['last_warning'] <= time() - 86400)
						$member_changes[] = array(
							'id' => $row['id_recipient'],
							'warning' => $members[$row['id_recipient']] >= $modSettings['warning_decrement'] ? $members[$row['id_recipient']] - $modSettings['warning_decrement'] : 0,
						);
				}
				$db->free_result($request);

				// Have some members to change?
				if (!empty($member_changes))
				{
					require_once(SUBSDIR . '/Members.subs.php');
					foreach ($member_changes as $change)
						updateMemberData($change['id'], array('warning' => $change['warning']));
				}
			}
		}

		// Do any spider stuff.
		if (!empty($modSettings['spider_mode']) && $modSettings['spider_mode'] > 1)
		{
			// We'll need this.
			require_once(SUBSDIR . '/SearchEngines.subs.php');
			consolidateSpiderStats();
		}

		// Check the database version - for some buggy MySQL version.
		$server_version = $db->db_server_info();
		if (DB_TYPE === 'MySQL' && in_array(substr($server_version, 0, 6), array('5.0.50', '5.0.51')))
			updateSettings(array('db_mysql_group_by_fix' => '1'));
		elseif (!empty($modSettings['db_mysql_group_by_fix']))
			removeSettings('db_mysql_group_by_fix');

		// Regenerate the Diffie-Hellman keys if OpenID is enabled.
		if (!empty($modSettings['enableOpenID']))
		{
			require_once(SUBSDIR . '/OpenID.subs.php');
			$openID = new OpenID();
			$openID->setup_DH(true);
		}
		elseif (!empty($modSettings['dh_keys']))
			removeSettings('dh_keys');

		// Clean up some old login history information.
		$db->query('', '
			DELETE FROM {db_prefix}member_logins
			WHERE time > {int:oldLogins}',
			array(
				'oldLogins' => !empty($modSettings['loginHistoryDays']) ? 60 * 60 * $modSettings['loginHistoryDays'] : 108000,
		));

		// Log we've done it...
		return true;
	}
}