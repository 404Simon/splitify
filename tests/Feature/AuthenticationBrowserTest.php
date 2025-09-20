<?php

namespace Tests\Feature;

use App\Models\User;

test('user can register', function () {
    visit('/')
        ->assertNoSmoke()
        ->assertNoConsoleLogs()
        ->assertNoJavaScriptErrors()
        ->assertPathIs('/login')
        ->assertSee("Don't have an account? Register here.")
        ->click('@register')
        ->fill('Name', 'Simon')
        ->fill('Email', 'leak@me.de')
        ->fill('Password', 'thisisnotsecure')
        ->fill('Confirm Password', 'thisisnotsecure')
        ->click('Register')
        ->assertPathIs('/groups');
});

test('user can login', function () {
    $user = User::factory()->create();
    visit('/')
        ->assertPathIs('/login')
        ->fill('Email', $user->email)
        ->fill('Password', 'password')
        ->check('Remember me')
        ->click('Log in')
        ->assertPathIs('/groups');
});

test('guests cannot access group overview', function () {
    visit('/groups')
        ->assertPathIs('/login');
});
