# RESTful API for order service
Rest API for order list, creation and updation

## Docker, language, Framework, Database and server requirement

- [Docker](https://www.docker.com/) as the container service to isolate the environment.
- [Php](https://php.net/) to develop backend support.
- [Laravel](https://laravel.com/docs) is a stunningly fast PHP micro-framework for building web applications
- [MySQL](https://mysql.com/) as the database layer
- [Apache](https://httpd.apache.org/docs/) as a proxy layer

## How to Install & Run

1.  Clone the repository.
2.  We have used the google distance matrix api for distance calculation and you need your own API key. 
    Update 'GOOGLE_API_KEY' in environment file located in ./api/.env file.
3.  Run `./start.sh` to build docker containers, executing migration and PHPUnit test cases and        
    generating code coverage report
4.  After starting container following will be executed automatically:
	- Table migrations using artisan migrate command.
  - Unit and Integration test cases execution.

## Manually Starting the docker and test Cases

1. You can run `docker-compose up` from terminal
2. Server is accessible at `http://localhost`
3. Run manual testcase suite:
	- Integration Tests: `docker exec order_php php ./vendor/bin/phpunit ./tests/Feature/` &
	- Unit Tests: `docker exec order_php php ./vendor/bin/phpunit ./tests/Unit/`

## Coverage report

1. Open URL `http://localhost/CodeCoverage` for code coverage report

## Swagger integration

1. Open URL `http://localhost/api/documentation` for API demo

## Code Structure
api folder contains application code

**./tests**

- this folder contains test cases files written in UnitTest and Integration folders

**./app**

- contains all the configuration files, controllers, models, services, helpers and validators
- database/migrations folder contains the migration files
	- To manually run migrations use this command `docker exec order_php php artisan migrate`
- `OrderController` contains all the api's methods :
    1. localhost/orders?page=1&limit=4 - GET url to fetch orders list with page and limit
    2. localhost/orders - POST method to create new order with origin and destination
    3. localhost/orders - PATCH method to update status for taken.
       (Handled simultaneous update request from multiple users at the same time by returning response status 422 if the order is already taken)
- `OrderService` contains the business logic.

## API Reference Documentation

- `localhost/orders?page=:page&limit=:limit` :

    GET Method - to fetch orders with page number and limit
    1. Header :
        - GET /orders?page=0&limit=5 HTTP/1.1
        - Host: localhost
        - Content-Type: application/json

    2. Responses :

    ```
            - Response
            [
              {
                "id": 1,
                "distance": 46732,
                "status": "TAKEN"
              },
              {
                "id": 2,
                "distance": 46731,
                "status": "UNASSIGNED"
              }
            ]
    ```

        Code                    Description
        - 200                   successful operation
        - 422                   Invalid Request Parameter

- `localhost/orders` :

    POST Method - to create a new order with origin and destination coordinates
    1. Header :
        - POST /orders HTTP/1.1
        - Host: localhost
        - Content-Type: application/json

    2. Post-Data :
    ```
         {
            "origin" :["26.345209", "76.193421"],
            "destination" :["27.573221", "77.1003452"]
         }
    ```

    3. Responses :
    ```
            - Response
            {
              "id": 21,
              "distance": 0,
              "status": "UNASSIGNED"
            }
    ```

        Code                    Description
        - 200                   successful operation
        - 405                   Invalid Inputs

- `localhost/orders/:id` :

    PATCH method to update status for taken.(Handled simultaneous update request from multiple users at the same time with response status 409)
    1. Header :
        - PATCH /orders/20 HTTP/1.1
        - Host: localhost
        - Content-Type: application/json
    2. Post-Data :
    ```
         {
            "status" : "TAKEN"
         }
    ```

    3. Responses :
    ```
            - Response
            {
              "status": "SUCCESS"
            }
    ```

        Code                    Description
        - 200                   Successful operation
        - 422                   Invalid Request Parameter
        - 409                   Order already taken
