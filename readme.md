# MailboxValidator Magento 2 Extension

MailboxValidator Magento 2 extension enables user to easily validate if an email address is valid, a type of disposable email, free email or role-based email.

This extension can be useful in many types of projects, for example

- to clean your mailing list prior to email sending
- to perform fraud check
- and so on

## Dependencies

An API key is required for this module to function.

Go to https://www.mailboxvalidator.com/plans#api to sign up for FREE API plan and you'll be given an API key.

This extension require Magento 2.4 to work. Please make sure you had updated your Magento to 2.4 before install this extension.

## Installation

To install this extension, you will need to:

1. Download the latest release from GitHub repo.
2. Create a folder and name as Hexasoft.
3. Unzip the file downloaded, rename it to MailboxValidator and transfer it into Hexasoft folder.
4. Upload the Hexasoft folder to the subdirectory of Magento installation root directory as: magento2/app/code/
5. Login to the Magento admin page and disable the cache under the System -> Cache Management page.
6. Go to our terminal, change the path to Magento root directory, and execute the following command: 
```bash
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

You can also read the **User Guides** in documentation section from here: https://marketplace.magento.com/hexasoft-module-mailboxvalidator.html

## Copyright

Copyright(C) 2024 by MailboxValidator.com.
