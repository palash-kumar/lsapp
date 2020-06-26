### Laravel
* Open Source PHP Framework
* Aims to make dev. process pleasing without sacrificing quality
* One of the most popular and respected PHP Framework
* Uses the MVC (Model View Controller) pattern

#### Contents
1. Laravel Overview
2. Installation/Setup
3. Build a Website & Blog Application
4. Authenticaiton & Access Control
5. Deploying a Laravel Application

**What does Laravel do?**
Features of Laravel are: 
| Route Handling   | Security Layer | Model & DB Migrations  | Views/Templates  |
| Authentication | Sessions | Compile Assets | Storage & File Management |
| Error Handling | Unit Testing | Email & Config | Cache Handling |

**Laravel is installed using composer**

Laravel includes the *Artisan* CLI which handles many tasks, such as:
* Creating controllers & models
* Creating database migration files and running migrations
* Create providers, events, jobs, form requests, etc
* Show routes
* Session command
* Run Tinker
* Create custom commands

#### Examples of Artisan commands
```shell
$ php artisan list // Shows all the available artisan commands
$ php artisan help migrate // Gives informatio on the command
$ php artisan make:controller TodosController // It creates a controller named TodosController
$ php artisan make:model Todo -m // It creates a model named Todo and migrate it to DB
$ php artisan make:migration add_tools_to_db-table=todos
$ php artisan migrate // runs the migration file created in the previous step
$ php artisan tinker // Interacts with DB
```

#### Eloquent ORM
It is another feature used by *Laravel*. **Eloquent** is an ORM (Object Relational Mapper) that uses active record which makes working with DB and Model very easy.

* Makes Querying & working with DB cery easy
* We can still use raw SQL queries if needed

```php
Use App\Todo; // Brings the model in
$todo->title = 'Some Todo';
$todo->save();
```

#### Blade Template Engine
*Laravel* also uses *Blade* Template engine. 
* Simple & powerful
* Control structures (if else, loops, etc)
* Template Inheritance: Extend layouts easily
* Can create custom components