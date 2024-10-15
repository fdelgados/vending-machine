# Vending Machine Challenge

## Requirements
In order to run this application, you need to have Docker and Docker Compose installed on your machine, as well as Make.
 
## Description
This is a simple vending machine program that allows a user to select a product and pay for it. The vending machine will then dispense the product and any change due.

## Installation

1. Clone the repository
```bash
git clone git@github.com:fdelgados/vending-machine.git
```
2. Start the Docker container and install dependencies:
```bash
make init
```
3. Run the application:
```bash
make init
```
4. To stop the application:
```bash
make stop
```
5. To destroy the application:
```bash
make destroy
```

## Usage

### Running the tests
If you want to run the tests of the overall application, you can run the following command:
```bash 
make test
```
Or you can run the tests of just one context, passing the parameter "s=" to run a specific test suite:
```bash
make test s=suite_name
```
There are one suite for each context of the application: `common`, `operation` and `maintenance`.

### Running the application
There are two ways to use the application:

#### Using the machine as a user
To use the machine as a user and buy a drink, you can run the following command:
```bash
make operate
```

#### Using the machine as service person
You can refill the machine with drinks or coins, or even get the total amount of money in the machine. To do that, you can run the following command:
```bash
make maintenance
```
In both cases, follow the machine's instructions.

Enjoy!