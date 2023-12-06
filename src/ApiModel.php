<?php
/**
 * Created by Joaquín Marcher.
 * User: jmarcher
 * Date: 02/10/2017
 * Time: 15:39.
 */

namespace OUTRIGHTVision;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use OUTRIGHTVision\Exceptions\ImmutableAttributeException;
use OUTRIGHTVision\Relationships\Relationship;
use OUTRIGHTVision\Relationships\SingleRelationship;
use OUTRIGHTVision\Relationships\Traits\HasRelationships;

/**
 * Mapped model from the an API Response.
 */
class ApiModel implements \ArrayAccess, Arrayable
{
    use HasRelationships;
    protected $data;

    protected $cast_model = [];

    protected $cast_dates = ['created_at', 'updated_at'];

    protected $date_timezone = 'Europe/Rome';

    protected $requiredParameters = ['id'];

    protected $relationships = [];

    protected $included_default = [];

    public function __construct($data = null)
    {
        $this->setAttributes($data);
        $this->castDates();
        $this->castApiModels();
        $this->includeDefaultRelationships();
    }

    protected function setAttributes($data = null)
    {
        if ($data instanceof self) {
            $this->data = $data->toArray();
        } elseif (is_iterable($data)) {
            $this->data = (array) $data;
        } else {
            $this->data = [];
        }
    }

    protected function includeDefaultRelationships()
    {
        foreach ($this->included_default as $includeRelationship) {
            if (method_exists($this, $includeRelationship) && $this->{$includeRelationship}() instanceof Relationship) {
                $this->data[$includeRelationship] = $this->{$includeRelationship}();
            }
        }
    }

    protected function castApiModels()
    {
        foreach ($this->cast_model as $key => $keyToCast) {
            $class = self::class;
            if (is_string($key)) {
                $class = $keyToCast;
                $keyToCast = $key;
            }
            if ($this->has($keyToCast)) {
                if ($this->data[$keyToCast] !== null && array_key_exists('data', (array) $this->data[$keyToCast])) {
                    $this->data[$keyToCast] = new $class($this->data[$keyToCast]['data']);
                    continue;
                }
                $this->data[$keyToCast] = new $class($this->data[$keyToCast] ?? []);
            } else {
                $this->data[$keyToCast] = new self();
            }
        }
        reset($this->cast_model);
    }

    protected function castDates()
    {
        foreach ($this->cast_dates as $keyToCast) {
            if (Str::startsWith($keyToCast, '@nullable:')) {
                $nullable = true;
            } else {
                $nullable = false;
            }
            if ($this->has($keyToCast)) {
                if($this->data[$keyToCast] === null && $nullable){
                    continue;
                }
                
                $this->data[$keyToCast] = Carbon::parse($this->data[$keyToCast], 'UTC');
                if ($this->date_timezone !== null) {
                    $this->data[$keyToCast]->setTimezone($this->date_timezone);
                }
            }
        }
    }

    public function getAttributes()
    {
        return $this->data;
    }

    public function __serialize():array
    {
        return $this->getAttributes();
    }

    public function __unserialize(array $data):void
    {
        $this->setAttributes($data);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return get_data($this->data, $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        Arr::set($this->data, $offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        Arr::forget($this->data, $offset);
    }

    public function get($key, $default = null)
    {
        $data = get_data($this->data, $key, $default);
        if ($data instanceof SingleRelationship) {
            // We need to reconstruct the relationship
            $className = $data->getRelatedClassQualifiedName();
            $data = new $className($data);
        }

        return $data;
    }

    public function has($key)
    {
        if (is_object($this->data)) {
            return property_exists($this->data, $key);
        }

        return array_key_exists($key, $this->data);
    }

    public function __set($key, $value)
    {

        // Check if there is any mutator, if yes, throw error
        if (method_exists($this, 'get'.ucfirst($key).'Attribute')) {
            throw new ImmutableAttributeException();
        }

        if (method_exists($this, $key) && ($this->{$key}() instanceof Relationship || $this->{$key}() instanceof SingleRelationship)) {
            $this->data[$key] = $value;

            // Refresh relationship
            $this->data[$key] = $this->{$key}();

            return $this->get($key);
        }

        if (method_exists($this, $key)) {
            throw new ImmutableAttributeException();
        }

        if (is_array($this->data) || ($this->data instanceof \ArrayAccess)) {
            if (array_key_exists($key, $this->data) && $this->data[$key] instanceof self) {
                $this->data[$key] = $value;
                $this->castApiModels();

                return $this->data[$key];
            }

            return $this->data[$key] = $value;
        }
    }

    protected function hasRelationship($key): ?string
    {
        if (method_exists($this, $key)
            && ($this->{$key}() instanceof Relationship || $this->{$key}() instanceof SingleRelationship)) {
            return $key;
        }

        return null;
    }

    public function __get($key)
    {
        if ($relation = $this->hasRelationship($key)) {
            // We check if the relationship was already loaded.
            if (!array_key_exists($relation, $this->relationships)) {
                $this->data[$relation] = $this->{$relation}();
            }

            return $this->get($relation);
        }

        if (is_array($this->data) || ($this->data instanceof Collection) || ($this->data instanceof \ArrayAccess)) {
            if (array_key_exists($key, $this->data) || ($this->data instanceof Collection && $this->data->has($key))) {
                return $this->get($key);
            }
        }

        // Check if there is any mutator, if yes, call it.
        if (method_exists($this, 'get'.ucfirst($key).'Attribute')) {
            return $this->{'get'.ucfirst($key).'Attribute'}();
        }

        if (method_exists($this, $key)) {
            return;
        }
    }

    /**
     * If any of the required parameter do not exist, the
     * model is maked as not existing.
     *
     * @return bool
     */
    public function exists(): bool
    {
        foreach ($this->requiredParameters as $parameter) {
            if (!$this->has($parameter)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return (array) $this->data;
    }
}
