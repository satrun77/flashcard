<?php

namespace Moo\FlashCard\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait AskAndValidate provide command user input for ask and validate the user input
 */
trait AskAndValidate
{
    /**
     * @param  string $question
     * @param  string $field
     * @return string
     */
    protected function askWithValidation(string $question, string $field): string
    {
        // Ask the question and get the answer
        $input = $this->ask($question);

        // Populate the model
        $this->getEntity()->fill([
            $field => $input,
        ]);

        // Validate the model data
        $validator = $this->getEntity()->getValidator();
        if ($validator->fails()) {
            // Get error message for the field
            $message = (string) $validator->errors()->first($field);
            // Display warning message if exists
            if (!empty($message)) {
                $this->warn($message);
                // Ask the question again
                return $this->askWithValidation($question, $field);
            }
        }

        return (string) $input;
    }

    /**
     * Required method to return the entity/model that contains the validation rules
     *
     * @return Model
     */
    abstract protected function getEntity(): Model;
}
