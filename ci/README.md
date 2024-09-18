The dependencies specified in composer.json of this directory are required to
run phpstan in CI.

`civicrm/civicrm-core:<5.52` has `symfony/dependency-injection:~3.0` as supported
version, thus the requirement `symfony/dependency-injection:>=4.4` is added.
