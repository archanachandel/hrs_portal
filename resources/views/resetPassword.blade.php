<form method="post">
    @csrf
    <input type="hidden"name="id" value="{{$user[0]['id']}}">
    <input type="hidden"name="password" placeholder="new password">
    <input type="hidden"name="password_confirmation" placeholder="confirm password">
    <br><br>
    <input type="submit"name="submit" >




</form>
