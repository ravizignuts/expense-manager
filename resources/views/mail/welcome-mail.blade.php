<x-mail::message>
Dear, {{ $user['firstname'] }}<br>
Welcome to Expense Manager App.<br>
<x-mail::button :url="'http://127.0.0.1:8000/api/auth/verifyuser/'">
Verify User
</x-mail::button>
Verification Token = {{ $user['email_verification_token'] }}
Thanks,<br>
Expense Manager
</x-mail::message>
