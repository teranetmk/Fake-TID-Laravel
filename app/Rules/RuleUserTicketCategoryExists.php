<?php

    namespace App\Rules;

    use Illuminate\Contracts\Validation\Rule;

    use App\Models\UserTicketCategory;
    
    class RuleUserTicketCategoryExists implements Rule
    {
        public function __construct()
        {

        }

        public function passes($attribute, $value)
        {
            return UserTicketCategory::where('id', $value)->get()->first() != null;
        }

        public function message()
        {
            return __('frontend/main.choose_ticket_category');
        }
    }
