## Mini Aspire API

This Project allows authenticated users to go through a loan application. This is a mini version of loan repayment system build with Laravel framework (v9).

### Features:

- User can register.
- User can login.
- User can apply for loan.
- Admin can approve the user loan.
- User can repay the loan. Can do full or partial repaymebnt

### Installation Instructions

- Run `composer install`
    - If want you run on docker run this 
    ```
  docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v $(pwd):/var/www/html \
      -w /var/www/html \
      laravelsail/php81-composer:latest \
      composer install --ignore-platform-reqs
  ```
    **Note:** In place of '/var/www/html' use you directory path
- Run `cp .env.example .env`
- Run `php artisan migrate`
- Run `php artisan jwt:secret`
  
  **Note:** To run application on docker have use different ports for application and mysql. copy and paste below line in .env
```
APP_PORT=3000
FORWARD_DB_PORT=3307
```

### API Documentation
[Postman Collection](https://www.getpostman.com/collections/bfda483964d0e50f228b)
