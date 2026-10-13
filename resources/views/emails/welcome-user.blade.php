@component('mail::message')
# Welcome to Project Tracker!

Hello **{{ $user->name }}**,

Your account has been created successfully by an administrator.

## Your Login Credentials

**Email:** {{ $user->email }}
**Temporary Password:** `{{ $password }}`

@component('mail::button', ['url' => config('app.url') . '/login', 'color' => 'success'])
Login to Your Account
@endcomponent

**Important:** Please change your password after your first login for security purposes.

### Your Assigned Roles

@if($user->roles && count($user->roles) > 0)
@foreach($user->roles as $role)
- {{ $role }}
@endforeach
@else
No roles assigned yet.
@endif

If you have any questions or need assistance, please contact your administrator.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
