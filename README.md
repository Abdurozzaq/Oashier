# Oashier
A Cashier Web App Made With Laravel, VueJS, And Vuetify

![Image of Yaktocat](https://github.com/Abdurozzaq/Oashier/blob/master/docs/screenshoots/CashierHomeDashboard.png)

**Features**


**User Roles**

Admin, Cashier

**Basic Features**

Authentication, Create Menu, Edit Menu, Set Menu Stock,
Create Order, Edit Order Details, 
Add & Edit Menu On Every Order And Menu Stock Automatically Changed,
Cancel Order, And Cash Payment.

**User Manager**

Create Cashier, Manage Cashier Profile,
Create Admin, Manage Admin Profile.



# Installation

Create a Database Table in phpMyAdmin

Extract the Oashier Source Code that has been downloaded to a folder anywhere.

Open Code Editor → Terminal.

In Terminal, navigate to the extracted Oashier folder.
  ```$ cd oashier```
  
Enter these commands one by one (without the $ sign),
  ```
  $ composer install
  $ npm install
  $ cp .env.example .env
  $ php artisan key:generate
  $ php artisan storage:link
  $ php artisan jwt:secret
  ```
  
Edit the .env file like this,
  ```DB_CONNECTION = mysql
  DB_HOST = 127.0.0.1 // change to Host your database
  DB_PORT = 3306
  DB_DATABASE = oashier // change to the name of the database table that you created
  DB_USERNAME = root // change to be your database username, default root
  DB_PASSWORD = ... // change to your databse password, null default 
  ```
  
Run this command for Seed :
  ```$ php artisan migrate --seed```
  
Done 😉, to run Oashier enter the command below:
  ```$ php artisan serve```
  
Then open the browser, and enter the url:
  ```http://localhost:8000```
  
or if you want to run on another port, use the command:
  ```$ php artisan serve --port: 627 // e.g. the port is "627"```

And for serving the vue JS run this command:


For Development : ```npm run watch```

For Production : ``` npm run prod```

Thank you, Good Luck ... 😁



# The Accounts on seeder:
Admin Account - Emaill: admin@gmail.com, Password: password

Cashier Account - Email: user@gmail.com, Password: password

You can add new account from admin account.
