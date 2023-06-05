<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    use App\Models\User;
    
    class Article extends Model
    {
        protected $table = 'articles';

        protected $fillable = [
            'title', 'body', 'user_id'
        ];

        public function getUser() {
            $name = '-/-';

            $user = User::where('id', $this->user_id)->get()->first();

            if($user != null) {
                $name = $user->username;
            }

            return (object) [
                'username' => $name,
                'name' => $name
            ];
        }
    }
