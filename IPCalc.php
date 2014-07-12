<?php
# =============================================================================
# IPCalc - MediaWiki Extension for IP Calculations
# Copyright (C) 2014 Dugas Enterprises, LLC.
# =============================================================================
# @file     IPCalc.php
# @brief    Setup for the extension
# @author   Paul Dugas <paul@dugasenterprises.com>
# =============================================================================

define("NS_IPCALC", 486);
define("NS_IPCALC_TALK", NS_IPCALC+1);
$wgExtraNamespaces[NS_IPCALC] = "IPCalc";
$wgExtraNamespaces[NS_IPCALC_TALK] = "IPCalc_talk";
$wgNamespaceProtection[NS_IPCALC] = array('editipcalc');
$wgNamespaceProtection[NS_IPCALC_TALK] = array('editipcalc');
//$wgGroupPermissions['sysop']['editipcalc'] = true;

$wgExtensionCredits['other'][] = array(
    'path'           => __FILE__,
    'name'           => 'IPCalc',
    'author'         => array('[mailto:paul@dugas.cc Paul Dugas]'),
    'version'        => '0.1.0',
    'url'            => 'https://github.com/pdugas/MediaWiki-IPCalc',
    'descriptionmsg' => 'ipcalc-desc',
);

$dir = dirname(__FILE__);
$inc = $dir.'/includes';

$wgAutoloadClasses['IPCalcHooks'] = $inc.'/IPCalcHooks.php';
$wgAutoloadClasses['IPCalcPage'] = $inc.'/IPCalcPage.php';

$wgExtensionMessagesFiles['IPCalc'] = $dir.'/IPCalc.i18n.php';

$wgHooks['ArticleFromTitle'][] = 'IPCalcHooks::onArticleFromTitle';
$wgHooks['ParserFirstCallInit'][] = 'IPCalcHooks::onFirstCallInit';
$wgHooks['LinkBegin'][] = 'IPCalcHooks::onLinkBegin';

# =============================================================================
# vim: set et sw=4 ts=4 :
