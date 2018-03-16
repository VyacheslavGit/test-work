<?php

/**
 * Class EmailNotifier
 */
class EmailNotifier implements INotifier
{
    public const NOTIFY_EMAIL = 'v.shcherrbyna@andersenlab.com';

    /**
     * Notify to email
     *
     * @param string $subject
     * @param array $data
     * @return bool
     */
    public function notify(string $subject, array $data = []): bool
    {
        return mail(self::NOTIFY_EMAIL, $subject, $this->getBodyMessage($data));
    }

    /**
     * Get email message body
     *
     * @param array $data
     * @return string
     */
    private function getBodyMessage(array $data): string
    {
        $body = '';
        foreach ($data as $key => $value) {
            $body .= "$key: $value\n";
        }

        return $body;
    }
}

