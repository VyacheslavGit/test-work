<?php

interface INotifier
{
    public function notify(string $subject, array $data = []): bool;
}

