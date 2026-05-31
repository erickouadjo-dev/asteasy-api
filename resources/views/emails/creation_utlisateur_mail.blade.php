
@php($title = $detail['title'] ?? 'Création de votre compte ASTEASY')
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
    Nous vous informons que votre compte est maintenant opérationnel sur
    <a href="https://asteasy.deepinovia.com">https://asteasy.deepinovia.com</a>.
  </p>

  <p>
    Pour vous connecter, veuillez cliquer sur le lien ci-dessous et définir votre mot de passe :
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

  <p>Nous vous souhaitons une très belle expérience.</p>

  <p>Merci,<br><strong>L'équipe ASTEASY</strong></p>
</body>
</html>