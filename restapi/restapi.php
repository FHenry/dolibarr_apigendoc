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

// Load Slim API
require '../vendor/autoload.php';

$res = @include ("../../master.inc.php"); // From htdocs directory
if (! $res) {
	$res = @include ("../../../master.inc.php"); // From "custom" directory
}
if (! $res)
	die("Include of main fails");

$error = 0;

define('EVEN_IF_ONLY_LOGIN_ALLOWED', 0);

/*
 * Sorry I didn't use middleware ===> All route are define here code is inside...
 */

$app = new \Slim\Slim();

if (! empty($conf->global->APIGENDOC_DEBUGMODE)) {
	$app->config('debug', true);
} else {
	$app->config('debug', false);
}

// Create PDF Doc
$app->get('/gendoc/:typedoc/:iddoc', function ($typedoc,$iddoc) use($app, $conf, $db) {
	
	if ($app->request->get('key') != $conf->global->APIGENDOC_API_KEY) {
		$app->halt('403', 'BAD API Key');
	}
	
	//Load admin user
	$user = new User($db);
	$user->fetch(1);
	
	if ($typedoc=='facture') {
		require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
		$object = new Facture($db);
		$typedoc='FACTURE_ADDON_PDF';
	}
	
	
	$object->fetch($iddoc);
	$object->fetch_thirdparty();
	
	// Save last template used to generate document
	if (empty($object->model_pdf)) {
		$object->setDocModel($user,$conf->global->$typedoc);
	}
	
	// Define output language
	$outputlangs = $langs;
	$newlang = '';
	if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang = $object->thirdparty->default_lang;
	if (! empty($newlang))
	{
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang($newlang);
	}
	
	$hidedetails=0;
	$hidedesc=0;
	$hideref=0;
	$result = $object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
	if ($result <= 0)
	{
		if ($app->config('debug') === true) {
			$msg = 'ERROR ' . get_class($object) . '->generateDocument: object->error:' . $object->error. ' $result='.$result. ' dblasterror='.$db->lasterror;
		} else {
			$msg = 'ERROR';
		}
		$app->halt('500', $msg);
	}
	
	
	$app->response->headers->set('Content-Type', 'application/json');
	
	$object->db = null;
	$app->response->setBody(json_encode(array('status'=>'OK')));
});

$app->run();