Unbound LDAP Bundle
===================

## Description

The Unbound LDAP in memory bundle is a Symfony 3 bundle that is built on top of the [Unbound LDAP in memory server](https://www.ldap.com/unboundid-ldap-sdk-for-java).
  
## Requirements

The system requirements for the Unbound LDAP SDK can be found on their [site](https://docs.ldap.com/ldap-sdk/docs/getting-started/system-requirements.html).
The system requirements for the bundle are php 5.6 or php 7.0. It is also advisable to have a working knowledge of the LDAP standards.

## Installation

There are 2 options for installation of this bundle. You can either include it in a symfony app, or use it as a stand alone app.

### Bundle Inclusion

Install the bundle into your Symfony 3 app with the following command. 
```
composer require --dev carnegielearning/unbound-ldap-bundle
```

### Stand Alone App
To use the bundle as a standalone app, you can simply clone the repository, and then perform a composer install.
```
git clone https://github.com/CarnegieLearningWeb/unbound-ldap-bundle.git
cd unbound-ldap-bundle
composer install
```


## Configuration
There are 4 parameters that can be set within the parameters.yml file. These are not required to be set within a yml file as they can be over ridden from the command line.

* unbound_server_bind_address - The local address to access the server from. The default is 127.0.0.1
* unbound_server_port         - The port that the server will bind to. The default is 6389
* unbound_server_base_dn      - The base dn that is to be used with the provided ldif. The default is dc=example,dc=com
* unbound_server_ldif         - The ldif that should be used to populate the server. The default is '%kernel.root_dir%/../src/CarnegieLearning/UnboundLdapBundle/Resources/ldap/sample.ldif'

## Usage
```
   bin/console unbound:server:run
```

To change default bind address and port use the address argument:
```
bin/console unbound:server:run 127.0.0.1:6389
``` 

To change default LDIF file use the --ldif option:
```
bin/console unbound:server:run --ldif=Resources/ldif/sample.ldif
```
 
To change the default BaseDN use the --base-dn option:
``` 
bin/console unbound:server:run --base-dn="dc=example,dc=com"
```
 
To force a restart of a server that is already running use --force or -f
```
bin/console unbound:server:run -f
```