<?php

    namespace App\Rules;

    use Illuminate\Contracts\Validation\Rule;

    use App\Models\FAQ;
    
    class RuleFAQExists implements Rule
    {
        private $message;

        public function __construct($message = null)
        {
            $this->message = $message;
        }

        public function passes($attribute, $value)
        {
            if($value == 0) {
                return true;
            }
            
            return FAQ::where('id', $value)->get()->first() != null;
        }

        public function message()
        {
            return $this->message ?? __('backend/system.settings.faq_not_exists');
        }
    }
