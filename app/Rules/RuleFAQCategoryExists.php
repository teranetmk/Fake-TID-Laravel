<?php

    namespace App\Rules;

    use Illuminate\Contracts\Validation\Rule;

	use App\Models\FAQCategory;
    
    class RuleFAQCategoryExists implements Rule
    {
        private $message;

        public function __construct($message = null)
        {
            $this->message = $message;
        }

        public function passes($attribute, $value)
        {
            return FAQCategory::where('id', $value)->get()->first() != null;
        }

        public function message()
        {
            return $this->message ?? __('backend/main.category_not_exists');
        }
    }
