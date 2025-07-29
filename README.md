# Laravel Modules

**Laravel Modules** — это пакет для Laravel, который предоставляет команду `php artisan module` для упрощения создания полноценных модулей Laravel со всеми необходимыми компонентами. Эта команда позволяет вам создать полнофункциональную структуру модуля с помощью одной команды, экономя время разработки и обеспечивая согласованность во всем вашем проекте

## Требования

- PHP 8.2 или выше
- Laravel 10.10, 11.0 или 12.0

## Установка

1. Добавьте ссылку на пакет в composer.json вашего проекта:
   ```json
   {
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Ma1kaveli/laravel-module-generator"
        }
    ]
   }
   ```

2. Установите пакет через Composer:

   ```bash
   composer require makaveli/laravel-module-generator
   ```

3. (Опционально) Опубликуйте конфигурацию, если требуется настройка:
   ```bash
   php artisan vendor:publish --tag=module-generator-config
   ```
   или
   ```bash
   php artisan vendor:publish --provider="Makaveli\ModuleGenerator\Providers\ModuleGeneratorServiceProvider" --tag="module-generator-config"
   ```

4. (Опционально) Для изменения stubs опубликуйте их:
   ```bash
   php artisan vendor:publish --tag=module-generator-stubs
   ```
   или
   ```bash
   php artisan vendor:publish --provider="Makaveli\ModuleGenerator\Providers\ModuleGeneratorServiceProvider" --tag="module-generator-stubs"
   ```

## Использование

### Основная команда: `module`

Команда `module` — это основная точка входа для создания модулей и их компонентов. Она принимает имя модуля и модели, а также опции для указания, какие компоненты нужно сгенерировать.

**Пример использования:**

```bash
php artisan module UserProfile User --a --c --m --r --s
```

Этот пример создаст модуль `UserProfile` с моделью `User` и сгенерирует действие, контроллер, модель, репозиторий и сервис.

**Параметры:**

- `{name}`: Имя модуля (обязательный).
- `{model}`: Имя модели (обязательный).
- `--a`: Генерировать действие (Action).
- `--cs`: Генерировать команду для сида (Command Seed).
- `--c`: Генерировать контроллер (Controller).
- `--ct`: Генерировать константы (Constant).
- `--f`: Генерировать фильтр (Filter).
- `--fy`: Генерировать фабрику (Factory).
- `--fdto`: Генерировать DTO для формы (Form DTO).
- `--ldto`: Генерировать DTO для списка (List DTO).
- `--j`: Генерировать задачу (Job).
- `--m`: Генерировать модель (Model).
- `--r`: Генерировать репозиторий (Repository).
- `--req`: Генерировать запросы (CreateRequest и UpdateRequest).
- `--res`: Генерировать ресурс (Resource).
- `--ru`: Генерировать правило (Rule).
- `--se`: Генерировать сидер (Seeder).
- `--s`: Генерировать сервис (Service).
- `--t`: Генерировать трейт (Trait).
- `--seT`: Указать, что сидер тестовый.
- `--folder=`: Указать опциональную папку для контроллера.
- `--scope=`: Область видимости контроллера (Shared, Private, Portal, Mobile).
- `--smb`: Использовать `SoftModelBase` вместо стандартной модели Eloquent.
- `--oL`: Добавить операцию списка (для контроллера).
- `--oS`: Добавить операцию отображения.
- `--oC`: Добавить операцию создания.
- `--oU`: Добавить операцию обновления.
- `--oD`: Добавить операцию удаления.
- `--oR`: Добавить операцию восстановления.
- `--bT=*`: Отношения BelongsTo (relationName,relationModel,relationKey).
- `--hM=*`: Отношения HasMany (relationName,relationModel,relationKey).
- `--hO=*`: Отношения HasOne (relationName,relationModel,relationKey).
- `--bTm=*`: Отношения BelongsToMany (relationName,relationModel,pivotModel,foreignPivotKey,relatedPivotKey).
- `--fF=`: Поля для Form DTO (список через запятую в lowerCamelCase).
- `--lF=`: Поля для List DTO (список через запятую в lowerCamelCase).
- `--rF=*`: Поля для запроса (name,rules).
- `--resF=*`: Поля для ресурса (field,mapperField,resource,isArray).
- `--resAF=*`: Дополнительные поля для ресурса (field,mapperField,resource,isArray).
- `--seF=`: Поля для сидера (список через запятую).
- `--sF=`: Поля для сервиса (список через запятую в lowerCamelCase).

### Отдельные команды

Библиотека также предоставляет команды для создания отдельных компонентов:

- `module:action`: Создает действие с операциями (show, create, update, delete, restore).
- `module:command-seed`: Создает команду для сида.
- `module:controller`: Создает контроллер с операциями (list, show, create, update, delete, restore).
- `module:constant`: Создает класс констант.
- `module:filter`: Создает фильтр.
- `module:factory`: Создает фабрику.
- `module:form-dto`: Создает DTO для формы с указанными полями.
- `module:list-dto`: Создает DTO для списка с указанными полями.
- `module:job`: Создает задачу (Job).
- `module:model`: Создает модель с фильтрами, фабрикой и отношениями.
- `module:repository`: Создает репозиторий.
- `module:request`: Создает запрос с правилами валидации.
- `module:resource`: Создает ресурс с полями и дополнительными полями.
- `module:rule`: Создает правило валидации.
- `module:seeder`: Создает сидер (обычный или тестовый) с полями.
- `module:service`: Создает сервис с операциями (create, update, delete, restore).
- `module:trait`: Создает трейт.

Каждая команда принимает свои параметры и опции для настройки генерируемого кода.

## Примеры использования

### Создание модуля с компонентами

```bash
php artisan module Blog Post --a --c --m --r --s --oC --oU --oD
```

Создает модуль `Blog` с моделью `Post` и генерирует действие, контроллер, модель, репозиторий и сервис с операциями создания, обновления и удаления.

### Создание модели с отношениями

```bash
php artisan module:model User User --smb --filter --factory --bT=role,Role,role_id --hM=posts,Post,user_id
```

Создает модель `User` с использованием `SoftModelBase`, фильтром, фабрикой и отношениями `BelongsTo` к `Role` и `HasMany` к `Post`.

### Создание DTO для формы

```bash
php artisan module:form-dto UserFormDTO User User --f=name,email,password
```

Создает DTO `UserFormDTO` в модуле `User` с полями `name`, `email` и `password`.

### Создание ресурса

```bash
php artisan module:resource UserResource User --f=id,null,null,false --f=name,null,null,false --aF=posts,posts,PostResource,true
```

Создает ресурс `UserResource` с полями `id`, `name` и дополнительным полем `posts` как коллекцией `PostResource`.

## Настройка

Библиотека использует шаблоны (stubs) для генерации кода. Их можно опубликовать и настроить:

```bash
php artisan vendor:publish --provider="Makaveli\ModuleGenerator\Providers\ModuleGeneratorServiceProvider" --tag="module-stubs"
```

После публикации шаблоны доступны в директории `stubs` вашего проекта для редактирования.

## Расширение

Для расширения функциональности можно создавать собственные команды, наследуясь от существующих классов и переопределяя методы.

TODO:
*[] Make generate Route
*[] Make generate permission slugs
*[] Make generate logger slugs
*[] Make generate migration when start making module
