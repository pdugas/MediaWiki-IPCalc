MediaWiki-IPCalc
================

MediaWik Extension for IP Address Calculations

MediaWiki-IPCalc is an IP address calculator MediaWiki extension intended to
help with documentation of IP networks.  Articles documenting subnets can
include the `<ipcalc/>` tag to embed a table that presents the address, netmask,
broadcast address, first and last host, etc.  The tag requires two attributes
as shown in the example below.

> &lt;ipcalc ip="10.10.0.0" cidr="24"/&gt;

The `ip` argument must be a valid dotted-quad IP address.  The `cidr` argument
is the CIDR prefix length.

The plugin also creates the `IPCalc` namespace in the wiki so you can embed
links to dynamically generated pages.

> [[IPCalc:10.10.0.0/24]]

This link leads to a page that consists simply of the corresponding `<ipcalc/>`
tag.

### INSTALLATION

No configuration other than including the extension is required.  Add a line
like the one below to the bottom of your `LocalSettings.php` file.

> require_once("$IP/extensions/IPCalc/IPCalc.php");

### TODO LIST
* Make the namespace number configurable.
* Add support for netmask attribute (and suffix in article name) instead of CIDR prefix length.
