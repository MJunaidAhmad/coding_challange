# Problem Statement

The system should have three main models: Product, Ingredient, and Order. A Burger (Product) can have several ingredients with corresponding stock levels, and the system keeps track of the stock levels of each ingredient. When a customer makes an order that includes a Burger, the system needs to update the stock of the ingredients accordingly. Additionally, if any of the ingredients' stock levels reach 50%, the system should send an email message to alert the merchant to purchase more of that ingredient.

The requirements are to write a controller action that accepts order details from the request payload, persists the order in the database, and updates the stock of the ingredients. The controller action should also send an email once the level of any of the ingredients reaches below 50%, but only a single email should be sent per ingredient. Finally, several test cases should be written to assert that the order was correctly stored and the stock was correctly updated.

# Installation

Clone the project from:
```bash
git clone https://github.com/
```
Open terminal in project directory, execute following command:
```bash
composer install
```

Copy the .env.example and rename it to .env

In .env file, set DB parameters, add following variables:
```bash
QUEUE_CONNECTION=database
```

```bash
RECEIVER_EMAIL={recipient@mail.com}
```


To run migrations and pupulate the database from seeders, open terminal in project directory, execute following command:
```bash
php artisan migrate --seed 
```
This project also utilizes Redis, to setup redis on local system, follow this [link](https://redis.io/docs/getting-started/installation/install-redis-on-windows/)
Then run below command to populate the Redis Cache
```bash
php artisan make:command PopulateRedisWithIngredientsDetails 
```
# Running the project

To run the project, open terminal in project directory, execute following command:
```bash
composer install
```
To run the queues, open terminal in project directory, execute following command:
```bash
php artisan queue:work
```
# Running the test cases

To run the test cases, open terminal in project directory, execute following command:
```bash
./vendor/bin/pest
```
To run the test cases coverage, open terminal in project directory, execute following command:
```bash
./vendor/bin/pest --coverage
```



