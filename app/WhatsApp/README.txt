(new App\Whatsapp\WhatsAppLogin())->setRecipients(['234XXXXXXXX','234XXXXXXXX'])->setTextMessage('Good Morning')->LoginWithQRCode()->sendMessageToContacts();


(new App\Whatsapp\WhatsAppLogin())->continueBrowserSession('1df848efa06fa0b148de57cecd16e326')->setRecipients(['234XXXXXXXX'])->setTextMessage('Good Morning')->LoginWithQRCode()->sendMessageToContacts();


(new App\Whatsapp\WhatsAppLogin())->openBrowserSession()->setRecipients(['2347030195298','2348056169548'])->setTextMessage('Good Morning')->LoginWithQRCode()->sendMessageToContacts();
