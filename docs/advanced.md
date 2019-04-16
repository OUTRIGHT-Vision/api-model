## Assumptions

- We cast the `created_at` and `updated_at` using `Carbon::parse()` by default, if you want to avoid this or add other parameters to date casting, you need to add them to `cast_dates` attribute.
- The `requiredParameters` defines which parameters are needed in order to determine if the model exists or not.
- The default timezone for dates is inside the attribute: `date_timezone`.
- You should not (it is not encouraged) to edit the `relationships` attribute manually.

## Helpers

The library comes with a handy helper `get_data`

This helper allows you to access arrays using dot notation and arrow notation.

### Examples

```php
$user  = [
    'name' => 'Jon Snow',
    'father' => [
            'data' => [
                'name' => 'Rhaegar Targaryen',
                'sister' => [
                    'data' => [
                        'name' => 'Daenerys Targaryen'
                    ],
            ],
        ],
    ],
];

get_data($user, 'name'); // "Jon Snow"
get_data($user, 'father.data.name'); // "Rhaegar Targaryen"
get_data($user, 'father.data.sister.data.name'); // Daenerys Targaryen
// Short version:
get_data($user, 'father->name'); // Daenerys Targaryen
get_data($user, 'father->sister->name'); // Daenerys Targaryen
```
