# pizzaservice

## Description

Database management tool for orders of a pizza delivery service including a database schema for mysql.

Interfaces to manage the database are:
* CLI: Input data into the database
* Web: View the data that is stored in the database


## Set up the development environment

* Install vagrant and virtualbox
* Navigate to the project folder in a console and type ````vagrant up````.
* When the vagrant box is ready type ````vagrant ssh```` and navigate to /vagrant.
* Finally type ````composer install````.


## Usage

### Pizza Tool

````
php pizzatool.php command [options] [arguments]
````

#### Options

| command           | description                            |
| ----------------- | -------------------------------------- |
| create:ingredient | Adds a new ingredient to the database. |
| create:pizza      | Adds a new pizza to the database.      |
| list:ingredient   | Shows a list of all ingredients.       |
| list:pizza        | Shows a list of all pizzas.            |


## License

This project is licensed under the MIT License - see the LICENSE file for details.
