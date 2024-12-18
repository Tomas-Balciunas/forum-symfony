# Forum App

General forum app (work is still in progress).
General functionalities:
#### Authentication:
- Simple registration with email verification. Currently, it is only local, content can be checked in web profiler. 
- The route to verify an account is /verify?code=verification_code.
- All actions are restricted until the account is verified.
#### Authorization:
- Users have several roles and each role has default permissions. 
- Most actions require permissions to have been granted.
- Admins can revoke or grant specific permissions as well as suspend user accounts.
#### Boards:
- Boards hold topics, they can be created by admins only. 
- Boards can be set to only allow topic creation for certain roles.
- Users can search for topics in boards.
#### Topics:
- Topics can be created by all users, they hold user posts. 
- Topics can be locked, hidden or edited by author or an admin.
- Admins can also set topics as important (moves topic at the top of the list) or move topics to another board.
#### Posts:
- Posts can be created by all users that have the permission to.
#### Users:
- Users have their own profile where they are able to edit their information and adjust some settings such as showing/hiding
email, posts, topics or setting profile private.
#### Notifications:
- (Very experimental feature). Currently, notifications are only sent to a user when another user posts a comment in their topic.

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
```bash
php bin/console tailwind:build
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
- Or promote an existing user to admin (requires relog)
```bash
php bin/console user:promote <username>
```