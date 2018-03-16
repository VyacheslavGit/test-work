<?php

interface ISender
{
    public function sendRequest(string $url, array $data = []);
}

