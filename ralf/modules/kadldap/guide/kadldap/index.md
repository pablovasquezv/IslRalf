# About Kadldap

This module provides an Auth driver for Active Directory & LDAP authentication,
based on the [adLDAP](http://adldap.sourceforge.net) library.
To use it, simply copy the config file from `MODPATH/config/kadldap.php` to
`APPPATH/config/kadldap.php` and edit the variables therein.

You can run a live test of the current configuration by attempting to log in
with the [test login form](../../kadldap).  This will verify that your configuration
is correct, and also enables you to see what information is available from the
classes in this module.

Please report any bugs or feature requests through
[Github](http://github.com/samwilson/kohana_kadldap).
