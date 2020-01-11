<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = Password::broker()->reset(
            $request->only(
                'email', 'password', 'password_confirmation', 'token'
            ),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET ?
            response([]) :
            response(['message' => trans($response)], 400);
    }


    /**
     * 修改密码
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function change(Request $request)
    {
        $this->validate($request, [
            'old' => 'required|string',
            'password' => 'required|string|min:6|max:32|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->post('old'), $user->password)) {
            return response(['message' => '旧密码错误，请检查'], Response::HTTP_BAD_REQUEST);
        }

        $token = $this->resetPassword($user, $request->post('password'));

        return response([], 200, [
            'Authorization' => 'Bearer ' . $token
        ]);
    }

    /**
     * @param \App\User $user
     * @param string $password
     * @param bool $login
     * @return string new_token
     * @throws \Throwable
     */
    public static function resetPassword($user, $password, $login = true)
    {
        $user->password = Hash::make($password);

        $user->setRememberToken(time()); // 修改密码的时间, 用于校验token是否有效

        $user->saveOrFail();

        event(new PasswordReset($user));

        if ($login) return Auth::login($user);
        else return true;
    }
}
