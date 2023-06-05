<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    use App\Models\FAQ;

    class FAQCategory extends Model
    {
        protected $table = 'faqs_categories';

        protected $fillable = [
            'name'
        ];
        
        public function getEntries() {
            $faqs = FAQ::where('category_id', $this->id)->get();
            
            return $faqs;
        }
    }
