<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>買い物リスト</title>
  </head>
  <body>
    <h1>ログイン</h1>
    @if ($errors->any())
        <div>
        @foreach ($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
        </div>
    @endif
    @if (session('front.user_register_success') == true)
        ユーザーを登録しました！！<br>
    @endif
    <form action="/login" method="post">
      @csrf
      email:<input name="email" value="{{ old('email') }}"><br>
      パスワード：<input name="password" type="password"><br>
      <button>ログインする</button><br>
      <a href="/user/register">会員登録</a><br>
    </form>
  </body>
</html>