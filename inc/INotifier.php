<?php

interface INotifier
{
    public function notify($subject, $data = array());
}

