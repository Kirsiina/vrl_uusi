# vrl_uusi

Fuel CMS toimii PHP:n versiolla 8.1.17, ei tue uudempaa. Jos teet muutoksia config.php tai env-tiedostoon, ÄLÄ commitoi niitä tänne. Pyörii Dockerilla, jos käytät XAMPPia tai vastaavaa, vaihda env-tiedostoon ja configiin kansion nimi localhostin perään, tai luo uusi virtualhost esimerkiksi [tällä ohjeella](https://stackoverflow.com/a/27754990)

----

Oletuksena tietokannan nimi on vrlv3, serveri localhost ja user root ilman salasanaa. 

Oletuksena salausavaimeksi on asetettu `$config['encryption_key'] = 'test_test_test_test';`

Kuvat, javascriptit yms. sijoitetaan assets-kansioon, kaikki sivut `fuel/modules/fuel/views` -kansioon

Alkuunsa luo tietokanta (vrlv3), ja aja sinne database kansion .sql tiedostot seuraavassa järjestyksessä (älä aja muita tiedostoja): 

- fuel_schema.sql
- listat_data_schema.sql
- tunnukset_schema.sql
- from_scratch_schema.sql
- from_scratch_insert.sql

# Pyörittäminen lokaalisti Dockerilla
Sovellus on kontitettu ja käynnistettävissä docker composella. Paikallisissa `database.php` ja `config.php` tiedostoissa tarvii olla tällaiset arvot:

<details>
<summary>database.php, rivistä 76 -> </summary>

```php
$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'db',
	'username' => 'root', //EDIT THIS
	'password' => '', //EDIT THIS
	'database' => 'vrlv3', //EDIT THIS
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_swedish_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE,
	'port' => 3306
);
```

</details>

<details>
<summary>config.php, rivi 26</summary>

```php
$config['base_url'] = 'http://localhost/';
```

</details>

### KOMENNOT

Käynnistys (sama komento toimii kaikilla kerroilla). Sinun ei tarvitse käynnistää containeria uudestaan kun teet koodiin muutoksia, ne päivittyvät reaaliajassa.

```sh
docker compose up --build
```

Containerien sammutus: `ctrl + C` tai pidemmän kaavan kautta (poistaa myös tietokannan volumen = alustuu seuraavalla käynnistyskerralla "nollasta"):

```sh
docker compose down --volumes
```

#### Oletusosoitteet yms. Dockerilla käynnistettäessä:

Verkko-osoite: `http://localhost`

PHPMyAdmin: `http://localhost:8080`

# FUEL CMS
FUEL CMS is a [CodeIgniter](https://codeigniter.com) based content management system. To learn more about its features visit: http://www.getfuelcms.com

### Installation
To install FUEL CMS, copy the contents of this folder to a web accessible folder and browse to the index.php file. Next, follow the directions on the screen. 

### Upgrade
If you have a current installation and are wanting to upgrade, there are a few things to be aware of. FUEL 1.4 uses CodeIgniter 3.x which includes a number of changes, the most prominent being the capitalization of controller and model names. Additionally it is more strict on reporting errors. FUEL 1.4 includes a script to help automate most (and maybe all) of the updates that may be required in your own fuel/application and installed advanced module code. It is recommended you run the following command using a different branch to test if you are running on Mac OSX or a Unix flavor operating system and using Git:
``php index.php fuel/installer/update``

### Documentation
To access the documentation, you can visit it [here](http://docs.getfuelcms.com).

### Bugs
To file a bug report, go to the [issues](http://github.com/daylightstudio/FUEL-CMS/issues) page.

### License
FUEL CMS is licensed under [Apache 2](http://www.apache.org/licenses/LICENSE-2.0.html). The full text of the license can be found in the fuel/licenses/fuel_license.txt file.

___

__Developed by David McReynolds, of [Daylight Studio](http://www.thedaylightstudio.com/)__