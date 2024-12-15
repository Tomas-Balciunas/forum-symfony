# Forum App

wip

# Setting Up

Set up .env, you may use the provided .env.example

### Run Locally

In the project directory run the commands below:

```bash
docker compose up --build
```
```bash
composer install
```
(might need to run the rest in docker bash depending on your environment)
```bash
php bin/console doctrine:migrations:migrate
```
```bash
php bin/console seed:permissions
```
### Additional commands:
Seed mock data for:
- Boards
```bash
php bin/console seed:boards
```
- Users
```bash
php bin/console seed:users
```
- Topics (requires users and boards)
```bash
php bin/console seed:topics
```
- Posts (requires users and topics)
```bash
php bin/console seed:posts
```
- Create a user with admin privileges, the credentials will be provided
```bash
php bin/console seed:admin
```