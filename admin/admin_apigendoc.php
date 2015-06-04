<?php
/* REST API to call generate dolibarr document output method (PDF,ODT,...)
 * Copyright (C) 2015  Florian HENRY  <florian.henry@open-concept.pro>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		admin/apigendoc.php
 * 	\ingroup	apigendoc
 */
// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
    $res = @include("../../../main.inc.php"); // From "custom" directory
}


// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/apigendoc.lib.php';

// Translations
$langs->load("apigendoc@apigendoc");

// Access control
if (! $user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');

/*
 * Actions
 */

$actionsave=GETPOST("save");

// Sauvegardes parametres
if ($actionsave)
{
	$error=0;
	$db->begin();
	
	$const_name='APIGENDOC_API_KEY';
	$const_val=GETPOST($const_name);
	if (! empty($const_val)) {
		$res = dolibarr_set_const($db, $const_name, $const_val, 'chaine', 0, '', 0);
	} else {
		$res = dolibarr_set_const($db, $const_name, '', 'chaine', 0, '', 0);
	}
	if (! $res > 0)
		$error ++;
	
	if (!$conf->use_javascript_ajax) {
		$const_name='APIGENDOC_DEBUGMODE';
		$const_val=GETPOST($const_name);
		if (! empty($const_val)) {
			$res = dolibarr_set_const($db, $const_name, $const_val, 'chaine', 0, '', 0);
		} else {
			$res = dolibarr_set_const($db, $const_name, '', 'chaine', 0, '', 0);
		}
		if (! $res > 0)
			$error ++;
	}

	if (empty($error))
	{
		$db->commit();
		setEventMessage($langs->trans("SetupSaved"));
	}
	else
	{
		$db->rollback();
		setEventMessage($langs->trans("Error"), 'errors');
	}
}

/*
 * View
 */
$page_name = "ApiGendocSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
    . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = apigendocAdminPrepareHead();
dol_fiche_head(
    $head,
    'settings',
    $langs->trans("Module103548Name"),
    0,
    "apigendoc@apigendoc"
);

$form=new Form($db);

// Setup page goes here
echo $langs->trans("ApiGendocSetupPage");

print '<form name="apigendocsetuppage" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<table class="noborder" width="100%">';

print '<tr class="liste_titre">';
print "<td>".$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
//print "<td>".$langs->trans("Examples")."</td>";
print "<td>&nbsp;</td>";
print "</tr>";

print '<tr class="impair">';
print '<td class="fieldrequired">'.$langs->trans("ApiGendocApiKey").'</td>';
print '<td><input type="text" class="flat" id="APIGENDOC_API_KEY" name="APIGENDOC_API_KEY" value="'. (GETPOST('APIGENDOC_API_KEY')?GETPOST('APIGENDOC_API_KEY'):(! empty($conf->global->APIGENDOC_API_KEY)?$conf->global->APIGENDOC_API_KEY:'')) . '" size="40">';
if (! empty($conf->use_javascript_ajax))
	print '&nbsp;'.img_picto($langs->trans('Generate'), 'refresh', 'id="generate_token" class="linkobject"');
print '</td>';
print '<td>&nbsp;</td>';
print '</tr>';

print '<tr class="pair">';
print '<td>' . $langs->trans("ApiGendocDebugMode") . '</td>';
print '<td align="left">';
if ($conf->use_javascript_ajax) {
	print ajax_constantonoff('APIGENDOC_DEBUGMODE',array(),0);
} else {
	$arrval = array (
			'0' => $langs->trans("No"),
			'1' => $langs->trans("Yes")
	);
	print $form->selectarray("APIGENDOC_DEBUGMODE", $arrval, $conf->global->APIGENDOC_DEBUGMODE);
}
print '</td>';
print '</tr>';


print '</table>';

print '<br><center>';
print '<input type="submit" name="save" class="button" value="'.$langs->trans("Save").'">';
print '</center>';

if (! empty($conf->use_javascript_ajax))
{
	print "\n".'<script type="text/javascript">';
	print '$(document).ready(function () {
            $("#generate_token").click(function() {
            	$.get( "'.DOL_URL_ROOT.'/core/ajax/security.php", {
            		action: \'getrandompassword\',
            		generic: true
				},
				function(token) {
					$("#APIGENDOC_API_KEY").val(token);
				});
            });
    });';
	print '</script>';
}

// Page end
dol_fiche_end();
llxFooter();
$db->close();