# Orders Manager

## Setup

### 1) Install the vendor libs with Composer!

[Download Composer][composer] either globally or right into *this* directory,
and then (from this directory), run:

```bash
php composer.phar install
```

This will interactively ask you for some settings - like `database_host`.
Fill those in.

If you make a mistake - just modify `app/config/parameters.yml` afterwards!

If you get an error about "Unknown Database" - it's cool! Ignore it for now.

### 2) Bootstrap the database

To get your database up and running, execute the following commands:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

If you get any errors, check your database settings in `app/config/parameters.yml`.

### 3) Start your web server

We recommend using the built-in PHP web server. To start it, run:

```bash
php bin/console server:run
```

Then, load the site at:

    http://localhost:8000
## Problem

An organisation needs to control its purchase orders to each supplier. Some purchase orders have an employee responsible for controlling the deliveries. The purchase orders are registered in the official government financial system, which does not implement any API. The only way to access the data is downloading a text file that contains all information of an account.
The employees responsible for controlling the orders need a centralised place to search for the orders they are responsible for and add comments about the deliveries.

## Solution

1. A user uploads the text file with the **balances** of all **purchase orders** in an **account**.
2. The system processes the text file and updates the **balances** of each **purchase order** in their respective **accounts**.
3. Users can easily search for **purchase orders**, **suppliers** or **accounts** via the system interface and download all information in a CVS file format.

### Accounts

There are four accounts in which an order can be at the same time:

1. *Orders to settle*. When the delivery has not started yet.
2. *Orders being settled*. When the delivery has started but has not finished yet.
3. *Orders settled to pay*. When the delivery is completely done but the supplier was not paid yet.
4. *Orders paid*. When delivery and payment are completely done.


[composer]: https://getcomposer.org/