# SportsRecruits Code Challenge

Hello, welcome to my Code Challenge application for SportsRecruits.

## Installation
The application is containerized using [Docker](https://docs.docker.com/get-docker/) and [Docker-Compose](https://docs.docker.com/compose/install/). These tools are required to install the application. Everything else is provided in the container images.
The application also persists its database data in the directory `~/.srapp`, which may need to be created (this installation assumes a MacOS, Unix, or WSL2 installation. Adjustments to this docker-compose.yml file may need to be made if running on native Windows due to the Windows directory structure). Finally, the application also uses some nontraditional ports, based entirely on port values open on my development machine while creating the project. Should you have any of these ports in use, some modifications may need to be made for full functionality.

