## Single Relationships
In the first model we created we casted the `company` parameter to an Api Model, but wouldn't it be nicer if we could just create a `Company` Api Model and cast the parameter to a `Company` model instead of to a generic Api Model?

Well it is pretty easy to do, first we need to create a new model just like the one we created earlier:

```php
<?php

namespace App\Api\Models;

use OUTRIGHTVision\ApiModel;

class Company extends ApiModel{}
```

Now we need to let know the `User` model that it needs to cast the `company` parameter to a `Company` model:

Add or extend the following `protected` attribute:
```php
<?php
namespace App\Api\Models;

use OUTRIGHTVision\ApiModel;
use App\Api\Models\Company;

class User extends ApiModel{
    protected $cast_model = [
        'company' => Company::class,
    ];
}
```

And just like this we can now access the user's `Company` as a PHP object like this:

```php
echo $user->company->name; // Result: Romaguera-Crona
echo $user->company->geo['lat']; // Result: -35.3159
```

But this is not much different from the case we had before. Well it isn't but now we can tell the `Company` model to cast the `geo` parameter to a `Geo` Api Model

Lets asume we already created a `Geo` Api Model file, now we need to add or extend the `$cast_model` attribute in `Company` like this:

```php
<?php
namespace App\Api\Models;

use OUTRIGHTVision\ApiModel;
use App\Api\Models\Company;

class User extends ApiModel{
    protected $cast_model = [
        'company' => Company::class,
    ];
}
```

Now we have a fully Laravel-like Eloquent model we can do beautifull things like this:

```php
echo $user->company->name; // Result: Romaguera-Crona
echo $user->company->geo->lat; // Result: -35.3159
echo $user->company->geo->randomParameter; // Result: null
```

## HasOne and BelongsTo Relationship

This is just another way to cast a single parameter to an object.

Lets take the example of the `$user` beloning to a company, we can accomplish the same result by doing the following:

```php
<?php
namespace App\Api\Models;

use OUTRIGHTVision\ApiModel;
use App\Api\Models\Company;

class User extends ApiModel{
    public function company()
    {
        return $this->belongsTo(Company::class, 'company');
    }
}
```

`$this->belongsTo` and `$this->hasOne` will have the same effect, it is just a matter of what does makes more sense in your project.

## HasMany Relationship
We can also cast a collection of objects like this:

Lets asume we created a `Post` Api model:

```php
<?php
namespace App\Api\Models;

use OUTRIGHTVision\ApiModel;
use App\Api\Models\Company;
use App\Api\Models\Post;

class User extends ApiModel{
    protected $cast_model = [
        'company' => Company::class,
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'posts');
    }
}
```

Relationships are lazy loaded, that means we do not waste server memory creating a collection if the parameter is not used.
The first time we do `$user->posts` is where we create the collection and return it.

If you want to eager load a relationship you can add or extend the `included_default` protected parameter.


