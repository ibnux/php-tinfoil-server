# Tinfoil Server using PHP

This script only for Google drive link, not serving files.

based on [this documentation](https://blawar.github.io/tinfoil/custom_index/)

sample on **tabel.sql**

- upload to server
- create mysql database
- import **tabel.sql**
- copy **config.example.php** to **config.php**
- edit **config.php** based mysql database
- chmod recursive 777 **cache/** and **data/**
- run **`composer install`**
- using phpmyadmin, start adding file id from google drive, or use [google drive API](https://developers.google.com/drive/api/v2/reference/files/list), read on [stackoverflow](https://stackoverflow.com/questions/24720075/how-to-get-list-of-files-by-folder-on-google-drive-api)


if you enable login **$must_login = false;** you must use SMS Server or WhatsApp server. username is phone number, and paswword anything, it will send real password by sms.