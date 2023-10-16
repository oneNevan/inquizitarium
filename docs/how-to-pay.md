# How to play?

## Requirements

1. [Docker Compose](https://docs.docker.com/compose/install/) (v2.10+) installed
2. Command line interface to execute commands
3. Any modern web browser (Google Chrome, Safari or Firefox is recommended)

## Getting Started

#### 1. Clone this repository from GitHub

```
git clone git@github.com:oneNevan/inquizitarium.git && cd inquizitarium 
```
#### 2. Start the project using `make start` command
- **_NOTE_**: The application uses `80` and `443` ports by default. If these ports are already in use, try specifying other free ports, i.e.:
```
HTTP_PORT=8000 HTTPS_PORT=4443 make start
```
OR using `docker compose` commands
```
docker compose build --pull --no-cache && HTTP_PORT=8000 HTTPS_PORT=4443 docker compose up -d
```
#### 3. Migrate database
Run database migration command
```
make sf c="doctrine:migrations:migrate -n" && make sf c="doctrine:fixtures:load -n"
```
OR the same without `make`
```
docker compose exec php bin/console doctrine:migrations:migrate -n && docker compose exec php bin/console doctrine:fixtures:load -n
```

#### 4. Open https://localhost in your favorite web browser to play "The Shelter Demo" quest

- **_NOTE:_** If you used custom ports on the previous step, add the port to the url (https://localhost:4443)

#### 5. Run CLI command to play "The Dwarfs Kingdom" quest (this one is bigger)

```
make sf c="inquizitarium:dwarfs-kingdom:enter"
```
OR the same without `make`
```
docker compose exec php bin/console inquizitarium:dwarfs-kingdom:enter
```

#### 6. To stop the project once you are finished use `make down` command

## Troubleshooting

- This project is based on [dunglas/symfony-docker](https://github.com/dunglas/symfony-docker/tree/main#getting-started)

- If you have any troubles while starting the project, please check the [documentation](https://github.com/dunglas/symfony-docker/tree/main#docs).
