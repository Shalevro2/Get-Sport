<?php

namespace App;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
/**
 * Mail
 *
 */
class Mail
{

    /**
     * Send a message
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     *
     * @return void
     */
    public static function send($to, $subject, $text, $html)
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSercure = 'tls';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->Username = 'GetSportISR@gmail.com';
        $mail->Password = '315725119';
        $mail->SetFrom('no-reply@GetSport.com', 'GetSport');
        
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->Body .= 'GetSport';
        
        $mail->isHTML(true);
        $mail->AddAddress($to);

        $mail->send();
    }
}
