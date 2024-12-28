<?php

namespace App\WhatsApp;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverWait;
use Illuminate\Support\Facades\Storage;

/**
 * Whatsapp Login
 * Class path 'App\Whatsapp\WhatsAppLogin'
 */
class WhatsAppLogin
{
    /**
     * The server url where browser will be run
     * 
     * @var string $server_url
     */
    public string $server_url;

    /**
     * The path where browser screen shots will be saved
     * 
     * @var string $screenshot_directory
     */
    public string $screenshot_directory;

    /**
     * The instance of Facebook\WebDriver\Remote\RemoteWebDriver
     * 
     * @var Facebook\WebDriver\Remote\RemoteWebDriver $driver
     */
    public $driver;

    /**
     * The recipients to be sent messages
     * 
     * @var array $recipients
     */
    public array $recipients;

    /**
     * The message to be sent
     * 
     * @var object $message
     */
    public object $message;

    /**
     * The code from successful login with phone number
     * 
     * @var string $login_with_phone_number_code
     */
    public string $login_with_phone_number_code;

    /**
     * Create a new WhatsAppLogin instance.
     * @param void|string $server_url
     * @return WhatsAppLogin
     */
    public function __construct(string $server_url = 'http://localhost:9515') 
    {
        $this->message = new \stdClass();
        $this->recipients = [];
        $this->server_url = $server_url;
        $this->screenshot_directory = storage_path('app/public/images/screenshots');
        if (!is_dir($this->screenshot_directory)) { mkdir($this->screenshot_directory, 0777, true); }
    }

    /**
     * Set the recipients
     * @param array $recipients
     * @return WhatsAppLogin
     */
    public function setRecipients(array $recipients) 
    {
        $this->recipients = $recipients;
        return $this;
    }

    /**
     * Set the message
     * @param string $text
     * @return WhatsAppLogin
     */
    public function setTextMessage(string $text) 
    {
        $this->message->type = "TEXT";
        $this->message->text = $text;
        return $this;
    }

    /**
     * Set the message
     * @param string $text
     * @param array $media
     * @return WhatsAppLogin
     */
    public function setMediaAndMessage(string $text, array $media) 
    {
        $this->message->type = "MEDIA";
        $this->message->text = $text;
        $this->message->attributes = $media;
        return $this;
    }

    /**
     * Set the message
     * @param string $text
     * @param array $documents
     * @return WhatsAppLogin
     */
    public function setDocumentAndMessage(string $text, array $document) 
    {
        $this->message->type = "DOCUMENT";
        $this->message->text = $text;
        $this->message->attributes = $document;
        return $this;
    }

    /**
     * Set the message
     * @param string $text
     * @param string $question
     * @param array $options
     * @return WhatsAppLogin
     */
    public function setPollAndMessage(string $text, string $question, array $options) 
    {
        $this->message->type = "POLL";
        $this->message->text = $text;
        $this->message->question = $question;
        $this->message->options = $options;
        return $this;
    }

    /**
     * Open browser browser
     * @param void
     * @return WhatsAppLogin
     */
    public function openBrowserSession()
    {
        // Set up the desired capabilities (e.g., for Chrome)
        $capabilities = DesiredCapabilities::chrome();

        // Open an existing session
        $driver = RemoteWebDriver::create($this->server_url,$capabilities);

        // Store driver instance
        $this->driver = $driver;

        // Return class instance
        return $this;
    }

    /**
     * Continue and existing browser session using session id
     * @param string $session_id
     * @return WhatsAppLogin
     */
    public function continueBrowserSession(string $session_id)
    {
        // Set up the desired capabilities (e.g., for Chrome)
        $capabilities = DesiredCapabilities::chrome();

        // Open an existing session
        $driver = RemoteWebDriver::createBySessionID($session_id, $this->server_url, null, null, true, $capabilities);

        // Store driver instance
        $this->driver = $driver;

        // Return class instance
        return $this;
    }

    /**
     * Get the browser session id
     * @param void
     * @return string
     */
    public function getBrowserSessionId() {
        return $this->driver->getSessionID();
    }

    /**
     * Get the browser instance
     * @param void
     * @return string
     */
    public function getBrowserInstance() {
        return $this->driver;
    }

    /**
     * Get the code from successful login with phone number
     * @param void
     * @return string
     */
    public function getLoginWithPhoneNumberCode() {
        return $this->login_with_phone_number_code ?? null;
    }

    /**
     * Login to whatsapp with QR code
     * @param string|null $unique_identifier
     * @return WhatsAppLogin
     */
    public function LoginWithQRCode(?string $unique_identifier = null)
    {
        // Retrieve driver instance
        $driver = $this->driver;

        // Unique identifier
        $unique_identifier = $unique_identifier ?? uniqid();

        // Go to URL
        $driver->get('https://web.whatsapp.com/');

        // Wait until QR code is visible or chat button is visible
        $driver->wait()->until(
            function () use ($driver) {

                $element1 = $driver->findElements(WebDriverBy::cssSelector('[data-testid="qrcode"]'));
                $element2 = $driver->findElements(WebDriverBy::cssSelector('[data-testid="chat"]'));

                return (!$element1 && !$element2)? false : true;
            }
        );

        try {

            // Find QR code
            $driver->findElement(WebDriverBy::cssSelector('[data-testid="qrcode"]'));

            // Continually snapshot
            do {

                // Take a screenshot of the entire page
                $screenshot = $driver->takeScreenshot();

                // Create a new image resource from the screenshot
                $image = imagecreatefromstring($screenshot);

                // Crop the image to the element's location and size
                $croppedImage = imagecrop($image, ['x' => 1510, 'y' => 280, 'width' => 600, 'height' => 600]);

                // Generate a unique filename
                $filename = $unique_identifier.'.png';

                // Save the cropped image to a file
                imagepng($croppedImage, $this->screenshot_directory.'/'.$filename);

                // Wait for 9 second before continuing
                sleep(9);

            } while ( !empty($driver->findElements(WebDriverBy::cssSelector('[data-testid="qrcode"]'))) );

        } catch (\Throwable $th) {

            // Find QR code
            $driver->findElement(WebDriverBy::cssSelector('[data-testid="chat"]'));
        }

        // Store driver instance
        $this->driver = $driver;

        // Return class instance
        return $this;
    }

    /**
     * Login to whatsapp with QR code
     * @param string|null $unique_identifier
     * @param string $phone_number
     * @return WhatsAppLogin
     */
    public function LoginWithPhoneNumber(?string $unique_identifier = null, string $phone_number)
    {
        // Retrieve driver instance
        $driver = $this->driver;

        // Unique identifier
        $unique_identifier = $unique_identifier ?? uniqid();

        // Go to URL
        $driver->get('https://web.whatsapp.com/');

        // Wait until link with phone number is visible or chat button is visible
        $driver->wait()->until(
            function () use ($driver) {

                $element1 = $driver->findElements(WebDriverBy::cssSelector('[data-testid="link-device-qrcode-alt-linking-hint"]'));
                $element2 = $driver->findElements(WebDriverBy::cssSelector('[data-testid="chat"]'));

                return (!$element1 && !$element2)? false : true;
            }
        );

        try {

            // Find link with phone number
            $driver->findElement(WebDriverBy::cssSelector('[data-testid="link-device-qrcode-alt-linking-hint"]'))->click();

            // Wait until link with phone number input is visible
            $driver->wait()->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('[data-testid="link-device-phone-number-input"]'))
            );

            // Click on link with phone number input
            $driver->findElement(WebDriverBy::cssSelector('[data-testid="link-device-phone-number-input"]'))->click();

            // Clear on link with phone number input
            for ($i = 0; $i < 6; $i++) {
                $driver->findElement(WebDriverBy::cssSelector('[data-testid="link-device-phone-number-input"]'))->sendKeys(WebDriverKeys::BACKSPACE);
            }

            // Clear on link with phone number input
            $driver->findElement(WebDriverBy::cssSelector('[data-testid="link-device-phone-number-input"]'))->sendKeys($phone_number);

            // Find the next button
            $driver->findElement(WebDriverBy::cssSelector('[data-testid="link-device-phone-number-entry-next-button"]'))->click();

            // Wait until code is visible
            $driver->wait()->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('[data-testid="link-with-phone-number-code-cells"]'))
            );

            // Find the code
            $code = $driver->findElement(WebDriverBy::cssSelector('[data-testid="link-with-phone-number-code-cells"]'));
            $spanElements = $code->findElements(WebDriverBy::cssSelector('span'));

            // Initialize an array to store the values inside the <span> elements
            $valuesInsideSpans = [];

            // Loop through each <span> element and get its text content
            foreach ($spanElements as $spanElement) {
                $valuesInsideSpans[] = $spanElement->getText();
            }

            $this->login_with_phone_number_code = implode(" ", $valuesInsideSpans);

        } catch (\Throwable $th) {

            // Find QR code
            $driver->findElement(WebDriverBy::cssSelector('[data-testid="chat"]'));
        }

        // Store driver instance
        $this->driver = $driver;

        // Return class instance
        return $this;
    }

    /**
     * Send a message to a whatsapp contacts
     * @return void
     * @return WhatsAppLogin
     */
    public function sendMessageToContacts()
    {
        // Retrieve driver instance
        $driver = $this->driver;

        // Wait until new chat is visible
        $driver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('[data-testid="chat"]'))
        );

        foreach ($this->recipients as $key => $recipient) {

            // Open a new tab
            $driver->newWindow('tab');

            // Switch to the new tab
            $driver->switchTo()->window($driver->getWindowHandles()[$key+1]);

            // Navigate to a URL in the new tab
            $driver->get('https://web.whatsapp.com/send/?phone='.$recipient.'&amp;text&amp;type=phone_number&amp;app_absent=0');

            // Wait until message input is visible
            $driver->wait()->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('[data-testid="conversation-compose-box-input"]'))
            );

            switch ($this->message->type) {
                case 'TEXT':

                    // Find and click on new chat
                    $driver->findElement(WebDriverBy::cssSelector('[data-testid="conversation-compose-box-input"]'))->click();

                    // Find the input field where you want to paste the text
                    $inputField = $driver->findElement(WebDriverBy::cssSelector('[data-testid="conversation-compose-box-input"]'));

                    // Clear the input field in case it already has some text
                    $inputField->clear();

                    // Use sendKeys() to paste the provided text into the input field
                    $inputField->sendKeys($this->message->text);

                    // Find and click on new chat
                    $driver->findElement(WebDriverBy::cssSelector('[data-testid="send"]'))->click();

                    // Wait for message to be sent
                    $driver->wait(6);

                    break;

                default:
                    break;
            }
        }

        // Store driver instance
        $this->driver = $driver;

        // Return class instance
        return $this;
    }

    /**
     * Send a message to a whatsapp contacts
     * @return WhatsAppLogin
     */
    public function sendMessageToWhatsAppContacts()
    {
        // Retrieve driver instance
        $driver = $this->driver;

        // Wait until new chat is visible
        $driver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('[data-testid="chat"]'))
        );

        // Find and click on new chat
        $driver->findElement(WebDriverBy::cssSelector('[data-testid="chat"]'))->click();

        // Wait until new chat is visible
        $driver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('[data-testid="contact-list-key"]'))
        );

        // Find user contact list
        $contact_list = $driver->findElement(WebDriverBy::cssSelector('[data-testid="contact-list-key"]'));

        // Click on the scrollable element to ensure it has focus
        $contact_list->click();

        // Simulate pressing the "End" key to scroll to the bottom
        $driver->getKeyboard()->sendKeys(WebDriverKeys::END);

        // Wait for 9 secs for the scroll position reaches the bottom
        $driver->wait(9);

        // Find all user contacts
        // $contacts = $contact_list->findElements(WebDriverBy::cssSelector('[data-testid^="list-item-"]'));
        $contacts = $driver->executeScript('return document.querySelectorAll("[data-testid^=\'list-item-\']");');

        // Iterate over the contacts and do something
        foreach ($contacts as $contact) {

            try {

                $contactElement = $driver->getWrappedElement($contact);

                // Find the specific element containing the desired text
                $cellFrameTitle = $contactElement->findElement(WebDriverBy::cssSelector('[data-testid="cell-frame-title"]'));

                // Get the text from the element
                $text = $cellFrameTitle->getText();

                // Output the text
                echo $text . "\n";

            } catch (\Throwable $th) {

                echo 'error' .$th->getMessage(). "\n";

            }
        }

        // Store driver instance
        $this->driver = $driver;

        // Return class instance
        return $this;
    }
}

