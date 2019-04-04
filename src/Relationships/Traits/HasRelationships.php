<?php
namespace OUTRIGHTVision\Relationships\Traits;

use OUTRIGHTVision\Relationships\HasMany;

trait HasRelationships
{
    protected function hasMany(string $class, string $field)
    {
        // We include the name 
        $this->relationships[] = debug_backtrace()[1]['function'];

        return new HasMany(collect($this->get($field. '.data'))->map(function($entity) use($class){
            return new $class($entity);
        }));
    }
}
