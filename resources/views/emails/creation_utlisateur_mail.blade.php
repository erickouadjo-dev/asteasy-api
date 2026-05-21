<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Création de mot de passe</title>
</head>
<body>
	<h2>{{$detail['title']}}</h2>
<p>Bonjour,</p>
<p>
	Nous vous informons que votre compte est maintenant opérationnel sur https://www.comptup.com.
	Pour vous connecter, veuillez cliquer sur l'URL ci-dessous et renseigner votre nouveau mot de passe :
	<a href="{{$detail['body']}}">{{$detail['body']}}</a>
</p>
<p>
	En cas de difficultés, n’hésitez pas à nous contacter via
	<a href="mailto:it_helpdesk@asteasy.com">it_helpdesk@asteasy.com</a>.
</p>
<p>Par ailleurs, nous vous souhaitons une très belle expérience.</p>
<p>Merci,</p>
<p>ASTEASY</p>
</body>
</html>
