# Issue-Tracking-System-Api

I created an issue-tracking-system-api for calculating due date time value. The codebase of the api is extendable if there is any new business request related to issue tracking system. 

The solution is PHP based, I used the Slim micro framework and docker for development.

## Install the Application

Run this command from the directory in which you want to install the application.

```bash
composer install
```

To run the application in development, you can run these commands 

```bash
composer start
```

Or you can use `docker-compose` to run the app with `docker`, so you can run these commands:
```bash
docker-compose up -d
```
After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer test
```

## Features

### Due Date Calculator

The endpoint is available by calling the following url: http://localhost:8080/due-date-calculator/{submitDateTime}/{turnaroundTime} .
The submitDateTime should follow DateTime::RFC3339 format to get valid result. The turnaroundTime is an integer, I made a decision not to support 0 as a valid value.