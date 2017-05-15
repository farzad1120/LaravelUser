Dear {{$user->name}},{{ PHP_EOL }}
You can verify your email address using the below link{{ PHP_EOL }}
{{ url('register/verify/'.$user->verification_token)  . PHP_EOL . PHP_EOL. PHP_EOL }}
This link will be valid for {{ $user->verification_token_time->diffForHumans() }}, until {{ $user->verification_token_time . PHP_EOL }}
Regards,{{ PHP_EOL }}
{{ config('app.name') }}
