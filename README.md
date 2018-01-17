# pizzaservice

## Description

Database management tool for orders of a pizza delivery service using propel and mysql.

Interfaces to manage the database are:
* CLI: Tool to create ingredients, pizzas and to complete orders
* Web: A website on which customers can order pizzas


## Set up the development environment

* Install vagrant and virtualbox
* Navigate to the project folder in a console and type ````vagrant up````.
* When the vagrant box is ready type ````vagrant ssh```` and navigate to /vagrant.
* Finally type ````composer install````.


## Usage

### Command Line Interface

#### Pizza Tool

````
php pizzatool.php command [options] [arguments]
````

##### Options

| command           | arguments | description                             | options             |
| ----------------- | --------- | --------------------------------------- | ------------------- |
| complete:order    |  order id | Completes an uncompleted order.         |          -          |
| create:ingredient |     -     | Adds a new ingredient to the database.  |          -          |
| create:pizza      |     -     | Adds a new pizza to the database.       |          -          |
| list:ingredient   |     -     | Shows a list of all ingredients.        |          -          |
| list:order        |     -     | Shows a list of all uncompleted orders. | --include-completed |
| list:pizza        |     -     | Shows a list of all pizzas.             |          -          |


### Web Interface

Open your web browser and visit localhost:4567 to use the web interface.


## License

This project is licensed under the MIT License - see the LICENSE file for details.
