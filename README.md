# Candidature #MAVOIX v1.0.0

Soumission et publication des inscriptions au tirage au sort [#MAVOIX](https://mavoix.info).

Le formulaire se trouve à l'adresse : https://candidature.mavoix.info.


## Dependencies

- PHP 5 or later
- Composer
- PHP mcrypt module
- MySQL database


## Installation

1. Set documentRoot of your VHOST on /web/
1. Set chmod 777 on /web/data folder
1. Set chmod 777 on /tmp folder
1. execute CREATE-DATABASE.sql in a new database
1. Create a user admin (use PASSWORD function on pass field)
1. Run composer update
1. Copy web/config.sample.php into web/config.php and customize it
