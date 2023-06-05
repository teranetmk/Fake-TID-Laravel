<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    use App\Models\FAQCategory;
    
    class FAQ extends Model
    {
        protected $table = 'faqs';

        protected $fillable = [
            'question', 'answer', 'category_id'
        ];

        public function getCategory() {
            $faqCategory = FAQCategory::where('id', $this->category_id)->first();

            if($faqCategory != null) {
                return $faqCategory;
            }
            
            return (object) [ 
                'id' => 0,
                'name' => __('frontend/main.uncategorized'),
                'slug' => 'uncategorized'
            ];
        }
    }
