<?php

/**
 * @name      Dialogo Forum
 * @copyright Dialogo Forum contributors
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2012 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 1.0 Alpha
 */

function template_email_members()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=news;sa=mailingcompose" method="post" id="admin_newsletters" class="flow_hidden" accept-charset="', $context['character_set'], '">
			<div class="cat_bar">
				<h3 class="catbg">', $txt['admin_newsletters'], '</h3>
			</div>
			<div class="information">
				', $txt['admin_news_select_recipients'], '
			</div>
			<div class="windowbg">
				<div class="content">
					<dl class="settings">
						<dt>
							<strong>', $txt['admin_news_select_group'], ':</strong><br />
							<span class="smalltext">', $txt['admin_news_select_group_desc'], '</span>
						</dt>
						<dd>';

	foreach ($context['groups'] as $group)
				echo '
							<label for="groups_', $group['id'], '"><input type="checkbox" name="groups[', $group['id'], ']" id="groups_', $group['id'], '" value="', $group['id'], '" checked="checked" class="input_check" /> ', $group['name'], '</label> <em>(', $group['member_count'], ')</em><br />';

	echo '
							<br />
							<label for="checkAllGroups"><input type="checkbox" id="checkAllGroups" checked="checked" onclick="invertAll(this, this.form, \'groups\');" class="input_check" /> <em>', $txt['check_all'], '</em></label>';

	echo '
						</dd>
					</dl>
					<br class="clear" />
				</div>
			</div>
			<br />

			<div id="advanced_panel_header" class="cat_bar">
				<h3 class="catbg">
					<img id="advanced_panel_toggle" class="panel_toggle" style="display: none;" src="', $settings['images_url'], '/', empty($context['show_advanced_options']) ? 'collapse' : 'expand', '.png" alt="*" />
					<a href="#" id="advanced_panel_link" >', $txt['advanced'], '</a>
				</h3>
			</div>

			<div id="advanced_panel_div" class="windowbg2">
				<div class="content">
					<dl class="settings">
						<dt>
							<strong>', $txt['admin_news_select_email'], ':</strong><br />
							<span class="smalltext">', $txt['admin_news_select_email_desc'], '</span>
						</dt>
						<dd>
							<textarea name="emails" rows="5" cols="30" style="' . (isBrowser('is_ie8') ? 'width: 635px; max-width: 98%; min-width: 98%' : 'width: 98%') . ';"></textarea>
						</dd>
						<dt>
							<strong>', $txt['admin_news_select_members'], ':</strong><br />
							<span class="smalltext">', $txt['admin_news_select_members_desc'], '</span>
						</dt>
						<dd>
							<input type="text" name="members" id="members" value="" size="30" class="input_text" />
							<span id="members_container"></span>
						</dd>
					</dl>
					<hr class="bordercolor" />
					<dl class="settings">
						<dt>
							<strong>', $txt['admin_news_select_excluded_groups'], ':</strong><br />
							<span class="smalltext">', $txt['admin_news_select_excluded_groups_desc'], '</span>
						</dt>
						<dd>';

	foreach ($context['groups'] as $group)
				echo '
							<label for="exclude_groups_', $group['id'], '"><input type="checkbox" name="exclude_groups[', $group['id'], ']" id="exclude_groups_', $group['id'], '" value="', $group['id'], '" class="input_check" /> ', $group['name'], '</label> <em>(', $group['member_count'], ')</em><br />';

	echo '
							<br />
							<label for="checkAllGroupsExclude"><input type="checkbox" id="checkAllGroupsExclude" onclick="invertAll(this, this.form, \'exclude_groups\');" class="input_check" /> <em>', $txt['check_all'], '</em></label><br />
						</dd>
						<dt>
							<strong>', $txt['admin_news_select_excluded_members'], ':</strong><br />
							<span class="smalltext">', $txt['admin_news_select_excluded_members_desc'], '</span>
						</dt>
						<dd>
							<input type="text" name="exclude_members" id="exclude_members" value="" size="30" class="input_text" />
							<span id="exclude_members_container"></span>
						</dd>
					</dl>
					<hr class="bordercolor" />
					<dl class="settings">
						<dt>
							<label for="email_force"><strong>', $txt['admin_news_select_override_notify'], ':</strong></label><br />
							<span class="smalltext">', $txt['email_force'], '</span>
						</dt>
						<dd>
							<input type="checkbox" name="email_force" id="email_force" value="1" class="input_check" />
						</dd>
					</dl>
				</div>
			</div>
			<div class="righttext">
				<input type="submit" value="', $txt['admin_next'], '" class="button_submit" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			</div>
		</form>
	</div>';

	// This is some javascript for the simple/advanced toggling and member suggest
	echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		var oAdvancedPanelToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($context['show_advanced_options']) ? 'true' : 'false', ',
			aSwappableContainers: [
				\'advanced_panel_div\'
			],
			aSwapImages: [
				{
					sId: \'advanced_panel_toggle\',
					srcExpanded: smf_images_url + \'/collapse.png\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: smf_images_url + \'/expand.png\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			aSwapLinks: [
				{
					sId: \'advanced_panel_link\',
					msgExpanded: ', JavaScriptEscape($txt['advanced']), ',
					msgCollapsed: ', JavaScriptEscape($txt['advanced']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'admin_preferences\',
				sSessionVar: smf_session_var,
				sSessionId: smf_session_id,
				sThemeId: \'1\'
			}
		});
	// ]]></script>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/suggest.js?alp21"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var oMemberSuggest = new smc_AutoSuggest({
			sSelf: \'oMemberSuggest\',
			sSessionId: smf_session_id,
			sSessionVar: smf_session_var,
			sSuggestId: \'members\',
			sControlId: \'members\',
			sSearchType: \'member\',
			bItemList: true,
			sPostName: \'member_list\',
			sURLMask: \'action=profile;u=%item_id%\',
			sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
			sItemListContainerId: \'members_container\',
			aListItems: []
		});
		var oExcludeMemberSuggest = new smc_AutoSuggest({
			sSelf: \'oExcludeMemberSuggest\',
			sSessionId: \'', $context['session_id'], '\',
			sSessionVar: \'', $context['session_var'], '\',
			sSuggestId: \'exclude_members\',
			sControlId: \'exclude_members\',
			sSearchType: \'member\',
			bItemList: true,
			sPostName: \'exclude_member_list\',
			sURLMask: \'action=profile;u=%item_id%\',
			sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
			sItemListContainerId: \'exclude_members_container\',
			aListItems: []
		});
	// ]]></script>';
}

function template_email_members_compose()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
		<div id="preview_section"', isset($context['preview_message']) ? '' : ' style="display: none;"', '>
			<div class="cat_bar">
				<h3 class="catbg">
					<span id="preview_subject">', empty($context['preview_subject']) ? '' : $context['preview_subject'], '</span>
				</h3>
			</div>
			<div class="windowbg">
				<div class="content">
					<div class="post" id="preview_body">
						', empty($context['preview_message']) ? '<br />' : $context['preview_message'], '
					</div>
				</div>
			</div>
		</div><br />';

	echo '
	<div id="admincenter">
		<form name="newsmodify" action="', $scripturl, '?action=admin;area=news;sa=mailingsend" method="post" accept-charset="', $context['character_set'], '">
			<div class="cat_bar">
				<h3 class="catbg">
					<a href="', $scripturl, '?action=helpadmin;help=email_members" onclick="return reqOverlayDiv(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics_hd.png" alt="', $txt['help'], '" class="icon" /></a> ', $txt['admin_newsletters'], '
				</h3>
			</div>
			<div class="information">
				', $txt['email_variables'], '
			</div>
			<div class="windowbg">
				<div class="content">
				<div class="', empty($context['error_type']) || $context['error_type'] != 'serious' ? 'noticebox' : 'errorbox', '"', empty($context['post_error']['messages']) ? ' style="display: none"' : '', ' id="errors">
					<dl>
						<dt>
							<strong id="error_serious">', $txt['error_while_submitting'] , '</strong>
						</dt>
						<dd class="error" id="error_list">
							', empty($context['post_error']['messages']) ? '' : implode('<br />', $context['post_error']['messages']), '
						</dd>
					</dl>
				</div>
				<dl id="post_header">
					<dt class="clear_left">
						<span', (isset($context['post_error']['no_subject']) ? ' class="error"' : ''), ' id="caption_subject">', $txt['subject'], ':</span>
					</dt>
					<dd id="pm_subject">
						<input type="text" name="subject" value="', $context['subject'], '" tabindex="', $context['tabindex']++, '" size="60" maxlength="60"',isset($context['post_error']['no_subject']) ? ' class="error"' : ' class="input_text"', '/>
					</dd>
				</dl><hr class="clear" />
				<div id="bbcBox_message"></div>';

	// What about smileys?
	if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
		echo '
				<div id="smileyBox_message"></div>';

	// Show BBC buttons, smileys and textbox.
	echo '
				', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');

					echo '
				<ul class="reset">
					<li><label for="send_pm"><input type="checkbox" name="send_pm" id="send_pm" ', !empty($context['send_pm']) ? 'checked="checked"' : '', 'class="input_check" onclick="checkboxes_status(this);" /> ', $txt['email_as_pms'], '</label></li>
					<li><label for="send_html"><input type="checkbox" name="send_html" id="send_html" ', !empty($context['send_html']) ? 'checked="checked"' : '', 'class="input_check" onclick="checkboxes_status(this);" /> ', $txt['email_as_html'], '</label></li>
					<li><label for="parse_html"><input type="checkbox" name="parse_html" id="parse_html" checked="checked" disabled="disabled" class="input_check" /> ', $txt['email_parsed_html'], '</label></li>
				</ul>
				<br class="clear_right" />
				<span id="post_confirm_buttons">
					', template_control_richedit_buttons($context['post_box_name']), '
				</span>
				</div>
			</div>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			<input type="hidden" name="email_force" value="', $context['email_force'], '" />
			<input type="hidden" name="total_emails" value="', $context['total_emails'], '" />
			<input type="hidden" name="max_id_member" value="', $context['max_id_member'], '" />';

	foreach ($context['recipients'] as $key => $values)
		echo '
			<input type="hidden" name="', $key, '" value="', implode(($key == 'emails' ? ';' : ','), $values), '" />';

	// The functions used to preview a posts without loading a new page.
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			var post_box_name = "', $context['post_box_name'], '";
			var form_name = "newsmodify";
			var preview_area = "news";
			var txt_preview_title = "', $txt['preview_title'], '";
			var txt_preview_fetch = "', $txt['preview_fetch'], '";

			function checkboxes_status (item)
			{
				if (item.id == \'send_html\')
					document.getElementById(\'parse_html\').disabled = !document.getElementById(\'parse_html\').disabled;
				if (item.id == \'send_pm\')
				{
					if (!document.getElementById(\'send_html\').checked)
						document.getElementById(\'parse_html\').disabled = true;
					else
						document.getElementById(\'parse_html\').disabled = false;
					document.getElementById(\'send_html\').disabled = !document.getElementById(\'send_html\').disabled;
				}
			}
		// ]]></script>
		</form>
	</div>';
}

function template_email_members_send()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=news;sa=mailingsend" method="post" accept-charset="', $context['character_set'], '" name="autoSubmit" id="autoSubmit">
			<div class="cat_bar">
				<h3 class="catbg">
					<a href="', $scripturl, '?action=helpadmin;help=email_members" onclick="return reqOverlayDiv(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics_hd.png" alt="', $txt['help'], '" /></a> ', $txt['admin_newsletters'], '
				</h3>
			</div>
			<div class="windowbg">
				<div class="content">
					<div class="progress_bar">
						<div class="full_bar">', $context['percentage_done'], '% ', $txt['email_done'], '</div>
						<div class="green_percent" style="width: ', $context['percentage_done'], '%;">&nbsp;</div>
					</div>
					<hr class="hrcolor" />
					<input type="submit" name="cont" value="', $txt['email_continue'], '" class="button_submit" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="hidden" name="subject" value="', $context['subject'], '" />
					<input type="hidden" name="message" value="', $context['message'], '" />
					<input type="hidden" name="start" value="', $context['start'], '" />
					<input type="hidden" name="total_emails" value="', $context['total_emails'], '" />
					<input type="hidden" name="max_id_member" value="', $context['max_id_member'], '" />
					<input type="hidden" name="send_pm" value="', $context['send_pm'], '" />
					<input type="hidden" name="send_html" value="', $context['send_html'], '" />
					<input type="hidden" name="parse_html" value="', $context['parse_html'], '" />';

	// All the things we must remember!
	foreach ($context['recipients'] as $key => $values)
		echo '
					<input type="hidden" name="', $key, '" value="', implode(($key == 'emails' ? ';' : ','), $values), '" />';

	echo '
				</div>
			</div>
		</form>
	</div>

	<script type="text/javascript"><!-- // --><![CDATA[
		var countdown = 2;
		var message = "', $txt['email_continue'], '";
		doAutoSubmit();
	// ]]></script>';
}
