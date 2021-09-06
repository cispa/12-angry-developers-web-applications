# 12 Angry Developers A Qualitative Study on Developers’ Struggles with CSP
This repository contains our code for each version (programming language) for the Coding Task.
It is a product of our [work](https://swag.cispa.saarland/papers/roth2021usable.pdf) published at the 28th ACM Conference on Computer and Communications Security (CCS) in 2021.

### Usage:
There are two alternative ways how you can set up this application:

##### 1) Using Docker:
Every folder contains a `docker-compose.yaml` and a `Dockerfile` as well as all required config files.
To start the application via docker-compose, you just need to run `docker-compose up` as root.

##### 2) Direct Installation:
Every folder contains a `install.sh` script as well as all required config files. To set up and start the application, you just need to execute that script with root privileges. Notably, we recommend using docker instead of directly installing the app.

### License:
This project is licensed under the terms of the AGPL3 license, which you can find in `LICENSE`.
