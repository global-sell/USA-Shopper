<?php
require_once 'config/config.php';
require_once 'config/google-oauth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL);
}

$error = '';
$success = '';

// Using whitelist approach - only trusted email providers are allowed
// No need for temp email blocklist anymore

/*
// Old temp email blocklist (now using whitelist instead)
$tempEmailDomains = [
    // Common temp email services
    'tempmail.com', 'temp-mail.com', 'temp-mail.org', 'temp-mail.io', 'temp-mail.net',
    'guerrillamail.com', 'guerrillamail.net', 'guerrillamail.org', 'guerrillamail.biz',
    '10minutemail.com', '10minutemail.net', '10minutemail.org',
    'throwaway.email', 'throwawaymail.com', 
    'mailinator.com', 'mailinator2.com', 'mailinator.net',
    'getairmail.com', 'fakeinbox.com', 'fake-mail.com',
    'trashmail.com', 'trashmail.net', 'trash-mail.com',
    'yopmail.com', 'yopmail.net', 'yopmail.fr',
    'maildrop.cc', 'maildrop.com',
    'sharklasers.com', 'grr.la', 'guerrillamail.de',
    'getnada.com', 'dropmail.me', 'mintemail.com', 'tmpeml.info',
    
    // Additional temp email services
    'disposablemail.com', 'dispostable.com', 'tempinbox.com',
    'emailondeck.com', 'spamgourmet.com', 'mytrashmail.com',
    'mailcatch.com', 'inboxkitten.com', 'mohmal.com',
    'jetable.org', 'spambog.com', 'spambox.us',
    'burnermail.io', 'harakirimail.com', 'anonbox.net',
    'anonymousemail.me', 'deadaddress.com', 'emailsensei.com',
    'emailtemporanea.com', 'emailtemporanea.net', 'emailtemporar.ro',
    'disposable-email.ml', 'disposableemail.ml', 'spamfree24.com',
    'spamfree24.de', 'spamfree24.eu', 'spamfree24.info',
    'spamfree24.net', 'spamfree24.org', 'emailias.com',
    'haltospam.com', 'incognitomail.com', 'mailexpire.com',
    'mailin8r.com', 'mailnesia.com', 'mailnull.com',
    'mailtemp.net', 'makemetheking.com', 'mintemail.com',
    'mytemp.email', 'no-spam.ws', 'nodezine.com',
    'oneoffemail.com', 'postacin.com', 'receiveee.com',
    'ruffrey.com', 's0ny.net', 'safe-mail.net',
    'securesmtp.net', 'sneakemail.com', 'sofimail.com',
    'spamavert.com', 'spambox.info', 'spamcannon.com',
    'spamcannon.net', 'spamcon.org', 'spamcorptastic.com',
    'spamex.com', 'spamfree.eu', 'spamgoes.in',
    'spamhereplease.com', 'spamherelots.com', 'spaminator.de',
    'spaml.com', 'spaml.de', 'spamoff.de',
    'spamspot.com', 'spamthis.co.uk', 'spamthisplease.com',
    'speed.1s.fr', 'supergreatmail.com', 'superrito.com',
    'teleworm.com', 'teleworm.us', 'tempalias.com',
    'tempe-mail.com', 'tempemail.com', 'tempemail.net',
    'tempemailaddress.com', 'tempemail.co.za', 'tempmail.de',
    'tempmail.eu', 'tempmail2.com', 'tempmailaddress.com',
    'tempmailer.com', 'tempmailer.de', 'tempmailid.com',
    'tmail.ws', 'wegwerfmail.de', 'wegwerfmail.net',
    'wegwerfmail.org', 'wetrainbayarea.com', 'wetrainbayarea.org',
    'wh4f.org', 'whyspam.me', 'willselfdestruct.com',
    'xemaps.com', 'xents.com', 'yapped.net',
    'yeah.net', 'zippymail.info', 'zoemail.com',
    'zzz.com', 'emailfake.com', 'moakt.com',
    'getnowtoday.cf', 'rootfest.net', 'clrmail.com',
    'discard.email', 'inboxbear.com', 'tmailor.com',
    'armyspy.com', 'cuvox.de', 'dayrep.com',
    'einrot.com', 'fleckens.hu', 'gustr.com',
    'jourrapide.com', 'rhyta.com', 'superrito.com',
    'teleworm.com', 'spam4.me', 'fudgerub.com',
    'mt2009.com', 'thankyou2010.com', 'mt2014.com',
    'mailforspam.com', 'reallymymail.com', 'wp.pl',
    'abyssmail.com', 'beefmilk.com', 'binkmail.com',
    'bobmail.info', 'cultmovie.com', 'deadfake.cf',
    
    // Domains used by temp-mail.io and similar services
    '1secmail.com', '1secmail.net', '1secmail.org',
    'esiix.com', 'vjuum.com', 'laafd.com', 'txcct.com',
    'dpptd.com', '1mail.ml', 'wwjmp.com', 'icznn.com',
    'lnotu.com', 'qoika.com', 'wfgfm.com', 'lyghs.com',
    'oosln.com', 'iffmx.com', 'laafd.com', 'bnzvz.com',
    'qiott.com', 'vvice.com', 'ndybl.com', 'scrix.com',
    'rteet.com', 'euaqa.com', 'ezztt.com', 'fexbox.org',
    'fexbox.ru', 'fexpost.com', 'fextemp.com', 'chammy.info',
    'cmail.club', 'cmail.com', 'cmail.net', 'cmail.org',
    'cutout.club', 'disbox.net', 'disbox.org', 'eelmail.com',
    'gomail.in', 'haribu.net', 'instant-mail.de', 'jmail.ovh',
    'legalrc.loan', 'linshiyou.com', 'mail.tm', 'another.net',
    'mowgli.jungleheart.com', 'nextstep.com', 'pecinan.com',
    'pecinan.net', 'pecinan.org', 'robot-mail.com', 'robot-mail.net',
    'rspcwf.org', 'smapfree.net', 'snapmail.cc', 'tempr.email',
    'tmpbox.net', 'tmpmail.net', 'tmpmail.org', 'vipmail.eu',
    'vomoto.com', 'vssms.com', 'wuzup.net', 'wuzupmail.net',
    'yxzx.net', 'zetmail.com', 'zhorachu.com', 'trbvm.com',
    'yyolf.net', 'cloudns.asia', 'cloudns.club', 'cloudns.nz',
    'cloudns.cc', 'now.im', 'mailto.plus', 'fextemp.org',
    
    // More rotating temp-mail domains (frequently updated by services)
    'mrotzis.com', 'zudpck.com', 'wqmzd.com', 'rteet.com',
    'cndps.com', 'fdfgg.com', 'grfio.com', 'yjgoe.com',
    'kjkszpj.com', 'khtyj.com', 'ipoo.org', 'ycare.de',
    'emlhub.com', 'goteem.com', 'psnap.org', 'prtnx.com',
    'bareed.ws', 'turoid.com', 'trashmail.ws', 'uwork4.us',
    'ccino.com', 'rppkn.com', 'ezfill.com', 'flyspam.com',
    'barryogorman.com', 'baxomale.ht.cx', 'bodhy.com', 'bofthew.com',
    'brefmail.com', 'cavi.mx', 'correo.blogos.net', 'dacoolest.com',
    'dumpmail.com', 'dumpmail.de', 'e-mail.org', 'email60.com',
    'emailage.cf', 'emailage.ga', 'emailage.gq', 'emailage.ml',
    'emailage.tk', 'emaildienst.de', 'emailthe.net', 'emailtmp.com',
    'emeil.in', 'emeil.ir', 'emz.net', 'fakemail.fr',
    'filzmail.com', 'fr33mail.info', 'gishpuppy.com', 'grr.la',
    'hatespam.org', 'imgof.com', 'imgv.de', 'inpwa.com',
    'insorg-mail.info', 'ipoo.org', 'irish2me.com', 'iwi.net',
    'jdz.ro', 'jsrsolutions.com', 'kasmail.com', 'koszmail.pl',
    'kurzepost.de', 'lawlita.com', 'letthemeatspam.com', 'lhsdv.com',
    'lopl.co.cc', 'lr78.com', 'lroid.com', 'mailbidon.com',
    'maileater.com', 'mailimate.com', 'mailin8r.com', 'mailinator.org',
    'mailme.gq', 'mailme.ir', 'mailme.lv', 'mailmoat.com',
    'mailms.com', 'mailna.biz', 'mailna.co', 'mailna.in',
    'mailna.me', 'mailnator.com', 'mailnesia.com', 'mailpick.biz',
    'mailproxsy.com', 'mailquack.com', 'mailrock.biz', 'mailscrap.com',
    'mailseal.de', 'mailshell.com', 'mailsiphon.com', 'mailslapping.com',
    'mailslite.com', 'mailtemp.info', 'mailtothis.com', 'mailtv.net',
    'mailtv.tv', 'mailzi.ru', 'megago.tk', 'meltmail.com',
    'mierdamail.com', 'mintemail.com', 'moncourrier.fr.nf', 'monemail.fr.nf',
    'monmail.fr.nf', 'msa.minsmail.com', 'mt2009.com', 'mycleaninbox.net',
    'mypartyclip.de', 'myphantomemail.com', 'mysamp.de', 'mytempemail.com',
    'mytempmail.com', 'mytrashmail.com', 'neomailbox.com', 'nepwk.com',
    'nervmich.net', 'nervtmich.net', 'netmails.com', 'netmails.net',
    'neverbox.com', 'nice-4u.com', 'nobulk.com', 'noclickemail.com',
    'nogmailspam.info', 'nomail.pw', 'nomail.xl.cx', 'nomail2me.com',
    'nomorespamemails.com', 'nospam.ze.tc', 'nospam4.us', 'nospamfor.us',
    'nospammail.net', 'notmailinator.com', 'nowhere.org', 'nowmymail.com',
    'nwldx.com', 'objectmail.com', 'obobbo.com', 'odnorazovoe.ru',
    'oneoffemail.com', 'onewaymail.com', 'onlatedotcom.info', 'online.ms',
    'opayq.com', 'ordinaryamerican.net', 'otherinbox.com', 'ovpn.to',
    'owlpic.com', 'pancakemail.com', 'pimpedupmyspace.com', 'pjjkp.com',
    'plexolan.de', 'poczta.onet.pl', 'politikerclub.de', 'poofy.org',
    'pookmail.com', 'privacy.net', 'privatdemail.net', 'proxymail.eu',
    'prtnx.com', 'punkass.com', 'putthisinyourspamdatabase.com', 'qq.com',
    'quickinbox.com', 'quickmail.nl', 'rcpt.at', 'recode.me',
    
    // Additional comprehensive list
    'regbypass.com', 'regspam.com', 'rhyta.com', 'rklips.com',
    'rmqkr.net', 'rppkn.com', 's0ny.net', 'safaat.cf',
    'safaat.ga', 'safaat.gq', 'safaat.ml', 'safaat.tk',
    'saharanightstempe.com', 'sandelf.de', 'saynotospams.com', 'schrott-email.de',
    'selfdestructingmail.com', 'selfdestructingmail.org', 'sendspamhere.com', 'shieldemail.com',
    'shiftmail.com', 'shortmail.net', 'sibmail.com', 'sinda.club',
    'singlespride.com', 'skeefmail.com', 'slaskpost.se', 'slopsbox.com',
    'smashmail.de', 'smellfear.com', 'snakemail.com', 'sneakmail.de',
    'sofimail.com', 'sofort-mail.de', 'sogetthis.com', 'solvemail.info',
    'soodomail.com', 'spam.la', 'spam.su', 'spamail.de',
    'spambob.com', 'spambob.net', 'spambob.org', 'spambog.com',
    'spambog.de', 'spambog.ru', 'spamcannon.com', 'spamcannon.net',
    'spamcero.com', 'spamcon.org', 'spamcorptastic.com', 'spamday.com',
    'spamex.com', 'spamfighter.cf', 'spamfighter.ga', 'spamfighter.gq',
    'spamfighter.ml', 'spamfighter.tk', 'spamfree.eu', 'spamgoes.in',
    'spamherelots.com', 'spamhereplease.com', 'spamhole.com', 'spamify.com',
    'spaminator.de', 'spamkill.info', 'spaml.com', 'spaml.de',
    'spammotel.com', 'spamobox.com', 'spamoff.de', 'spamslicer.com',
    'spamspot.com', 'spamstack.net', 'spamthis.co.uk', 'spamthisplease.com',
    'spamtrail.com', 'spamtrap.co', 'spikio.com', 'spoofmail.de',
    'stuffmail.de', 'suioe.com', 'supergreatmail.com', 'supermailer.jp',
    'superrito.com', 'superstachel.de', 'suremail.info', 'sweetxxx.de',
    'tafmail.com', 'teewars.org', 'teleworm.com', 'teleworm.us',
    'temp-mail.com', 'temp-mail.de', 'temp-mail.org', 'temp-mail.ru',
    'tempemail.biz', 'tempemail.co.za', 'tempemail.com', 'tempemail.net',
    'tempinbox.co.uk', 'tempinbox.com', 'tempmail.co', 'tempmail.it',
    'tempmail.ws', 'tempmaildemo.com', 'tempmailer.com', 'tempmailer.de',
    'tempomail.fr', 'temporarily.de', 'temporarioemail.com.br', 'temporaryemail.net',
    'temporaryemail.us', 'temporaryforwarding.com', 'temporaryinbox.com', 'temporarymailaddress.com',
    'tempthe.net', 'thankyou2010.com', 'thc.st', 'thelimestones.com',
    'thisisnotmyrealemail.com', 'thismail.net', 'throam.com', 'throwawayemail.com',
    'throwawayemailaddress.com', 'tilien.com', 'tittbit.in', 'tizi.com',
    'tmail.com', 'tmail.ws', 'tmailinator.com', 'toiea.com',
    'tradermail.info', 'trash-amil.com', 'trash-mail.at', 'trash-mail.cf',
    'trash-mail.com', 'trash-mail.de', 'trash-mail.ga', 'trash-mail.gq',
    'trash-mail.ml', 'trash-mail.tk', 'trash2009.com', 'trash2010.com',
    'trash2011.com', 'trashdevil.com', 'trashdevil.de', 'trashemail.de',
    'trashemails.de', 'trashmail.at', 'trashmail.com', 'trashmail.de',
    'trashmail.me', 'trashmail.net', 'trashmail.org', 'trashmail.ws',
    'trashmailer.com', 'trashymail.com', 'trashymail.net', 'trialmail.de',
    'trillianpro.com', 'tryalert.com', 'turual.com', 'twinmail.de',
    'tyldd.com', 'uggsrock.com', 'umail.net', 'uroid.com',
    'us.af', 'venompen.com', 'veryrealemail.com', 'vidchart.com',
    'viditag.com', 'viewcastmedia.com', 'viewcastmedia.net', 'viewcastmedia.org',
    'vipxm.net', 'viralplays.com', 'vpn.st', 'vubby.com',
    'wasteland.rfc822.org', 'webemail.me', 'webm4il.info', 'webuser.in',
    'wee.my', 'weg-werf-email.de', 'wegwerf-email-addressen.de', 'wegwerf-emails.de',
    'wegwerfadresse.de', 'wegwerfemail.com', 'wegwerfemail.de', 'wegwerfmail.de',
    'wegwerfmail.info', 'wegwerfmail.net', 'wegwerfmail.org', 'wetrainbayarea.com',
    'wetrainbayarea.org', 'wh4f.org', 'whatiaas.com', 'whatpaas.com',
    'whyspam.me', 'wilemail.com', 'willhackforfood.biz', 'willselfdestruct.com',
    'winemaven.info', 'wronghead.com', 'wuzup.net', 'wuzupmail.net',
    'wwwnew.eu', 'xagloo.com', 'xemaps.com', 'xents.com',
    'xmaily.com', 'xoxy.net', 'yapped.net', 'yaqp.com',
    'yep.it', 'yogamaven.com', 'yopmail.com', 'yopmail.fr',
    'yopmail.net', 'youmailr.com', 'ypmail.webarnak.fr.eu.org', 'yuurok.com',
    'zehnminuten.de', 'zehnminutenmail.de', 'zippymail.info', 'zoaxe.com',
    'zoemail.com', 'zoemail.net', 'zomg.info', 'zweb.in'
];
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
    
    if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Please fill in all fields';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters long';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username can only contain letters, numbers, and underscores';
    } elseif (!validateEmail($email)) {
        $error = 'Invalid email format';
    } else {
        // Verify Turnstile token
        $turnstileResult = verifyTurnstile($turnstileToken, $_SERVER['REMOTE_ADDR']);
        if (!$turnstileResult['success']) {
            $error = 'Security verification failed. Please try again.';
        } else {
        // Whitelist approach - only allow trusted email providers
        $emailDomain = substr(strrchr($email, "@"), 1);
        $emailDomainLower = strtolower($emailDomain);
        
        // List of allowed email providers
        $allowedEmailProviders = [
            // Major providers
            'gmail.com', 'googlemail.com',
            'yahoo.com', 'yahoo.co.uk', 'yahoo.co.in', 'yahoo.fr', 'yahoo.de',
            'hotmail.com', 'hotmail.co.uk', 'hotmail.fr', 'hotmail.de',
            'outlook.com', 'outlook.fr', 'outlook.de', 'outlook.in',
            'live.com', 'live.co.uk', 'live.fr',
            'msn.com',
            
            // Other legitimate providers
            'aol.com', 'aol.co.uk',
            'icloud.com', 'me.com', 'mac.com',
            'protonmail.com', 'protonmail.ch', 'pm.me',
            'zoho.com', 'zohomail.com',
            'mail.com',
            'gmx.com', 'gmx.de', 'gmx.net',
            'yandex.com', 'yandex.ru',
            'mail.ru',
            'inbox.com',
            'fastmail.com',
            'tutanota.com', 'tutanota.de', 'tutamail.com',
            'hushmail.com',
            'runbox.com'
        ];
        
        $isAllowedEmail = in_array($emailDomainLower, $allowedEmailProviders);
        
        if (!$isAllowedEmail) {
            $error = 'Please use a valid email address from Gmail, Yahoo, Hotmail, Outlook, or other major email providers.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $error = 'Password must contain at least one uppercase letter';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $error = 'Password must contain at least one lowercase letter';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $error = 'Password must contain at least one number';
        } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $error = 'Password must contain at least one special character (!@#$%^&*())';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } else {
            $db = Database::getInstance();
            
            // Check if email or username already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'Email or username already registered';
            } else {
            // Create new user
            $hashedPassword = password_hash($password, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
            $stmt = $db->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, 'user')");
            $stmt->bind_param("ssss", $name, $username, $email, $hashedPassword);
            
            if ($stmt->execute()) {
                // Send welcome email
                $siteName = getSetting('site_name', 'Rangpur Food');
                $subject = "Welcome to $siteName! 🎉";
                
                $message = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <style>
                        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                        .container { max-width: 600px; margin: 30px auto; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
                        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center; color: white; }
                        .header h1 { margin: 0; font-size: 32px; font-weight: 700; }
                        .header p { margin: 10px 0 0; opacity: 0.95; font-size: 16px; }
                        .content { padding: 40px 30px; }
                        .content h2 { color: #333; font-size: 24px; margin: 0 0 20px; }
                        .content p { color: #555; line-height: 1.8; font-size: 16px; margin: 0 0 15px; }
                        .credentials { background: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 25px 0; border-radius: 8px; }
                        .credentials p { margin: 5px 0; font-size: 15px; color: #333; }
                        .credentials strong { color: #667eea; }
                        .button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 30px; margin: 20px 0; font-weight: 600; font-size: 16px; box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3); }
                        .features { display: table; width: 100%; margin: 30px 0; }
                        .feature { display: table-row; }
                        .feature-icon { display: table-cell; width: 40px; padding: 10px 0; vertical-align: top; }
                        .feature-text { display: table-cell; padding: 10px 0 10px 15px; vertical-align: top; color: #555; }
                        .footer { background: #f8f9fa; padding: 30px; text-align: center; color: #888; font-size: 14px; }
                        .footer p { margin: 5px 0; }
                        .social { margin: 20px 0; }
                        .social a { display: inline-block; margin: 0 10px; color: #667eea; text-decoration: none; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>🎉 Welcome to $siteName!</h1>
                            <p>Your account has been successfully created</p>
                        </div>
                        
                        <div class='content'>
                            <h2>Hello $name! 👋</h2>
                            <p>Thank you for joining our community! We're thrilled to have you on board.</p>
                            
                            <div class='credentials'>
                                <p><strong>📧 Email:</strong> $email</p>
                                <p><strong>👤 Username:</strong> $username</p>
                            </div>
                            
                            <p>Your account is now active and you can start exploring all our amazing features!</p>
                            
                            <div style='text-align: center;'>
                                <a href='" . SITE_URL . "/login.php' class='button'>Login to Your Account →</a>
                            </div>
                            
                            <div class='features'>
                                <div class='feature'>
                                    <div class='feature-icon'>🛍️</div>
                                    <div class='feature-text'><strong>Browse Products</strong> - Explore our wide range of quality products</div>
                                </div>
                                <div class='feature'>
                                    <div class='feature-icon'>🏷️</div>
                                    <div class='feature-text'><strong>Exclusive Deals</strong> - Access member-only discounts and offers</div>
                                </div>
                                <div class='feature'>
                                    <div class='feature-icon'>🎯</div>
                                    <div class='feature-text'><strong>Track Orders</strong> - Monitor your purchases in real-time</div>
                                </div>
                                <div class='feature'>
                                    <div class='feature-icon'>💬</div>
                                    <div class='feature-text'><strong>24/7 Support</strong> - We're here to help whenever you need</div>
                                </div>
                            </div>
                            
                            <p style='color: #888; font-size: 14px; margin-top: 30px;'>
                                If you didn't create this account, please ignore this email or contact our support team.
                            </p>
                        </div>
                        
                        <div class='footer'>
                            <p><strong>$siteName</strong></p>
                            <p>Thanks for choosing us!</p>
                            <div class='social'>
                                <a href='#'>Facebook</a> | 
                                <a href='#'>Twitter</a> | 
                                <a href='#'>Instagram</a>
                            </div>
                            <p style='font-size: 12px; color: #aaa; margin-top: 15px;'>
                                © " . date('Y') . " $siteName. All rights reserved.
                            </p>
                        </div>
                    </div>
                </body>
                </html>
                ";
                
                sendEmail($email, $subject, $message);
                
                redirect(SITE_URL . '/login.php?registered=1');
            } else {
                $error = 'Registration failed. Please try again.';
            }
            }
        }
        }
    }
}

$pageTitle = "Sign Up";
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 40vh; display: flex; align-items: center;">
    <div class="container text-center text-white">
        <div class="animate__animated animate__fadeInDown">
            <i class="fas fa-user-plus fa-4x mb-3" style="opacity: 0.9;"></i>
            <h1 class="display-4 fw-bold mb-3">Join Our Community</h1>
            <p class="lead mb-0">Create your account and start your journey with us</p>
        </div>
    </div>
</section>

<div class="container py-5" style="margin-top: -80px;">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0 shadow-lg animate__animated animate__fadeInUp" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-4 p-md-5">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-outline mb-3">
                            <input type="text" id="name" name="name" class="form-control form-control-lg" 
                                   value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            <label class="form-label" for="name">Full Name</label>
                        </div>
                        
                        <div class="form-outline mb-3">
                            <input type="text" id="username" name="username" class="form-control form-control-lg" 
                                   value="<?php echo htmlspecialchars($username ?? ''); ?>" required 
                                   pattern="[a-zA-Z0-9_]{3,}" 
                                   title="Username must be at least 3 characters and contain only letters, numbers, and underscores">
                            <label class="form-label" for="username">Username</label>
                        </div>
                        
                        <div class="form-outline mb-3">
                            <input type="email" id="email" name="email" class="form-control form-control-lg" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            <label class="form-label" for="email">Email Address</label>
                        </div>
                        
                        <div class="form-outline mb-3 position-relative">
                            <input type="password" id="password" name="password" class="form-control form-control-lg" required style="padding-right: 45px;">
                            <label class="form-label" for="password">Password</label>
                            <button type="button" class="btn btn-link password-toggle position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); z-index: 10; text-decoration: none;" onclick="togglePassword('password')">
                                <i class="fas fa-eye-slash" id="password-icon"></i>
                            </button>
                        </div>
                        
                        <div class="form-outline mb-3 position-relative">
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   class="form-control form-control-lg" required style="padding-right: 45px;">
                            <label class="form-label" for="confirm_password">Confirm Password</label>
                            <button type="button" class="btn btn-link password-toggle position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); z-index: 10; text-decoration: none;" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye-slash" id="confirm_password-icon"></i>
                            </button>
                        </div>
                        
                        <!-- Password Strength Indicator -->
                        <div id="password-strength" class="mb-3" style="display: none;">
                            <div class="progress mb-2" style="height: 8px;">
                                <div id="strength-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small id="strength-text" class="text-muted">Password Strength: <span id="strength-label">-</span></small>
                        </div>
                        
                        <!-- Password Requirements -->
                        <div class="card mb-3" style="background: #f8f9fa; border: none;">
                            <div class="card-body p-3">
                                <small class="text-muted d-block mb-2"><strong>Password Requirements:</strong></small>
                                <div class="password-requirements" style="font-size: 0.85rem;">
                                    <div id="req-length" class="requirement-item mb-1">
                                        <i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i>
                                        <span>At least 8 characters</span>
                                    </div>
                                    <div id="req-uppercase" class="requirement-item mb-1">
                                        <i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i>
                                        <span>One uppercase letter (A-Z)</span>
                                    </div>
                                    <div id="req-lowercase" class="requirement-item mb-1">
                                        <i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i>
                                        <span>One lowercase letter (a-z)</span>
                                    </div>
                                    <div id="req-number" class="requirement-item mb-1">
                                        <i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i>
                                        <span>One number (0-9)</span>
                                    </div>
                                    <div id="req-special" class="requirement-item mb-1">
                                        <i class="fas fa-circle text-muted me-2" style="font-size: 0.5rem;"></i>
                                        <span>One special character (!@#$%^&*)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-primary">Terms and Conditions</a>
                            </label>
                        </div>
                        
                        <!-- Cloudflare Turnstile -->
                        <div class="mb-4 d-flex justify-content-center">
                            <?php echo getTurnstileWidget(); ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" style="border-radius: 10px; padding: 15px;">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </form>
                    
                    <!-- Divider -->
                    <div class="d-flex align-items-center my-4">
                        <hr class="flex-grow-1">
                        <span class="px-3 text-muted">OR</span>
                        <hr class="flex-grow-1">
                    </div>
                    
                    <!-- Google Signup Button -->
                    <a href="<?php echo getGoogleLoginUrl(); ?>" class="btn btn-outline-dark btn-lg w-100 mb-4 d-flex align-items-center justify-content-center" style="border-radius: 10px; padding: 15px;">
                        <svg width="20" height="20" class="me-3" viewBox="0 0 48 48">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                            <path fill="none" d="M0 0h48v48H0z"></path>
                        </svg>
                        Sign up with Google
                    </a>
                    
                    <div class="text-center">
                        <p class="mb-0 text-muted">Already have an account? 
                            <a href="<?php echo SITE_URL; ?>/login.php" class="text-primary fw-bold text-decoration-none">Login here</a>
                        </p>
                    </div>
                    
                    <!-- Features -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="row text-center g-3">
                            <div class="col-4">
                                <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                                <p class="small mb-0 text-muted">Secure</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-bolt fa-2x text-warning mb-2"></i>
                                <p class="small mb-0 text-muted">Fast</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <p class="small mb-0 text-muted">Verified</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Benefits -->
            <div class="row g-3 mt-4">
                <div class="col-md-6">
                    <a href="<?php echo SITE_URL; ?>/products.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInLeft" style="border-radius: 15px;">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-gift fa-3x text-primary mb-3"></i>
                                <h6 class="fw-bold">Exclusive Deals</h6>
                                <p class="small text-muted mb-0">Get access to member-only discounts</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInRight" style="border-radius: 15px;">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-headset fa-3x text-success mb-3"></i>
                                <h6 class="fw-bold">24/7 Support</h6>
                                <p class="small text-muted mb-0">We're here to help anytime</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
/* Fix autofill styling */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 30px white inset !important;
    -webkit-text-fill-color: #000 !important;
    transition: background-color 5000s ease-in-out 0s;
}

/* Fix floating labels with autofill */
.form-outline input:-webkit-autofill ~ label,
.form-outline input:not(:placeholder-shown) ~ label {
    transform: translateY(-1rem) translateY(0.1rem) scale(0.8);
    background: white;
    padding: 0 0.5rem;
}

/* Ensure labels are properly positioned */
.form-outline {
    position: relative;
}

.form-outline label {
    position: absolute;
    top: 0.5rem;
    left: 0.75rem;
    transition: all 0.2s ease;
    pointer-events: none;
    color: #6c757d;
    z-index: 1;
}

/* Active label state */
.form-outline label.active,
.form-outline input:focus ~ label,
.form-outline input:not(:placeholder-shown) ~ label,
.form-outline input.active ~ label {
    transform: translateY(-1.5rem) scale(0.85);
    background: white;
    padding: 0 0.5rem;
    color: #667eea;
}

.form-outline input {
    padding: 0.75rem;
}

.form-outline input:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    border-color: #667eea;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.card {
    transition: transform 0.3s ease;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

/* Password strength indicator */
#password {
    transition: border-color 0.3s ease;
}

.animate__animated {
    animation-duration: 0.8s;
}

/* Social buttons */
.btn-outline-danger, .btn-outline-primary, .btn-outline-info {
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.btn-outline-primary:hover {
    background-color: #1877f2;
    border-color: #1877f2;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(24, 119, 242, 0.3);
}

.btn-outline-info:hover {
    background-color: #1da1f2;
    border-color: #1da1f2;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(29, 161, 242, 0.3);
}

/* Password Toggle Button */
.password-toggle {
    color: #6c757d;
    padding: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.password-toggle:hover {
    color: #667eea;
    transform: translateY(-50%) scale(1.1);
}

.password-toggle i {
    transition: all 0.4s ease;
}

.password-toggle.active i {
    animation: eyeBlink 0.3s ease;
}

@keyframes eyeBlink {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(0.8); }
}

/* Visual Effects */
/* Animated gradient background */
body {
    position: relative;
    overflow-x: hidden;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    opacity: 0.03;
    z-index: -1;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Floating shapes */
.floating-shapes {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    overflow: hidden;
    z-index: -1;
    pointer-events: none;
}

.shape {
    position: absolute;
    opacity: 0.1;
    animation: float 20s infinite ease-in-out;
}

.shape-1 {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.shape-2 {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #764ba2, #667eea);
    border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
    top: 70%;
    left: 80%;
    animation-delay: 2s;
}

.shape-3 {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    top: 40%;
    left: 85%;
    animation-delay: 4s;
}

.shape-4 {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #764ba2, #667eea);
    border-radius: 20% 80% 80% 20% / 20% 20% 80% 80%;
    top: 80%;
    left: 15%;
    animation-delay: 6s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-30px) rotate(180deg);
    }
}

/* Card entrance animation */
.card {
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Input focus glow effect */
.form-outline input:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25),
                0 0 20px rgba(102, 126, 234, 0.2);
    border-color: #667eea;
    animation: inputGlow 0.3s ease;
}

@keyframes inputGlow {
    0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
    100% { box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25), 0 0 20px rgba(102, 126, 234, 0.2); }
}

/* Button ripple effect */
.btn-primary {
    position: relative;
    overflow: hidden;
}

.btn-primary::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-primary:hover::after {
    width: 300px;
    height: 300px;
}

/* Label animation */
.form-outline label {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-outline input:focus ~ label,
.form-outline label.active {
    animation: labelFloat 0.3s ease;
}

@keyframes labelFloat {
    0% {
        transform: translateY(0) scale(1);
    }
    100% {
        transform: translateY(-1.5rem) scale(0.85);
    }
}

/* Card hover lift effect */
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
}

/* Progress bar animation */
.progress-bar {
    transition: width 0.4s ease, background-color 0.4s ease;
}

/* Requirement check animation */
.requirement-item {
    transition: all 0.3s ease;
}

.requirement-item i {
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

/* Hero section pulse */
.animate__animated {
    animation-duration: 1s;
}

/* Icon pulse on hover */
.fas:hover {
    animation: iconPulse 0.5s ease;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Alert slide in */
.alert {
    animation: slideInDown 0.4s ease;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Feature Cards Active Effects */
.row.g-3.mt-4 .card {
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    background: #fff;
    position: relative;
    overflow: hidden;
}

.row.g-3.mt-4 .card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 50px rgba(102, 126, 234, 0.25) !important;
    border-color: #667eea;
}

.row.g-3.mt-4 .card:active {
    transform: translateY(-8px) scale(1.01);
}

.row.g-3.mt-4 .card i {
    transition: all 0.4s ease;
}

.row.g-3.mt-4 .card:hover i {
    transform: scale(1.2) rotateY(180deg);
}

.row.g-3.mt-4 .card h6 {
    transition: color 0.3s ease;
}

.row.g-3.mt-4 .card:hover h6 {
    color: #667eea;
}

.row.g-3.mt-4 .card p {
    transition: color 0.3s ease;
}

.row.g-3.mt-4 .card:hover p {
    color: #764ba2;
}

/* Add shimmer effect on hover */
.row.g-3.mt-4 .card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.2), transparent);
    transition: left 0.5s;
}

.row.g-3.mt-4 .card:hover::before {
    left: 100%;
}

/* Icon container glow */
.row.g-3.mt-4 .card:hover .fa-gift,
.row.g-3.mt-4 .card:hover .fa-headset {
    text-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
}

/* Link styling for cards */
.row.g-3.mt-4 a {
    display: block;
    text-decoration: none !important;
}

.row.g-3.mt-4 a .card {
    transition: all 0.4s ease;
}
</style>

<!-- Floating Shapes -->
<div class="floating-shapes">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <div class="shape shape-4"></div>
</div>

<script>
// Password Toggle Function
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    const button = icon.closest('.password-toggle');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        button.classList.add('active');
        
        // Animation
        setTimeout(() => button.classList.remove('active'), 300);
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        button.classList.add('active');
        
        // Animation
        setTimeout(() => button.classList.remove('active'), 300);
    }
}

// Password Strength Checker
function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthBar = document.getElementById('strength-bar');
    const strengthLabel = document.getElementById('strength-label');
    const strengthContainer = document.getElementById('password-strength');
    
    // Requirements elements
    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqLowercase = document.getElementById('req-lowercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');
    
    // Check requirements
    const hasLength = password.length >= 8;
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecial = /[^A-Za-z0-9]/.test(password);
    
    // Update requirement indicators
    updateRequirement(reqLength, hasLength);
    updateRequirement(reqUppercase, hasUppercase);
    updateRequirement(reqLowercase, hasLowercase);
    updateRequirement(reqNumber, hasNumber);
    updateRequirement(reqSpecial, hasSpecial);
    
    // Calculate strength
    let strength = 0;
    if (hasLength) strength++;
    if (hasUppercase) strength++;
    if (hasLowercase) strength++;
    if (hasNumber) strength++;
    if (hasSpecial) strength++;
    
    // Show/hide strength bar
    if (password.length > 0) {
        strengthContainer.style.display = 'block';
    } else {
        strengthContainer.style.display = 'none';
        return;
    }
    
    // Update strength bar
    const percentage = (strength / 5) * 100;
    strengthBar.style.width = percentage + '%';
    
    // Update color and label based on strength
    strengthBar.classList.remove('bg-danger', 'bg-warning', 'bg-info', 'bg-success');
    
    if (strength <= 2) {
        strengthBar.classList.add('bg-danger');
        strengthLabel.textContent = 'Weak';
        strengthLabel.style.color = '#dc3545';
    } else if (strength === 3) {
        strengthBar.classList.add('bg-warning');
        strengthLabel.textContent = 'Fair';
        strengthLabel.style.color = '#ffc107';
    } else if (strength === 4) {
        strengthBar.classList.add('bg-info');
        strengthLabel.textContent = 'Good';
        strengthLabel.style.color = '#0dcaf0';
    } else {
        strengthBar.classList.add('bg-success');
        strengthLabel.textContent = 'Strong';
        strengthLabel.style.color = '#28a745';
    }
}

function updateRequirement(element, isMet) {
    const icon = element.querySelector('i');
    const text = element.querySelector('span');
    
    if (isMet) {
        icon.classList.remove('fa-circle', 'text-muted');
        icon.classList.add('fa-check-circle', 'text-success');
        text.style.color = '#28a745';
        text.style.fontWeight = '500';
    } else {
        icon.classList.remove('fa-check-circle', 'text-success');
        icon.classList.add('fa-circle', 'text-muted');
        text.style.color = '';
        text.style.fontWeight = '';
    }
}

// Initialize Material Design form inputs
document.addEventListener('DOMContentLoaded', function() {
    // Add password strength checking
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', checkPasswordStrength);
        passwordInput.addEventListener('keyup', checkPasswordStrength);
    }
    // Initialize all form inputs with MDB
    const inputs = document.querySelectorAll('.form-outline input');
    inputs.forEach(input => {
        // Check if input has value (including autofill)
        function checkInput() {
            if (input.value !== '' || input.matches(':autofill')) {
                input.classList.add('active');
                const label = input.nextElementSibling;
                if (label && label.classList.contains('form-label')) {
                    label.classList.add('active');
                }
            }
        }
        
        // Check on load
        checkInput();
        
        // Check on input change
        input.addEventListener('input', checkInput);
        input.addEventListener('change', checkInput);
        
        // Handle focus
        input.addEventListener('focus', function() {
            const label = this.nextElementSibling;
            if (label && label.classList.contains('form-label')) {
                label.classList.add('active');
            }
        });
        
        // Handle blur
        input.addEventListener('blur', function() {
            if (this.value === '' && !this.matches(':autofill')) {
                const label = this.nextElementSibling;
                if (label && label.classList.contains('form-label')) {
                    label.classList.remove('active');
                }
            }
        });
    });
    
    // Check for autofill after a delay
    setTimeout(() => {
        inputs.forEach(input => {
            if (input.matches(':autofill') || input.value !== '') {
                input.classList.add('active');
                const label = input.nextElementSibling;
                if (label && label.classList.contains('form-label')) {
                    label.classList.add('active');
                }
            }
        });
    }, 100);
});

// Password strength indicator
document.getElementById('password')?.addEventListener('input', function() {
    const password = this.value;
    const strength = password.length >= 8 ? 'strong' : password.length >= 6 ? 'medium' : 'weak';
    
    if (password.length > 0) {
        if (strength === 'strong') {
            this.style.borderColor = '#28a745';
        } else if (strength === 'medium') {
            this.style.borderColor = '#ffc107';
        } else {
            this.style.borderColor = '#dc3545';
        }
    } else {
        this.style.borderColor = '';
    }
});

// Password match indicator
document.getElementById('confirm_password')?.addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword.length > 0) {
        if (password === confirmPassword) {
            this.style.borderColor = '#28a745';
        } else {
            this.style.borderColor = '#dc3545';
        }
    } else {
        this.style.borderColor = '';
    }
});

// Form animation on submit
document.querySelector('form')?.addEventListener('submit', function(e) {
    const button = this.querySelector('button[type="submit"]');
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating Account...';
    button.disabled = true;
});

</script>

<?php require_once 'includes/footer.php'; ?>