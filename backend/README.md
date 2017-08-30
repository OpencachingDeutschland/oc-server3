# Backend - The Opencaching Admin Application

The Backend App uses a stronger encrypted password than the current Frontend.
You can generate such a Password (e.g. for inital Setup) using

```php -r "echo password_hash('ThePassword', PASSWORD_BCRYPT) . PHP_EOL;"```

(Replace ```ThePassword``` with your passwort)
This will give you a string like:

```$2y$10$vVUthclGqqDJ5tX5XdFmUONOLsh4ppCLJS51HfVdzfmL.GW1YHxvW```


Copy & Paste this string into the field ```admin_password``` in the ```user``` table.
