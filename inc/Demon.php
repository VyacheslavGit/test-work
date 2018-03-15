<?php

class Demon
{
    const API_URL = 'https://syn.su/testwork.php';

    const WAIT_PERIOD = 3600;

    // Response error codes
    const ERROR_MESSAGE_DECRYPT = 10;
    const ERROR_INVALID_METHOD = 15;
    const ERROR_EMPTY_MESSAGE = 20;

    /**
     * Response error messages list
     *
     * @var array
     */
    private $responseErrorsList = array(
        self::ERROR_MESSAGE_DECRYPT => 'Не получилось расшифровать строку',
        self::ERROR_INVALID_METHOD => 'Нет такого метода',
        self::ERROR_EMPTY_MESSAGE => 'Пустое значение параметра message'
    );

    private $timeStart = null;
    private $executionTime = null;

    private $stopDemon = false;

    private $sender = null;
    private $notifier = null;


    /**
     * Demon constructor.
     */
    public function __construct(ISender $sender, INotifier $notifier)
    {
        $this->timeStart = microtime(true);

        $this->sender = $sender;
        $this->notifier = $notifier;
    }

    /**
     * Run demon
     */
    public function run()
    {
        // send request to get method
        $response = $this->sendRequest(self::API_URL, array(
            'method' => 'get'
        ));

        $message = isset($response['response']['message']) ? $response['response']['message'] : null;
        $key = isset($response['response']['key']) ? $response['response']['key'] : null;

        $updateMessage = base64_encode($this->encryptXor($message, $key));

        while (!$this->stopDemon) {

            // send request to update method
            $response = $this->sendRequest(self::API_URL, array(
                'method' => 'update',
                'message' => $updateMessage
            ));

            if ( !$this->isSuccessResponse($response)) {
                $this->stop();

                $errorCode = isset($response['errorCode']) ? $response['errorCode'] : null;
                $errorMessage = $this->getResponseErrorMessage($errorCode);

                $this->errorNotify($errorCode, $errorMessage);

                $this->showError($errorMessage);
            }

            sleep(self::WAIT_PERIOD);
        }
    }

    /**
     * Send request
     *
     * @param $url
     * @param array $data
     * @return mixed
     */
    private function sendRequest($url, $data = array())
    {
        return $this->sender->sendRequest($url, $data);
    }

    /**
     * XOR encrypt text
     *
     * @param $text
     * @param $key
     * @return string
     */
    private function encryptXor($text, $key)
    {
        $textLenght = mb_strlen($text);

        $key = mb_substr($key, 0, $textLenght);
        $keyLenght = mb_strlen($key);

        $result = '';
        for ($i = 0; $i < $textLenght; $i++) {
            $result .= $text[$i] ^ $key[$i % $keyLenght];
        }
        return $result;
    }

    /**
     * Check is success response for update method
     *
     * @param $response
     * @return bool
     */
    private function isSuccessResponse($response)
    {
        if (isset($response['response']) && ($response['response'] === 'Success')) {
            return true;
        }

        return false;
    }

    /**
     * Stop demon progressing
     */
    private function stop()
    {
        $this->stopDemon = true;
        $this->setExecutionTime();
    }

    /**
     * Get error message by response error code
     *
     * @param $errorCode
     * @return mixed|string
     */
    private function getResponseErrorMessage($errorCode)
    {
        $errorMessage = '';
        if (isset($this->responseErrorsList[$errorCode])) {
            $errorMessage = $this->responseErrorsList[$errorCode];
        }

        return $errorMessage;
    }

    /**
     * Notify about error
     *
     * @param $errorCode
     * @param $errorMessage
     * @return bool
     */
    private function errorNotify($errorCode, $errorMessage)
    {
        $data = array(
            'Code' => $errorCode,
            'Message' => $errorMessage,
            'Demon execution time' => sprintf('%d sec', $this->getExecutionTime())
        );

        return $this->notifier->notify('Update method error', $data);
    }

    /**
     * Show error
     *
     * @param $errorMessage
     */
    private function showError($errorMessage)
    {
        $message = sprintf('Error: %s', $errorMessage) . PHP_EOL;
        die($message);
    }

    /**
     * Get demon execution time
     *
     * @return null
     */
    private function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     * Set demon execution time
     *
     * @return null
     */
    private function setExecutionTime()
    {
        $this->executionTime = round(microtime(true) - $this->timeStart);
    }

    /**
     * Demon destructor
     */
    public function __destruct()
    {
        die('Execution time: ' . $this->getExecutionTime() . ' sec.' . PHP_EOL);
    }
}