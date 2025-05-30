# Navi template

This project uses Docker Compose to build and run a multi-container application. Below are the instructions to set up, build, and run the project.

## Prerequisites

- [Docker](https://www.docker.com/get-started) installed on your machine.
- [Docker Compose](https://docs.docker.com/compose/install/) installed.

## Project Structure

The project includes a `docker-compose.yml` file that defines the services, networks, and volumes required to run the application.

- navi
- nginx
- php
- postgres

## Getting Started

build

```bash
docker compose build
```

run

```bash
docker compose up
```