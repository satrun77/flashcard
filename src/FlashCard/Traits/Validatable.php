<?php

namespace Moo\FlashCard\Traits;

use Illuminate\Database\Eloquent\Model;
use Validator;

/**
 * Trait Validatable execute validation rules before creating or updating model attributes
 */
trait Validatable
{
    /**
     * Boot the trait.
     */
    protected static function bootValidatable()
    {
        // Hook to execute before creating
        static::creating(function (Model $model) {
            $model->validate();
        });

        // Hook to execute before updating
        static::updating(function (Model $model) {
            $model->validate();
        });
    }

    /**
     * Validates current attributes against rules
     *
     * @return bool
     * @throws \DomainException
     */
    public function validate()
    {
        $validator = $this->getValidator();

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        return true;
    }

    /**
     * Get instance of validator
     *
     * @return \Illuminate\Validation\Validator
     */
    public function getValidator(): \Illuminate\Validation\Validator
    {
        return Validator::make($this->attributes, $this->getRules());
    }

    /**
     * Required method that provide collection or rules
     *
     * @return array
     */
    abstract protected function getRules(): array;
}