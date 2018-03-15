<?php

class EmailNotifier implements INotifier
{
    const NOTIFY_EMAIL = 'v.shcherrbyna@andersenlab.com';

    public function notify($subject, $data = array())
    {
         return mail(self::NOTIFY_EMAIL, $subject, $this->getBodyMessage($data));
    }

    /**
     * Get email message body
     * @param $data
     * @return string
     */
    private function getBodyMessage($data)
    {

        $body = '';
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $body .= "$key: $value\n";
            }
        }

        return $body;
    }
}

