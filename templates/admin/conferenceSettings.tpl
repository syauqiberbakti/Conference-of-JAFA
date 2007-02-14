{**
 * conferenceSettings.tpl
 *
 * Copyright (c) 2003-2005 The Public Knowledge Project
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Basic conference settings under site administration.
 *
 * $Id$
 *}

{assign var="pageTitle" value="admin.conferences.conferenceSettings"}
{include file="common/header.tpl"}

<br />

<form method="post" action="{url op="updateConference"}">
{if $conferenceId}
<input type="hidden" name="conferenceId" value="{$conferenceId}" />
{/if}

{include file="common/formErrors.tpl"}

{if not $conferenceId}
<p><span class="instruct">{translate key="admin.conferences.createInstructions"}</span></p>
{/if}

<table class="data" width="100%">
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="title" key="manager.setup.conferenceTitle" required="true"}</td>
		<td width="80%" class="value"><input type="text" id="title" name="title" value="{$title|escape}" size="40" maxlength="120" class="textField" /></td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="description" key="admin.conferences.conferenceDescription"}</td>
		<td class="value">
			<textarea name="description" id="description" cols="40" rows="10" class="textArea">{$description|escape}</textarea>
			<br />
			<span class="instruct">{translate key="admin.conferences.conferenceDescriptionInstructions" sampleUrl=$sampleUrl}</span>
		</td>
	</tr>
	<tr valign="top">
		<td class="label">{fieldLabel name="title" key="common.path" required="true"}</td>
		<td class="value">
			<input type="text" id="path" name="path" value="{$path|escape}" size="16" maxlength="32" class="textField" />
			<br />
			{translate|assign:"sampleEllipsis" key="common.ellipsis"}
			{url|assign:"sampleUrl" conference="path" schedConf="$sampleEllipsis"}
			<span class="instruct">{translate key="admin.conferences.urlWillBe" sampleUrl=$sampleUrl}</span>
		</td>
	</tr>
	<tr valign="top">
		<td colspan="2" class="label">
			<input type="checkbox" name="enabled" id="enabled" value="1"{if $enabled} checked="checked"{/if} /> <label for="enabled">{translate key="admin.conferences.enableConferenceInstructions"}</label>
		</td>
	</tr>
</table>

<p><input type="submit" value="{translate key="common.save"}" class="button defaultButton" /> <input type="button" value="{translate key="common.cancel"}" class="button" onclick="document.location.href='{url op="conferences" escape=false}'" /></p>

</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>

{include file="common/footer.tpl"}
