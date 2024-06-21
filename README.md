# Sylvera Tech Test

Container uses PHP 8.2. Expectation is that solutions provided should use modern PHP where appropriate.

You will need php, [composer](https://getcomposer.org/download/) & [docker](https://www.docker.com/products/docker-desktop/) in order to complete this challenge.

## Install

```shell
cd plugin
composer install
cd ..
docker compose up -d
```

You can open [the post type admin panel](http://localhost:8080/wp-admin/edit.php?post_type=project) once the containers are running. As the database container is created with a seed database it may not be immediately available (this normally takes only a couple of seconds to apply).

## Credentials

Username: `admin`

Password: `X4JCRpiMLDW783oT3%`

## Challenge

This technical challenge is broken into 2 steps: fixing issues, & a refactoring of the existing class.

You should complete these 2 steps in order and _at least_ commit once at the end of each step.

### Fixes

A project (`ID: 28`) already exists in the seed database. This project will be used for the API specification tests. 

#### An error is thrown when loading a project to edit.

Find & fix this issue. You will be asked about your approach to diagnosing the issue.

#### When saving a new project, or editing the existing projects the contents of the inputs read `Array`.

Again, you will be asked about your approach to diagnosing the issue & any alternative solutions you may have considered.

#### `Project Description` and `Project Founded` should be required fields

These inputs currently accept empty values. This behaviour should be corrected.   

#### Update the API to match specifications

[spec.yaml](plugin/openapi/spec.yaml) contains an agreed API contract. Update the API output to meet this contract.

To test the output ensure the docker containers are running & execute:

```shell
# from root of the project
php plugin/openapi/test.php
```

This will yield either an `ERROR: {message}` or `OK` result.

### Unit tests

An example unit test exists utilising BrainMonkey for mocking WordPress functions. This should assist with any unit testing, but feel free to consult [their documentation](https://giuseppe-mazzapica.gitbook.io/brain-monkey/functions-testing-tools/functions-expect)

### Refactoring

[ProjectPostType](plugin/src/ProjectPostType.php) breaks SOLID principles. Provide comments in the code briefly describing what you feel should change, why & how. If you have extra time is insuffient, then feel free to implement some of your suggestions.

## Expectations

Your solution after refactoring should demonstrate modern php syntax, should be readable & well tested. 
