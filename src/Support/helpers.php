<?php

use Illuminate\Support\Arr;

if (!function_exists('get_data')) {
    /**
     * Gets the data from inside an array replacing -> with .data.
     * e.g.: get_data($data, 'user->contact->email') === $data['user']['data']['contact']['data']['email'].
     *
     * @param array|Collection $data
     * @param string           $key
     * @param mixed            $default
     * @param bool             $nullableString If the string can be empty
     *
     * @return mixed
     */
    function get_data($data, string $key, $default = null, $nullableString = true)
    {
        $key = str_replace('->', '.data.', $key);
        $result = Arr::get($data, $key, $default);
        if (!$nullableString) {
            $result = filled($result) ? $result : $default;
        }

        return $result;
    }
}
