@component('mail::message')
# Password Reset

Hello **{{ $user->name }}**,

Your password has been reset by an administrator.

## Your New Login Credentials

**Email:** {{ $user->email }}
**New Temporary Password:** `{{ $password }}`

@component('mail::button', ['url' => config('app.url') . '/login', 'color' => 'primary'])
Login to Your Account
@endcomponent

**Important:** Please change this temporary password after logging in for security purposes.

If you did not request this password reset, please contact your administrator immediately.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
