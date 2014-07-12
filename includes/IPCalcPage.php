<?php
# =============================================================================
# IPCalc - MediaWiki Extension for IP Calculations
# Copyright (C) 2014 Dugas Enterprises, LLC.
# =============================================================================
# @file     includes/IPCalcPage.php
# @brief    Article/Page class for the IPCalc extension
# @author   Paul Dugas <paul@dugasenterprises.com>
# @version  $Id: IPCalcPage.php 65 2014-03-08 21:44:35Z paul.dugas.serco $
# =============================================================================

class IPCalcPage extends Article
{
  function __construct($title) 
    { parent::__construct($title); }

  function view()
    {
      global $wgOut, $wgUser, $wgIPCalcLiveStatus;

      $title = $this->getTitle()->getPartialURL();
      $wgOut->setHTMLTitle($title);
      $wgOut->setPageTitle($title);
      $wgOut->addMeta("refresh", "90");
      $wgOut->enableClientCache(false);
      
      if (preg_match('/^((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.)'.
                       '((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.)'.
                       '((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.)'.
                       '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)'.
                       '\/(3[0-2]|[1-2]?[0-9])$/', $title, $matches)) {
        $wgOut->addWikiText(sprintf('<ipcalc ip="%d.%d.%d.%d" cidr="%d"/>',
                                    $matches[2], $matches[4], $matches[6],
                                    $matches[7], $matches[8]));
      } else {
        $wgOut->addWikiText('Invalid IP/CIDR; "'.$title.'"');
      }
    }

} // class IPCalcPage

# =============================================================================
# $LastChangedDate: 2014-03-08 16:44:35 -0500 (Sat, 08 Mar 2014) $
# $LastChangedBy: paul.dugas.serco $
# $URL: https://svn.sercovdot.com/repos/imms/trunk/var/www/wiki/extensions/IPCalc/includes/IPCalcPage.php $
# $Revision: 65 $
# vim: set et sw=4 ts=4 :
