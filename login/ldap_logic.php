<?php

function user_authorization($username, $password){
  $ds=ldap_connect(LDAPCONFIG['host'], LDAPCONFIG['port']);
  ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
  ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

  $bind=ldap_bind($ds, $username .'@' .DOMAIN, $password);

  if ($bind) {
      return true;
  } else {
      return false;
  }
}

?>
