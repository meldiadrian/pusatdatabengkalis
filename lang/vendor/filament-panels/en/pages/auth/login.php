<?php

return [

    'title' => 'Masuk',

    'heading' => 'Masuk',

    'actions' => [

        'register' => [
            'before' => 'or',
            'label' => 'sign up for an account',
        ],

        'request_password_reset' => [
            'label' => 'Forgot password?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Silahkan masukkan email Anda',
        ],

        'password' => [
            'label' => 'Silahkan masukkan password Anda',
        ],

        'remember' => [
            'label' => 'Ingat saya',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Masuk',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Data login tidak cocok dengan data kami.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Terlalu banyak percobaan login',
            'body' => 'Silakan coba beberapa saat lagi.',
        ],

    ],

];
