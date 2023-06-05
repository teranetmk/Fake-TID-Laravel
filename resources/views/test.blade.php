<html>
<head>
    <title>hCaptcha Demo</title>
    <script src="https://hcaptcha.com/1/api.js" async defer></script>
</head>
<body>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    form {
        display: flex;
        justify-content: center;
        flex-direction: column;
        padding: 20px;
        background-color: #ffe6c2;
        border-radius: 4px;
    }

    form > input {
        margin-bottom: 20px;
        display: inline-block;
        height: 34px;
        padding: 10px 5px;
    }
</style>

<form action="/test/check" method="GET">
    <input type="text" name="email" placeholder="Email"/>
    <input type="password" name="password" placeholder="Password"/>
    <div class="h-captcha" data-sitekey="10000000-ffff-ffff-ffff-000000000001"></div>
    <br/>
    <input type="submit" value="Submit"/>
</form>
</body>
</html>
