[![Travis CI Image](https://travis-ci.org/welps/kaltura-entries-to-xml.svg?branch=master)](https://travis-ci.org/welps/kaltura-entries-to-xml) [![Coverage Status](https://coveralls.io/repos/welps/kaltura-entries-to-xml/badge.svg?branch=master&service=github)](https://coveralls.io/github/welps/kaltura-entries-to-xml?branch=master)

# Kaltura Entries To XML
Retrieves entries from your Kaltura instance in the form of formatted XML for bulk import back into the Kaltura Management Console. This will allow you to make bulk changes to data that the KMC isn't capable of.

Demo: [http://waynecheng.net/kaltura-entries-to-xml/]

Versions: [For PHP 5.3](https://github.com/welps/kaltura-entries-to-xml/tree/php53-conversion) | [For PHP 5.5.9+](https://github.com/welps/kaltura-entries-to-xml)

## Installation

1. Clone or download this repository
2. Copy `.env.example` to `.env` and fill in your Kaltura credentials
3. Within the root of the repository, type `composer install`
4. Make sure to generate a new laravel key with `php artisan key:generate`.
5. If your server does not support `.htaccess` files, you may wish to follow instructions here for Laravel to work properly with your web server: [http://laravel.com/docs/#pretty-urls]
6. You should be able to access this application now through your browser at the repository location / public folder. If you've deployed it at http://www.example.com/kaltura-entries-to-xml, you will be able to access it at http://www.example.com/kaltura-entries-to-xml/public/.
7. If you receive a permissions error regarding the directories, you may need to run the following command from the root of the repository `find . -type d -exec chmod 775 {} +` to give proper directory permissions.

## Use

1. Use the application to find all entries matching the search item you're looking for, like a certain misspelled tag.
2. Download the XML the application gives you and make your changes with any XML or text editor.
3. Log into the KMC at http://www.kaltura.com (or wherever your instance is) and submit the corrected XML through the "Bulk Import" service. Note that you may wish to keep a copy of the original XML in case you wish to revert changes.
4. The KMC will apply the XML update to all applicable entries.

If you have any issues, feel free to open a new issue or pull request. Thanks.
