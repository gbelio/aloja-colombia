<?php

namespace App\Listeners;

use DB;
use Illuminate\Mail\Events\MessageSending;
use App\Correo;

// Basado en https://github.com/shvetsgroup/laravel-email-database-correo

class EmailLogger
{
    /**
     * Handle the event.
     *
     * @param MessageSending $event
     */
    public function handle(MessageSending $event)
    {
       $message = $event->message;

       $correo = new Correo();
       $correo->date = date('Y-m-d H:i:s');
       $correo->from = $this->formatAddressField($message, 'From');
       $correo->to = $this->formatAddressField($message, 'To');
       $correo->cc = $this->formatAddressField($message, 'Cc');
       $correo->bcc = $this->formatAddressField($message, 'Bcc');
       $correo->subject = $message->getSubject();
       if ( strlen($message->getBody()) > 15000 ) {
        $correo->body = "El contenido del cuerpo del mensaje es demasiado amplio para ser visualizado."; 
       }
       else {
        $correo->body = $message->getBody();        
       }
       $correo->headers = (string)$message->getHeaders();
       //$correo->attachments = $message->getChildren() ? implode("\n\n", $message->getChildren()) : null;
       $correo->save();

    }

    /**
     * Format address strings for sender, to, cc, bcc.
     *
     * @param $message
     * @param $field
     * @return null|string
     */
    function formatAddressField($message, $field)
    {
        $headers = $message->getHeaders();

        if (!$headers->has($field)) {
            return null;
        }

        $mailboxes = $headers->get($field)->getFieldBodyModel();

        $strings = [];
        foreach ($mailboxes as $email => $name) {
            $mailboxStr = $email;
            if (null !== $name) {
                $mailboxStr = $name . ' <' . $mailboxStr . '>';
            }
            $strings[] = $mailboxStr;
        }
        return implode(', ', $strings);
    }
}