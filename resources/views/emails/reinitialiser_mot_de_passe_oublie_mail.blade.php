@php($title = $detail['title'] ?? 'Réinitialisation de votre mot de passe ASTEASY')
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>{{ $title }}</title>
</head>
<body style="font-family: Arial, sans-serif; color:#222; line-height:1.5;">
	<h2 style="color:#0a2540;">{{ $title }}</h2>

	<p>Bonjour {{ $detail['prenom'] ?? '' }},</p>

	<p>
		Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte ASTEASY.
	</p>

	<p>
		Pour définir un nouveau mot de passe, veuillez cliquer sur le lien ci-dessous :
	</p>

	<p>
		<a href="{{ $detail['body'] }}"
			 style="display:inline-block;padding:10px 18px;background:#0a2540;color:#fff;text-decoration:none;border-radius:6px;">
			Définir mon mot de passe
		</a>
	</p>

	<p>
		En cas de difficulté, n'hésitez pas à nous contacter à
		<a href="mailto:it_helpdesk@asteasy.com">it_helpdesk@asteasy.com</a>.
	</p>

	<p>
		Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email.
		Votre mot de passe actuel restera inchangé.
	</p>

	<p>Ce lien de réinitialisation est personnel et temporaire.</p>

	<p>Merci,<br><strong>L'équipe ASTEASY</strong></p>
</body>
</html>
