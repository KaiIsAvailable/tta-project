<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>User List</h1>
    <a href="{{ route('users.create') }}">Add New User</a>
    
    @if($users->isEmpty())
        <p>No users found.</p>
    @else
        <ul>
            @foreach($users as $user)
                <li>{{ $user->name }} - {{ $user->email }} - Role: <strong>{{ $user->role }}</strong></li>
            @endforeach
        </ul>
        {{ $users->links() }} <!-- Pagination links -->
    @endif
</body>
</html>
