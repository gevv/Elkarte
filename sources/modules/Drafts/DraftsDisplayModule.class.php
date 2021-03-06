<?php

/**
 * Integration system for drafts into Post controller
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
 * @version 1.1 dev
 *
 */

if (!defined('ELK'))
	die('No access...');

class Drafts_Display_Module
{
	protected static $_autosave_enabled = false;
	protected static $_autosave_frequency = 30000;

	public static function hooks()
	{
		global $modSettings;

		if (!empty($modSettings['drafts_enabled']) && !empty($modSettings['drafts_post_enabled']))
		{
			self::$_autosave_enabled = !empty($modSettings['drafts_autosave_enabled']);

			if (!empty($modSettings['drafts_autosave_frequency']))
				self::$_autosave_frequency = (int) $modSettings['drafts_autosave_frequency'] * 1000;

			return array(
				array('prepare_context', array('Drafts_Display_Module', 'prepare_context'), array('use_quick_reply', 'editorOptions', 'board')),
			);
		}
		else
			return array();
	}

	public function prepare_context($use_quick_reply, &$editorOptions, $board)
	{
		global $context, $options;

		// Check if the draft functions are enabled and that they have permission to use them (for quick reply.)
		$context['drafts_save'] = $use_quick_reply && allowedTo('post_draft') && $context['can_reply'];
		$context['drafts_autosave'] = $context['drafts_save'] && self::$_autosave_enabled && allowedTo('post_autosave_draft') && !empty($options['drafts_autosave_enabled']);

		// Build a list of drafts that they can load into the editor
		if (!empty($context['drafts_save']))
		{
			loadLanguage('Drafts');
			if ($context['drafts_autosave'])
			{
				// WYSIWYG editor
				if (!empty($options['use_editor_quick_reply']))
				{
					if (!isset($editorOptions['plugin_addons']))
						$editorOptions['plugin_addons'] = array();
					if (!isset($editorOptions['plugin_options']))
						$editorOptions['plugin_options'] = array();

					// @todo remove
					$context['drafts_autosave_frequency'] = self::$_autosave_frequency;

					$editorOptions['plugin_addons'][] = 'draft';
					$editorOptions['plugin_options'][] = '
						draftOptions: {
							sLastNote: \'draft_lastautosave\',
							sSceditorID: \'' . $editorOptions['id'] . '\',
							sType: \'post\',
							iBoard: ' . $board . ',
							iFreq: ' . self::$_autosave_frequency . ',
							sLastID: \'id_draft\',
							sTextareaID: \'' . $editorOptions['id'] . '\',
							id_draft: ' . (empty($context['id_draft']) ? 0 : $context['id_draft']) . '
						}';

					loadJavascriptFile('drafts.plugin.js', array('defer' => true));
				}
				// Plain text area
				else
				{
					loadJavascriptFile('drafts.js');
					addInlineJavascript('
				var oDraftAutoSave = new elk_DraftAutoSave({
					sSelf: \'oDraftAutoSave\',
					sLastNote: \'draft_lastautosave\',
					sTextareaID: \'message\',
					sLastID: \'id_draft\',
					iBoard: ' . $board . ',
					iFreq: ' . self::$_autosave_frequency . '
				});', true);
				}
			}
		}
	}
}