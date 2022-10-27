# SoGO for Debian

This is a fork of the official [Sogo](https://github.com/Alinto/sogo) webmail.

We forked this to get an easier way to create debian packages.

# How To Build The Debian Package

Please note that the source code of SOGo contains the pre-built minified & compiled versions of all JS & CSS.

As a result, if you change any file in a subfolder of UI/WebServerResources/js or scss, you'll need to use grunt to compile those.

Please look at the [SOGo Developer Guide](Documentation/SOGoDevelopersGuide.asciidoc) for that.

This Debian package can be built (the Objective-C part) easily as follow (on a standard Debian Bullseye) :

```

# install the build-dependencies:
apt install debhelper gobjc libgnustep-base-dev \
  libmemcached-dev libxml2-dev libssl-dev libcurl4-openssl-dev \
  liblasso3-dev libytnef0-dev libzip-dev libsodium-dev
  
# then install your locally-build sope packages:
# note that you'll need libwbxml2 from debian bookworm (current as of 2022-10)
apt install libsope-appserver4.9-dev libsope-core4.9-dev libsope-gdl1-4.9-dev \
  libsope-ldap4.9-dev libsope-mime4.9-dev libsope-xml4.9-dev \
  libwbxml2-dev libsbjson-dev 

# this compiles the debian package.
# adds --username=you@yourmachine to pgp-sign it
# this signs using debian/changelog last maintainer email.
debuild -d -- binary
```

You'll normally get sogo & sogo-activesync packages in the parent folder.

You can then use dput to send your packages to a debian repository



## Contribute

SOGo is a collaborative effort in order to create the best Free and Open Source groupware solution.

There are multiple ways you can contribute to the project

* Documentation reviews, enhancements and translations
* Feature requests or by sharing your ideas (see the roadmap)
* Participate in the discussion on mailing lists
* Patches for bugs or enhancements
* Provide new translations

## Source Code

You can browse the lastest version of the source code online from Inverse's github repository:

* [https://github.com/Alinto/sogo](https://github.com/Alinto/sogo)

To compile SOGo, you first need to obtain the source code of both SOGo and SOPE. The source code of SOPE and SOGo can be obtained from Inverse's github repositories:

* [https://github.com/Alinto/sope/archive/master.zip](https://github.com/Alinto/sope/archive/master.zip)
* [https://github.com/Alinto/sogo/archive/master.zip](https://github.com/Alinto/sogo/archive/master.zip)

The source code of the SOGo Connector extension for Thunderbird 78+ can be obtained from Inverse's github repository:

* [https://github.com/Alinto/sogo-connector](https://github.com/Alinto/sogo-connector)

Please refer to the [FAQ](https://sogo.nu/support.html#/faq) for [compilation instructions](https://sogo.nu/support/faq/how-do-i-compile-sogo.html).

## Translations

SOGo and its associated components are available in various languages. The following list describes the official translations alongside their maintainers:

* [en] English - [Alinto](https://www.alinto.com)
* [ar] Arabic - Anass Ahmed
* [eu] Basque - Gorka Gonzalez
* [bs_BA] Bosnian - Refik Bećirović
* [pr_BR] Brazilian Portuguese - Alexandre Marcilio
* [bg] Bulgarian - Todor Todorov
* [ca] Catalan - Hector M. Rulot Segovia
* [zh_CN] Chinese (China) - Thomas Kuiper
* [zh_TW] Chinese (Taiwan)
* [hr_HR] Croatian - Jens Riecken
* [cs] Czech - Šimon Halamásek
* [da_DK] Danish (Denmark) - Altibox
* [nl] Dutch - Roel van Os
* [fi] Finnish - Kari Salmu
* [fr] French - [Alinto](https://www.alinto.com)
* [de] German - Alexander Greiner-Baer
* [he] Hebrew - Raz Aidlitz
* [hu] Hungarian - Sándor Kuti
* [id] Indonesian - Woka
* [is] Icelandic - Anna Jonna Armannsdottir
* [it] Italian - Alessio Fattorini
* [jp] Japanese - Ryo Yamamoto
* [kk] Kazakh - Nazym Idrissova
* [lv] Latvian - Juris Balandis
* [lt] Lithuanian - Mantas Liobė
* [mk_MK] Macedonian - Miroslav Jovanovic
* [sr_ME] Montenegrin - Ivan Pleskonjić
* [nb_NO] Norwegian (Bokmål) - Jan Ivar Karlsen / Altibox
* [nn_NO] Norwegian (Nynorsk) - Altibox
* [pl] Polish - Paweł Bogusławski
* [pt] Portuguese - Eduardo Crispim
* [ro_RO] Romanian - Vasile Razvan Luca
* [ru] Russian - Alex Kabakaev
* [sr] Serbian - Bogdanović Bojan
* [sr@latin)] Serbian (Latin) - Zlatko Štulić
* [sk] Slovak - Martin Pastor
* [sl_SI] Slovenian - Jens Riecken
* [es_AR] Spanish (Argentina) - Federico Alberto Sayd
* [es_ES] Spanish (Spain) - Dominique Couot
* [sv] Swedish - Peter Johansson
* [tr_TR] Turkish (Turkey) - Muhammed Yalçınkaya, Sinan Kurşunoğlu
* [uk] Ukrainian - Oleksa Stasevych
* [cy] Welsh - Iona Bailey
 
If you would like to translate the software in an other language, please consult the [FAQ](https://sogo.nu/support/faq/how-to-translate-sogo-in-another-language.html).
