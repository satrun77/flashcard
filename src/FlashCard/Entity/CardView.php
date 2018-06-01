<?php

namespace Moo\FlashCard\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Moo\FlashCard\Traits\Validatable;

/**
 * CardView is the entity class that represents a record from the database.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class CardView extends Model
{
    use Validatable;

    /**
     * Timestamp enabled.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Name of database table.
     *
     * @var string
     */
    protected $table = 'card_view';

    /**
     * List of allowed columns to be used in $this->fill().
     *
     * @var array
     */
    protected $fillable = ['card_id', 'ip'];

    /**
     * Get the card this view belong to
     */
    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Set ip
     *
     * @param string $value
     * @return self
     */
    public function setIpAttribute($value)
    {
        $this->attributes['ip'] = ip2long(trim($value));

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIpAttribute()
    {
        return long2ip($this->attributes['ip']);
    }

    /**
     * Get collection of validation rules for model attributes
     *
     * @return array
     */
    protected function getRules(): array
    {
        return [
            'ip' => [
                'required',
                // Ensure ip address unique per card
                Rule::unique($this->table, 'ip')->where('card_id', $this->card_id),
            ]
        ];
    }
}
