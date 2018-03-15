<?php

interface ISender
{
    public function sendRequest($url, $data = array());
}

