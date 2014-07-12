<?php
# =============================================================================
# IPCalc - MediaWiki Extension for IP Calculations
# Copyright (C) 2014 Dugas Enterprises, LLC.
# =============================================================================
# @file     includes/IPCalcHook.php
# @brief    Hooks for the IPCalc extension
# @author   Paul Dugas <paul@dugasenterprises.com>
# @version  $Id: IPCalcHooks.php 73 2014-03-18 12:29:03Z paul.dugas.serco $
# =============================================================================

class IPCalcHooks
{

  public static function onFirstCallInit(Parser $parser)
    {
      $parser->setHook('ipcalc', 'IPCalcHooks::onTag');
      $parser->setFunctionHook('ipcalc', 'IPCalcHooks::onFunc');
      return true;
    }

  public static function onArticleFromTitle($title, &$article) 
    {
      if ($title->getNamespace() == NS_IPCALC) 
        { $article = new IPCalcPage($title); }
      return true;
    }

  public static function onFunc(Parser $parser, 
                                $ip = '', $cidr = '', $caption = '')
    {
      if (empty($ip)) { error_log('missing addr'); return '(missing addr)'; }
 
      if (preg_match('/^(.*)\/(.*)$/', $ip, $m)) { 
        $caption = $cidr;
        $cidr = $m[2];
        $ip = $m[1];
      }

      if (preg_match('/^(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.'.
                       '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.'.
                       '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.'.
                       '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)$/', $ip, $m)) {
        $ip = sprintf('%d.%d.%d.%d', $m[1], $m[2], $m[3], $m[4]);
      } else { error_log("invalid addr; $ip"); return '(invalid addr)'; }
      $ip_bin = IPCalcHooks::ip2bin($ip);

      if     (substr($ip_bin, 0, 1) ==    '0') { $class = 'A'; }
      elseif (substr($ip_bin, 0, 2) ==   '10') { $class = 'B'; }
      elseif (substr($ip_bin, 0, 3) ==  '110') { $class = 'C'; }
      elseif (substr($ip_bin, 0, 4) == '1110') { $class = 'D, Multicast'; }
      else                                     { $class = 'E, Experimental'; }

      if (empty($cidr)) {
        switch ($class) {
            case 'A': $cidr =  8; break;
            case 'B': $cidr = 16; break;
            case 'C': $cidr = 24; break;
            case 'D': $cidr = 32; break;
            case 'E': $cidr = 32; break;
            default:  $cidr = 32; break;
        }
      }

      if (preg_match('/^(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.'.
                       '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.'.
                       '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.'.
                       '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)$/', $cidr, $m)) {
        $mask = sprintf('%d.%d.%d.%d', $m[1], $m[2], $m[3], $m[4]);
        $mask = IPCalcHooks::ip2bin($mask);
        if (preg_match('/^(1*)0*$/', $mask, $m)) { $cidr = strlen($m[1]); }
        else { error_log("invalid mask; $cidr"); return '(invalid mask)'; }
      }

      if ($cidr < 0 || $cidr > 32) 
        { error_log("invalid CIDR; $cidr"); return "(invalid CIDR)"; }
      
      $nm_bin = str_pad(str_pad('', $cidr, '1'), 32, '0');
      $nm = IPCalcHooks::bin2ip($nm_bin);
      
      $wc_bin = rtrim($nm_bin, '0');
      $wc_bin = str_pad(str_replace('1','0',$wc_bin), 32, '1');
      $wc = IPCalcHooks::bin2ip($wc_bin);

      $net_bin = str_pad(substr($ip_bin, 0, $cidr), 32, '0');
      $net = IPCalcHooks::bin2ip($net_bin);

      $bcast_bin = str_pad(substr($ip_bin, 0, $cidr), 32, '1');
      $bcast = IPCalcHooks::bin2ip($bcast_bin);

      $first_bin = str_pad(substr($net_bin, 0, 31), 32, '1');
      $first = IPCalcHooks::bin2ip($first_bin);

      $last_bin = str_pad(substr($bcast_bin, 0, 31), 32, '0');
      $last = IPCalcHooks::bin2ip($last_bin);

      $total = bindec(str_pad('', (32-$cidr), '1')) - 1;

      $special = '';
      if (substr($ip_bin, 0,  8) == '00001010' ||
          substr($ip_bin, 0, 12) == '101011000001' ||
          substr($ip_bin, 0, 16) == '1100000010101000)') {
        $special='[http://www.ietf.org/rfc/rfc1918.txt '.
                 'RFC-1918 Private Address]';
      }

      if (empty($caption)) {
        $caption = '';
      } else {
        $caption = "|+ $caption\n";
      }

      return "{|class=\"wikitable\"\n".$caption.
             "!Address\n".
             "|<tt>$ip</tt>\n".
             "|<tt>".IPCalcHooks::bin2str($ip_bin, $cidr)."</tt>\n".
             "|-\n".
             "!Netmask\n".
             "|<tt>$nm</tt> (<tt>$cidr</tt>)\n".
             "|<tt>".IPCalcHooks::bin2str($nm_bin, $cidr)."</tt>\n".
             "|-\n".
             "!Wildcard\n".
             "|<tt>$wc</tt>\n".
             "|<tt>".IPCalcHooks::bin2str($wc_bin, $cidr)."</tt>\n".
             "|-\n".
             "!Network\n".
             "|<tt>$net</tt>\n".
             "|<tt>".IPCalcHooks::bin2str($net_bin, $cidr).
             " (Class $class)</tt>\n".
             "|-\n".
             "!Broadcast\n".
             "|<tt>$bcast</tt>\n".
             "|<tt>".IPCalcHooks::bin2str($bcast_bin, $cidr)."</tt>\n".
             "|-\n".
             "!First&nbsp;Host\n".
             "|<tt>$first</tt>\n".
             "|<tt>".IPCalcHooks::bin2str($first_bin, $cidr)."</tt>\n".
             "|-\n".
             "!Last&nbsp;Host\n".
             "|<tt>$last</tt>\n".
             "|<tt>".IPCalcHooks::bin2str($last_bin, $cidr)."</tt>\n".
             "|-\n".
             "!Hosts/Net\n".
             "|<tt>$total</tt>\n".
             "|<tt>$special</tt>\n".
             "|}";
    }

  public static function onTag($input, array $args, 
                               Parser $parser, PPFrame $frame)
    {
      $addr = $args['addr']; 
          if (empty($addr)) { $addr = $args['ip']; }
      $mask = $args['mask'];
          if (empty($mask)) { $mask = $args['cidr']; }
          if (empty($mask)) { $mask = $args['net']; }
          if (empty($mask)) { $mask = $args['subnet']; }
      $caption = $input;
          if (empty($caption)) { $caption = $args['caption']; }
          if (empty($caption)) { $caption = $args['title']; }
      return $parser->recursiveTagParse(self::onFunc($parser, $addr, 
                                                     $mask, $caption));
    }

  public static function onLinkBegin($dummy, $target, &$html, &$customAttribs,
                                     &$query, &$options, &$ret) 
    {
      if ($target->getNamespace() == NS_IPCALC) {
        if (preg_match('/^(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.'.
                         '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.'.
                         '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.'.
                         '(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\/'.
                         '(3[0-2]|[1-2]?[0-9])$/', 
                         $target->getText())) {
          $options[] = 'known';
          $options[] = 'noclasses';
        }
      }
      return true;
    }

  private static function ip2bin($ip)
    {
      $octets = explode('.', $ip);
      foreach ($octets as $key => $val) {
        $octets[$key] = str_pad(decbin($octets[$key]), 8, "0", STR_PAD_LEFT);
      }
      return implode('', $octets);
    }

  private static function bin2ip($bin)
    {
      $octets = str_split($bin, 8);
      foreach ($octets as $key => $val) {
        $octets[$key] = bindec($octets[$key]);
      }
      return implode('.', $octets);
    }

  private static function bin2str($bin, $cidr)
    {
      $str = rtrim(chunk_split($bin,8,"."),".");
      $offset = floor($cidr/8) + $cidr;
      return substr($str, 0, $offset).'&nbsp;'.substr($str, $offset);
    }

} // class IPCalcHooks

# =============================================================================
# $LastChangedDate: 2014-03-18 08:29:03 -0400 (Tue, 18 Mar 2014) $
# $LastChangedBy: paul.dugas.serco $
# $URL: https://svn.sercovdot.com/repos/imms/trunk/var/www/wiki/extensions/IPCalc/includes/IPCalcHooks.php $
# $Revision: 73 $
# vim: set et sw=4 ts=4 :
