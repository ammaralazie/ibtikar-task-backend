<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LdapRecord\Configuration\ConfigurationException;

use LdapRecord\Models\FreeIPA\User as FreeIPAUser;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Payload;
use LdapRecord\Container;

class AuthService
{
    public function authenticateUser($credentials):  array | null
    {
        $user = FreeIPAUser::where('uid', $credentials['username'])->first();

        if ($user && $this->validatePassword($credentials)) {

            $data=$this->generateUserToken($user,$credentials['password']);

            return ['userData' => $data['user'],'accessToken' =>$data['accessToken']];
        }

        return null;
    }


    private function validatePassword(array $credentials):bool
    {
        $ldapConnection =Container::getDefaultConnection();

        $user = "uid={$credentials['username']},cn=users,cn=accounts,dc=demo1,dc=freeipa,dc=org";

        try {
            return $ldapConnection->auth()->attempt($user, $credentials['password']);
        }
        catch (\LdapRecord\Auth\BindException | \LdapRecord\LdapRecordException $e){
            $error = $e->getDetailedError();

            error_log($error);
            return  $error ;
        }
        catch (\Exception $e) {

            error_log($e->getMessage());
            return $e->getMessage();
        }
    }

    private function generateUserToken(FreeIPAUser $userData,string $password): array
    {

        $username = $userData['uid'][0] ;
        $firstName = $userData['givenname'][0];
        $lastName = $userData['sn'][0] ;

        $user = User::firstOrCreate(
            ['username'=>$username],[
            'id'=>Str::uuid()->toString(),
            'username' => $username,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'freeIPA' => true,
            'password'=>bcrypt($password)
        ]);


        // Log user data for debugging
        error_log('User data:'. json_encode($user->toArray()));

        // Generate the token from the user instance
        return ['user'=>$user,'accessToken'=>JWTAuth::fromUser($user)] ;
    }
}
