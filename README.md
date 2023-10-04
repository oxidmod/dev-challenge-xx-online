# Backend | DEV Challenge XX | Online round
Almost perfect Excel backend :)

## How to start service
The easiest way is to execute `./start.sh` from the root folder.\
\
Same actions step-by-step:
* Execute `docker-compose up -d` to start containers.
* Execute `docker-compose exec app php composer.phar install` to install composer dependencies.
* Execute `docker-compose exec app php composer.phar migrate` to create DB table.
* Execute `docker-compose restart app` to restart app container (Now we are ready).

## Tests
There are two types of tests unit and feature (integration):
* Execute `docker-compose exec app php composer.phar tests` to run all tests
* Execute `docker-compose exec app php composer.phar tests-unit` to run only unit tests
* Execute `docker-compose exec app php composer.phar tests-feature` to run only feature tests

## How to use API
API is exposed on http://localhost:8080/api/v1. Try to open it in browser to check that everything is OK and webserver is ready.\
Other accessible endpoints:
* `GET http://localhost:8080/api/v1/:sheet_id` - Get all cell on the sheet
* `GET http://localhost:8080/api/v1/:sheet_id/:cell_id` - Get single cell
* `POST http://localhost:8080/api/v1/:sheet_id/:cell_id` - Create or update single cell

## Notes
One of the requirements of the task is a format of sheet_id and cell_id:\
`:sheet_id and :cell_id should be any URL-compatible text that represents the namespace and can be generated on the client`\
I decided to accept only those characters which do not require URL encoding, so allowed characters are limited with folowing regular expression:\
`/A-Za-z0-9_\~\.\-/`. The only reason why is not to make user struggle with entering URL-encoded values as part of his formulas :)

## How it works
The application has three main parts: Value parser, dependency graph and expression evaluator.

### Value parser (App\Domain\ValueParser\ValueParser)
The purpose of the parser is to understand if we receive just a simple value (number or string) or some formula (string started with `=` symbol).\

In case if we receive formula this parser is trying to recognize cell IDs inside the formula string and replace them with special placeholders.\
That parsed formula with placeholders and list of referenced cell IDs will be saved for later usage during formula calculation.\

As a result, value parser returns one of two possible objects `App\Domain\ValueParser\Value` or `App\Domain\ValueParser\Formula`
Simple value is set as a result of the cell immediately and formula must be calculated first.

### Dependency graph (App\Domain\Sheet\DependencyGraphInterface)
As we have many cells which could depend on each other, we need to determine the correct order of cells calculation.\
To deal with problem I decided to use dependency graph (the same algorithm is used to install software dependencies)
Behind the interface you will find simple library [digilist/dependency-graph](https://github.com/digilist/dependency-graph) which can build dependencies tree from given nodes and then resolve correct calculation order.\
That library also allows me to find circular references and throws exception in this case.\

As it was mentioned before, list of referenced cells (parent cells) is saved for each cell after parsing. This list is used to build dependencies graph.\
At this point request handling could be interrupted by circular references error.\

If circular references aren't found we can continue. Next step is to use dependencies graph to found all other cells which are dependent from updated cell.\
It's obvious that their values must be recalculated to check that new value of updated cell will not spoil other cells.\
So, results of those cells are unset, and they are sent to next stage keeping order which was calculated by dependency graph. 

### Expression evaluator (App\Domain\Sheet\ExpressionEvaluatorInterface)
This is the last step. Expression evaluator is using parsed formulas from the value parsing stage.\
Each cell placeholder is replaced with a value of that cell to prepare final expression. Then it is evaluated and saved as a cell result. At this step some errors could appear (invalid math expression, division by zero, etc...)
These errors are thrown by another library [chriskonnertz/string-calc](https://github.com/chriskonnertz/string-calc).

Part of the errors could appear because of incorrect initial value parsing.\
As cell ID could contain `-` it could lead to incorrect parsing. If you are sure that the expression is valid it is better to put cell IDs inside of brackets `(my-cell-5)` to help parser a little bit.\

This calculation library also allows to use different math function and constants, so it could be used to improve user experience with formulas.

### Saving results
If all affected cells are calculated without errors we can save that changes into database.\
Whole create/update cell request is wrapped into transaction (see App\Presentation\Http\Middlewares\TransactionMiddleware)\
and data is locked for processing with `FOR UPDATE` construction.\
As all results are saved on update action, the read operation are really fast.


## How to improve application
* Calculation library is using native php operations and they could be not precise enough with very small or big numbers. But the library allows us to replace all operations and functions with custom implementations. So, it is possible to use bcmath extension for better calculation.
* I decided not to use any ORM and just wrote few SQL queries. But it is possible to improve performance for big sheets with libraries like Doctrine ORM, which could track if objects were changed and save only affected cells.
* More tests are always good =)
