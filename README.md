# x-sure cms-yii2-base

# Environment
* php7.4

# Pseudo-static Configuration
```
location / {
    try_files $uri $uri/ /index.php$is_args$args;
}
```

# Access Paths

-Pre-order interface, 4 types of tests
/webpayment/cashier/preorder
/webpayment/cashier/preorder2
/webpayment/cashier/preorder3
/webpayment/cashier/preorder4
/webpayment/refund/refund-query-test 


1.Set up this PHP API system.
Specifically, a PHP developer is required to build it. An assets folder needs to be created in the main directory, and then run composer install inside the system folder to install the dependencies.
Once the installation is successful, a vendor folder will appear.

2.Point the program to the index.php file.

3.Access it.
For example: domain/webpayment/cashier/preorder
That is, create an order.

4.The keys are stored in source/config/cert and can be changed to your own.
