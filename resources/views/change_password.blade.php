<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <form action="{{route('change_password')}}" method="post">
        @csrf
        <input type="number" name="user_id" placeholder="User ID">
        <input type="text" name="new_password" placeholder="New Password">
        <button>Change</button>
    </form>
</body>
</html>