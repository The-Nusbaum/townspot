Townspot-Zend
=============
Liquibase
------------------------------------------------
mvn -Ptsz resources:resources liquibase:update

Build Lucene indexes
------------------------------------------------
php public/index.php lucene build indexes

Clearing All Cache
------------------------------------------------
php public/index.php cache clear