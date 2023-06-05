# [FAKE-TIDS.SU](https://fake-tids.su)

![screenshot](./screenshot.png)

## Requirements

- Docker


## Get started

### Setup

1. Clone the repository into `~/dev`

```bash
git clone git@github.com:baddiservices/fake-tids.su.prod.git
```

2. To avoid user/group permissions issues, let's export your user ID & group ID

```bash
export WWWUSER=$(id -u ${USER}) WWWGROUP=$(id -g ${USER})
```

3. Copy `.env.example` to `.env`

```bash
cp .env.example .env
```

4. Build docker containers

```bash
docker-compose up -d
```

5. Check all containers are running

```bash
docker-compose ps
```

| NAME          |        COMMAND         |       SERVICE |            STATUS |                                          PORTS |
|---------------|:----------------------:|--------------:|------------------:|-----------------------------------------------:|
| mysql | "tini -- /docker-ent…" | mysql-server | running (healthy) |                         0.0.0.0:3306->3306/tcp |
| faketids   |   "start-container"    |         php71 |           running |                             0.0.0.0:80->80/tcp |

6. Connect to app container via SSH

```bash
docker-compose exec faketids /bin/bash
```

7. Install project dependencies

```bash
> composer install && php artisan key:generate && php artisan storage:link
```

8. Run migration and seed default data

```bash
> php artisan migrate && php artisan db:seed && php artisan passport:install
```

9. Build front end

```bash
> npm install && npm run dev
```

Here we're, you can go to [http://localhost:8088](http://localhost:8088)

### Notes

You can use [Laravel Valet](https://laravel.com/docs/9.x/valet) to link the application with a hostname easily.

### Resources

- [Docker Compose](https://docs.docker.com/compose/install)
- [Laravel](https://laravel.com/docs/9.x)
- [Laravel Sail](https://laravel.com/docs/9.x/sail)
- [Laravel Valet](https://laravel.com/docs/9.x/valet)
