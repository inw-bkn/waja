<?php

namespace App\APIs;

use Faker\Factory;
use App\Contracts\AuthUserAPI;

class FakeAuthUserAPI implements AuthUserAPI
{
    public function getUser($login)
    {
        $faker = Factory::create();
        
        if (is_numeric($login)) {
            $gender = ($login % 2) == 0;
        } else {
            $gender = (strlen($login) % 2) == 0;
            $orgId = rand(40000000, 80000000);
        }

        $name = $gender ?
                    $faker->firstNameFemale . ' ' . $faker->lastName :
                    $faker->firstNameMale . ' ' . $faker->lastName;

        return [
            'reply_code' => 1,
            'reply_text' => 'OK',
            'login' => $login,
            'document_id' => $faker->ean13,
            'org_id' => $orgId,
            'full_name' => $name,
            'full_name_en' => $name,
            'email' => "",
            'active' => 1,
            'remark' => '',
            'position' => '',
        ];
    }

    public function authenticate($credentials)
    {
        if ($credentials['password'] !== $credentials['login']) {
            return ['reply_code' => 4, 'reply_text' => 'wrong password.'];
        }
        $data = $this->getUser($credentials['login']);
        $data['reply_text'] = 'granted.';
        return $data;
    }
}
