<?php
# =============================================================================
# IPCalc - MediaWiki Extension for IP Address Calculations
# =============================================================================
# @file     IPCalc.i18n.php
# @brief    Internationalization for the extension
# @author   Paul Dugas <paul@dugasenterprises.com>
# @version  $Id: IPCalc.i18n.php 73 2014-03-18 12:29:03Z paul.dugas.serco $
# =============================================================================

$messages = array();

$messages['en'] = array(
	'ipcalc'      => 'IPCalc',
	'ipcalc-desc' => 'Dynamically generates articles for IP subnets.',
);

$messages['qqq'] = array(
	'ipcalc' => '{{doc-special|IPCalc}}
IPCalc is a module that generates wiki articles for IP subnets.',
	'ipcalc-desc' => '{{desc}}',
);

$magicWords = array();

$magicWords['en'] = array(
        'ipcalc' => array( 0, 'ipcalc' ),
);

# =============================================================================
# $LastChangedDate: 2014-03-18 08:29:03 -0400 (Tue, 18 Mar 2014) $
# $LastChangedBy: paul.dugas.serco $
# $URL: https://svn.sercovdot.com/repos/imms/trunk/var/www/wiki/extensions/IPCalc/IPCalc.i18n.php $
# $Revision: 73 $
# vim: set et sw=4 ts=4 :
