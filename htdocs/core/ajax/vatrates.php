<?php
/* Copyright (C) 2012 Regis Houssin  <regis.houssin@inodbox.com>
 * Copyright (C) 2024       Frédéric France         <frederic.france@free.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *       \file       htdocs/core/ajax/vatrates.php
 *       \brief      File to load vat rates combobox according to thirdparty ID. Values are returned in JSON format.
 */

if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', '1'); // Disables token renewal
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', '1');
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1');
}

// Load Dolibarr environment
require '../../main.inc.php';
/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Societe $mysoc
 * @var Translate $langs
 * @var User $user
 */

$id = GETPOSTINT('id');
$action = GETPOST('action', 'aZ09');	// 'getSellerVATRates' or 'getBuyerVATRates'
$htmlname	= GETPOST('htmlname', 'alpha');
$selected	= (GETPOST('selected') ? GETPOST('selected') : '-1');
$productid = (GETPOSTINT('productid') ? GETPOSTINT('productid') : 0);

// Security check
$result = restrictedArea($user, 'societe', $id, '&societe', '', 'fk_soc', 'rowid', 0);


/*
 * Actions
 */

// None


/*
 * View
 */

top_httphead('application/json');

//print '<!-- Ajax page called with url '.dol_escape_htmltag($_SERVER["PHP_SELF"]).'?'.dol_escape_htmltag($_SERVER["QUERY_STRING"]).' -->'."\n";

// Load original field value
if (!empty($id) && !empty($action) && !empty($htmlname)) {
	$form = new Form($db);
	$soc = new Societe($db);

	$soc->fetch($id);

	if ($action == 'getSellerVATRates') {	// action = 'getSellerVATRates'. Test on permission not required here, already done in the restrictArea()
		$seller = $mysoc;
		$buyer = $soc;
	} else {	// action = 'getBuyerVATRates' or 'getVatRates'. Test on permission not required here, already done in the restrictArea()
		$buyer = $mysoc;
		$seller = $soc;
	}

	$return = array();
	$return['value']	= $form->load_tva('tva_tx', $selected, $seller, $buyer, $productid, 0, '', true);
	$return['num'] = $form->num;
	$return['error']	= $form->error;

	echo json_encode($return);
}
