<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    protected UserService $userService;

    /**
     * Регистрация — внедряем UserService
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Страница регистрации
     *
     * @return \Illuminate\View\View
     */
    public function registerForm()
    {
        return view('auth.register', ['title' => 'Регистрация']);
    }

    /**
     * Обработка регистрации
     *
     * @param RegisterRequest $request
     * @return RedirectResponse
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = $this->userService->createUser($request->validated());

        $request->session()->regenerate();

        auth()->login($user);

        $redirectUrl = session('payment.redirect', null);

        if ($redirectUrl !== null) {
            session()->forget('payment.redirect');

            return $redirectUrl
                ? redirect($redirectUrl)
                : (in_array(auth()->user()->role, ['admin', 'editor'])
                    ? redirect()->route('admin.index')
                    : redirect()->route('frontend.courses.index'));
        }

        return redirect()->route('frontend.courses.index');
    }
}
