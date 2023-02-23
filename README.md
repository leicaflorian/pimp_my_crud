# Pimp My CRUD

Laravel artisan command for easily generating CRUD views and controller with all necessary code.

- [Installation](#installation)
- [Usage](#usage)
  - [Views](#views)
    - [Arguments](#arguments)
    - [Options](#options)
  - [Controller](#controller)
    - [Arguments](#arguments-1)
    - [Options](#options-1)

## Installation

Install package via composer

```bash
composer require --dev "leicaflorian/pimp_my_crud"
```

## Usage

Before using the command, you have to generate the necessary models and migrate the database because the command will
use the database schema to generate the views and controller.

### Views

`php artisan pmc:views [options] [--] <resource>`

#### Arguments

- `resource`: Name of the resource, in lowercase, plural, e.g. "posts. This can be nested in a subfolder, e.g. "admin/posts"

#### Options

- `--only`: Only create the specified views, separated by comma. Available values are "index", "edit", "create" and "
  show"
- `--wysiwyg`: Add a wysiwyg editor to the edit and create views
- `--force`: Overwrite existing views
- `--model`: Manually specify the model name. [Default: resource name in PascalCase, singular]

```bash
php artisan pmc:views posts
php artisan pmc:views posts --only=index,edit
php artisan pmc:views posts --only=index,edit --force
php artisan pmc:views posts --wysiwyg --force
php artisan pmc:views language-lines --model=Language
```

### Controller

`php artisan pmc:controller [options] [--] <resource>`

#### Arguments

- `resource`: Name of the resource, in lowercase, plural, e.g. "posts. This can be nested in a subfolder, e.g. "admin/posts"

#### Options

- `--force`: Overwrite existing controller
- `--model`: Manually specify the model name. [Default: resource name in PascalCase, singular]
- `--controller`: Manually specify the controller name. [Default: resource name in PascalCase, singular, ending with "Controller"]

```bash
php artisan pmc:controller posts
php artisan pmc:controller posts --force
php artisan pmc:controller language-lines --model=Language 
php artisan pmc:controller language-lines --controller=Language 
```
