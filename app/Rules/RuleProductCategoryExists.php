<?php

    namespace App\Rules;

    use Illuminate\Contracts\Validation\Rule;

	use App\Models\ProductCategory;
    
    class RuleProductCategoryExists implements Rule
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
            
            return ProductCategory::where('id', $value)->get()->first() != null;
        }

        public function message()
        {
            return $this->message ?? __('backend/main.product_category_not_exists');
        }
    }
