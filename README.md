1. Clone the project from Git https://github.com/surajit2908/aspire-api
2. Rrun `composer install`
3. Make .env file a copy of .env.example
4. Run `php artisan migrate`
5. Run `php artisan db:seed` to create admin user
6. Run `php artisan passport:install`
7. Run `php artisan telescope:install`
8. Run `php artisan migrate`
9. Run `php artisan serve`
10. Run `composer test` for testing

Telescope Viewer - http://127.0.0.1:8000/telescope/requests

Admin user credentials
email:surajitsil2908@gmail.com
password:mySecretPassword

Customer :

1. Register customer
2. Login with registered customer email & password
3. Request a loan
4. View all loans belongs to himself
5. Pay Repayments

Admin :
Admin himself also a customer.

1. View all loans
2. Loan approval

Postman documentation link
https://documenter.getpostman.com/view/16323309/2s8Z6yYYyo
