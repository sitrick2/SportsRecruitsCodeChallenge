# SportsRecruits Code Challenge

Hello, welcome to my Code Challenge application for SportsRecruits.

## Installation
The application is containerized using [Docker](https://docs.docker.com/get-docker/) and [Docker-Compose](https://docs.docker.com/compose/install/). These tools are required to install the application. Everything else is provided in the container images.
The application also persists its database data in the directory `~/.srapp`, which may need to be created (this installation assumes a MacOS, Unix, or WSL2 installation. Adjustments to this docker-compose.yml file may need to be made if running on native Windows due to the Windows directory structure). Finally, the application also uses some nontraditional ports, based entirely on port values open on my development machine while creating the project. Should you have any of these ports in use, some modifications may need to be made for full functionality.

## Use
Simply navigate to `http://localhost:8001/teams/create` in a web browser.

## Noteworthy Design Choices
- The application makes (relatively) heavy use of laravel's singleton binding to pair Interface classes with the desired class that implements them. Where referenced, these classes reference the Interface instead of the intended class directly so as to minimize the footprint
should a different implementation of the class be desired further down the road. 
- The application also uses the Repository pattern for accessing models/the database for similar reasons -- if there ever came a time to move the application away from Eloquent, it would simply require rewriting the repository implementation, minimizing the amount of referenced changes elsewhere in the code. This provides a good mix of utilizing the features of the framework while also allowing flexibility and maneuverability with future changes without adding insane amounts of tech debt to manage.
- The use of docker images allows for the simple setup of a `mysql-test` database instance to point the test suites at.  I find this is sturdier than the default laravel setting of using sqlite, which has a handful of annoying conflicts with msyql and can cause unreliable test results and/or undiscovered bugs that later pop up in the deploy process. This does sacrifice some performance in running the actual tests, but I find the tradeoff worthwhile for the headaches it saves.

## Changes For Further Iterations
- Currently for the sake of time, the application generally handles invalid data errors gracefully with warning logs, discarding failures and continuing on. Further iterations would make use of robust Exception throwing and handling to restore corrupted input or otherwise escalate the failure state.
- In the interest of time, simplicity, and sticking to my interpretation of the instructions, most of this process is handled synchronously through a single route and controller method. Given the opportunity to refine and optimize, I would likely break this logic down into a collection of REST API endpoints, process the balancing logic asynchronously using job queues to speed up processing, and retrieve the final team data on the frontend with ajax requests.
- Tests are not effectively mocked at this point, and logic tends to just flow naturally through the application. This is of course not ideal test design but allowed for speed of development and to essentially use tests as a debugging tool. Further iterations would provide more mocks, especially in the `Medium` suite of tests.
- The implementation of the Repository pattern is not as cleanly decoupled as I would prefer -- there are still a handful of instances in the service classes that directly access a model class outside of a repository instead of using the repository as an access layer. I consider this light tech debt I would hope to resolve later.
  - A bonus outcome of a cleaner Repository pattern implementation would be the ability to cache database query results in a clean way that would easily allow for cachebusting when necessary (routing everything through the Repository classes as an access layer would simplify the insertion of `Cache::delete()` calls)
- The application currently relies pretty heavily on manipulation of models in Collections, and it gets a little unwieldly in places. A custom Collection class for Teams especially would allow cleaner code and more efficient logic in retrieving required data.
