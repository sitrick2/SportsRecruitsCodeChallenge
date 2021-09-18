# SportsRecruits Code Challenge

Hello, welcome to my Code Challenge application for SportsRecruits.

## Installation
The application is containerized using [Docker](https://docs.docker.com/get-docker/) and [Docker-Compose](https://docs.docker.com/compose/install/). These tools are required to install the application. Everything else is provided in the container images.
The application also persists its database data in the directory `~/.srapp`, which may need to be created (this installation assumes a MacOS, Unix, or WSL2 installation. Adjustments to this docker-compose.yml file may need to be made if running on native Windows due to the Windows directory structure). Finally, the application also uses some nontraditional ports, based entirely on port values open on my development machine while creating the project. Should you have any of these ports in use, some modifications may need to be made for full functionality.

## Noteworthy Design Choices
- The application makes (relatively) heavy use of laravel's singleton binding to pair Interface classes with the desired class that implements them. Where referenced, these classes reference the Interface instead of the intended class directly so as to minimize the footprint
should a different implementation of the class be desired further down the road. The application also uses the Repository pattern for accessing models/the database for similar reasons -- if there ever came a time to move the application away from Eloquent, it would simply require rewriting the repository implementation, minimizing the amount of referenced changes elsewhere in the code. This provides a good mix of utilizing the features of the framework while also allowing flexibility and maneuverability with future changes without adding insane amounts of tech debt to manage.


## Changes For Further Iterations
- Currently for the sake of time, the application generally handles invalid data errors gracefully with warning logs, discarding failures and continuing on. Further iterations would make use of robust Exception throwing and handling to restore corrupted input or otherwise escalate the failure state.
