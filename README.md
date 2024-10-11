# Vending Machine Challenge

## Description
This is a simple vending machine program that allows a user to select a product and pay for it. The vending machine will then dispense the product and any change due.

## Installation

1. Clone the repository
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
