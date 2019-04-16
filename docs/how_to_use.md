# Creating your first model
First we need to make an API Call (API Call library is under development and will be published soon)

Lets fake some API calls and asume that the result is a fake user as the following:

```json
{
    "data" : {
    "id": 1,
    "name": "Leanne Graham",
    "username": "Bret",
    "email": "Sincere@april.biz",
    "address": {
      "street": "Kulas Light",
      "suite": "Apt. 556",
      "city": "Gwenborough",
      "zipcode": "92998-3874",
      "geo": {
        "lat": "-37.3159",
        "lng": "81.1496"
      }
    },
    "phone": "1-770-736-8031 x56442",
    "website": "hildegard.org",
    "company": {
      "name": "Romaguera-Crona",
      "catchPhrase": "Multi-layered client-server neural-net",
      "bs": "harness real-time e-markets",
      "geo": {
        "lat": "-35.3159",
        "lng": "80.1496"
      }
    },
    "posts": {
        "data": [
            {
                "id": 1,
                "title": "sunt aut facere repellat provident occaecati excepturi optio reprehenderit",
                "body": "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto"
            },
            {
                "id": 2,
                "title": "qui est esse",
                "body": "est rerum tempore vitae\nsequi sint nihil reprehenderit dolor beatae ea dolores neque\nfugiat blanditiis voluptate porro vel nihil molestiae ut reiciendis\nqui aperiam non debitis possimus qui neque nisi nulla"
            },
            {
                "id": 3,
                "title": "ea molestias quasi exercitationem repellat qui ipsa sit aut",
                "body": "et iusto sed quo iure\nvoluptatem occaecati omnis eligendi aut ad\nvoluptatem doloribus vel accusantium quis pariatur\nmolestiae porro eius odio et labore et velit aut"
            }
        ]
    }
  }
}
```

To create our first Api Model we need to create a new file `User.php` like this:

```php
<?php

namespace App\Api\Models;

use OUTRIGHTVision\ApiModel;

class User extends ApiModel{}
```

Just like that we can access any parameter inside the User json object as a PHP object:

```PHP
$user = new User($jsonResponse);
echo $user->id; // Result: 1
echo $user->website; // Result: hildegard.org
echo $user->company['name']; // Result: Romaguera-Crona
```

## Single Relationships
But that does not look pretty, the `User`s company should also be an Api Model, this can be done really easy:

We need to let know the `User` model that it needs to cast the `company` parameter to an Api model:

Add or extend the following `protected` attribute:
```php
class User extends ApiModel{
    protected $cast_model = [
        'company',
    ];
}
```

After we do this we can access the attributes from a company just like this:

```php
echo $user->company->name; // Result: Romaguera-Crona
echo $user->company->geo['lat']; // Result: -35.3159
```

Continue reading the next chapter to know more on how to cast parameters to custom Api Models.
